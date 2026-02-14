<?php

declare(strict_types=1);

require_once __DIR__ . '/Model.php';

/**
 * Course Model
 * Handles all course-related operations including prerequisites and lecturer assignments
 */
class Course extends Model
{
    protected string $table = 'courses';

    /**
     * Find a course by its code
     */
    public function findByCode(string $code): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE code = :code LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all courses with full details (uses view)
     */
    public function getAllCoursesWithDetails(array $filters = []): array
    {
        $sql = "SELECT * FROM v_courses_full WHERE 1=1";
        $params = [];

        if (!empty($filters['department_id'])) {
            $sql .= " AND department_id = :department_id";
            $params['department_id'] = $filters['department_id'];
        }

        if (!empty($filters['level_id'])) {
            $sql .= " AND level_id = :level_id";
            $params['level_id'] = $filters['level_id'];
        }

        if (!empty($filters['semester_number'])) {
            $sql .= " AND semester_number = :semester_number";
            $params['semester_number'] = $filters['semester_number'];
        }

        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }

        if (isset($filters['is_elective'])) {
            $sql .= " AND is_elective = :is_elective";
            $params['is_elective'] = $filters['is_elective'];
        }

        $sql .= " ORDER BY level_number, semester_number, code";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses by department
     */
    public function getCoursesByDepartment(int $departmentId, bool $activeOnly = true): array
    {
        $sql = "SELECT c.*, l.name AS level_name, l.level_number 
                FROM {$this->table} c
                INNER JOIN levels l ON c.level_id = l.id
                WHERE c.department_id = :department_id";
        
        if ($activeOnly) {
            $sql .= " AND c.is_active = 1";
        }
        
        $sql .= " ORDER BY l.level_number, c.semester_number, c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['department_id' => $departmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses by level
     */
    public function getCoursesByLevel(int $levelId, ?int $departmentId = null, bool $activeOnly = true): array
    {
        $sql = "SELECT c.*, d.name AS department_name, d.code AS department_code 
                FROM {$this->table} c
                INNER JOIN departments d ON c.department_id = d.id
                WHERE c.level_id = :level_id";
        
        $params = ['level_id' => $levelId];

        if ($departmentId !== null) {
            $sql .= " AND c.department_id = :department_id";
            $params['department_id'] = $departmentId;
        }
        
        if ($activeOnly) {
            $sql .= " AND c.is_active = 1";
        }
        
        $sql .= " ORDER BY c.semester_number, c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses for a specific semester
     */
    public function getCoursesForSemester(int $departmentId, int $levelId, int $semesterNumber, bool $activeOnly = true): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE department_id = :department_id 
                AND level_id = :level_id 
                AND semester_number = :semester_number";
        
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        
        $sql .= " ORDER BY is_elective, code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'department_id' => $departmentId,
            'level_id' => $levelId,
            'semester_number' => $semesterNumber
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get course with full details including prerequisites and lecturers
     */
    public function getCourseWithDetails(int $id): ?array
    {
        $sql = "SELECT * FROM v_courses_full WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            return null;
        }

        // Get prerequisites
        $course['prerequisites'] = $this->getPrerequisites($id);
        
        // Get assigned lecturers for current session
        $course['lecturers'] = $this->getCourseLecturers($id);

        return $course;
    }

    /**
     * Get prerequisites for a course
     */
    public function getPrerequisites(int $courseId): array
    {
        $sql = "SELECT c.id, c.code, c.title, c.credit_units 
                FROM course_prerequisites cp
                INNER JOIN courses c ON cp.prerequisite_course_id = c.id
                WHERE cp.course_id = :course_id
                ORDER BY c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses that require this course as prerequisite
     */
    public function getDependentCourses(int $courseId): array
    {
        $sql = "SELECT c.id, c.code, c.title, c.credit_units 
                FROM course_prerequisites cp
                INNER JOIN courses c ON cp.course_id = c.id
                WHERE cp.prerequisite_course_id = :course_id
                ORDER BY c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add a prerequisite to a course
     */
    public function addPrerequisite(int $courseId, int $prerequisiteCourseId): bool
    {
        // Check if it would create a circular dependency
        if ($this->wouldCreateCircularDependency($courseId, $prerequisiteCourseId)) {
            return false;
        }

        $sql = "INSERT INTO course_prerequisites (course_id, prerequisite_course_id) 
                VALUES (:course_id, :prerequisite_course_id)
                ON DUPLICATE KEY UPDATE course_id = course_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'course_id' => $courseId,
            'prerequisite_course_id' => $prerequisiteCourseId
        ]);
    }

    /**
     * Remove a prerequisite from a course
     */
    public function removePrerequisite(int $courseId, int $prerequisiteCourseId): bool
    {
        $sql = "DELETE FROM course_prerequisites 
                WHERE course_id = :course_id 
                AND prerequisite_course_id = :prerequisite_course_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'course_id' => $courseId,
            'prerequisite_course_id' => $prerequisiteCourseId
        ]);
    }

    /**
     * Check if adding a prerequisite would create a circular dependency
     */
    private function wouldCreateCircularDependency(int $courseId, int $prerequisiteCourseId): bool
    {
        // If the prerequisite course has the target course as its prerequisite (directly or indirectly)
        $checked = [];
        return $this->hasPrerequisite($prerequisiteCourseId, $courseId, $checked);
    }

    /**
     * Recursive function to check if a course has another as prerequisite
     */
    private function hasPrerequisite(int $courseId, int $targetPrerequisiteId, array &$checked): bool
    {
        if (in_array($courseId, $checked)) {
            return false; // Already checked this course
        }
        
        $checked[] = $courseId;

        $sql = "SELECT prerequisite_course_id FROM course_prerequisites WHERE course_id = :course_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['course_id' => $courseId]);
        $prerequisites = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($prerequisites as $prereqId) {
            if ($prereqId == $targetPrerequisiteId) {
                return true;
            }
            if ($this->hasPrerequisite($prereqId, $targetPrerequisiteId, $checked)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get lecturers assigned to a course
     */
    public function getCourseLecturers(int $courseId, ?int $sessionId = null): array
    {
        $sql = "SELECT cl.*, 
                       CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name,
                       u.email,
                       s.name AS session_name
                FROM course_lecturers cl
                INNER JOIN users u ON cl.lecturer_id = u.id
                INNER JOIN sessions s ON cl.session_id = s.id
                WHERE cl.course_id = :course_id";
        
        $params = ['course_id' => $courseId];

        if ($sessionId !== null) {
            $sql .= " AND cl.session_id = :session_id";
            $params['session_id'] = $sessionId;
        } else {
            // Get current session
            $sql .= " AND s.is_current = 1";
        }
        
        $sql .= " ORDER BY cl.is_coordinator DESC, u.last_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Assign a lecturer to a course
     */
    public function assignLecturer(int $courseId, int $lecturerId, int $sessionId, bool $isCoordinator = false): bool
    {
        $sql = "INSERT INTO course_lecturers (course_id, lecturer_id, session_id, is_coordinator) 
                VALUES (:course_id, :lecturer_id, :session_id, :is_coordinator)
                ON DUPLICATE KEY UPDATE is_coordinator = :is_coordinator";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'course_id' => $courseId,
            'lecturer_id' => $lecturerId,
            'session_id' => $sessionId,
            'is_coordinator' => $isCoordinator ? 1 : 0
        ]);
    }

    /**
     * Remove a lecturer from a course
     */
    public function removeLecturer(int $courseId, int $lecturerId, int $sessionId): bool
    {
        $sql = "DELETE FROM course_lecturers 
                WHERE course_id = :course_id 
                AND lecturer_id = :lecturer_id 
                AND session_id = :session_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'course_id' => $courseId,
            'lecturer_id' => $lecturerId,
            'session_id' => $sessionId
        ]);
    }

    /**
     * Get courses assigned to a lecturer
     */
    public function getCoursesByLecturer(int $lecturerId, ?int $sessionId = null): array
    {
        $sql = "SELECT c.*, cl.is_coordinator, s.name AS session_name,
                       d.name AS department_name, l.name AS level_name
                FROM course_lecturers cl
                INNER JOIN courses c ON cl.course_id = c.id
                INNER JOIN sessions s ON cl.session_id = s.id
                INNER JOIN departments d ON c.department_id = d.id
                INNER JOIN levels l ON c.level_id = l.id
                WHERE cl.lecturer_id = :lecturer_id";
        
        $params = ['lecturer_id' => $lecturerId];

        if ($sessionId !== null) {
            $sql .= " AND cl.session_id = :session_id";
            $params['session_id'] = $sessionId;
        } else {
            $sql .= " AND s.is_current = 1";
        }
        
        $sql .= " ORDER BY l.level_number, c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get course statistics
     */
    public function getCourseStats(int $courseId, ?int $semesterId = null): array
    {
        $params = ['course_id' => $courseId];
        
        // Get registration count
        $sql = "SELECT COUNT(*) AS total_registrations,
                       SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_registrations,
                       SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_registrations,
                       SUM(CASE WHEN dropped = 1 THEN 1 ELSE 0 END) AS dropped_registrations
                FROM course_registrations 
                WHERE course_id = :course_id";
        
        if ($semesterId !== null) {
            $sql .= " AND semester_id = :semester_id";
            $params['semester_id'] = $semesterId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get result statistics if available
        $sql = "SELECT 
                    COUNT(*) AS graded_count,
                    AVG(total_score) AS avg_score,
                    SUM(CASE WHEN grade IN ('A', 'B', 'C', 'D', 'E') THEN 1 ELSE 0 END) AS pass_count,
                    SUM(CASE WHEN grade = 'F' THEN 1 ELSE 0 END) AS fail_count
                FROM course_results 
                WHERE course_id = :course_id 
                AND status = 'approved'";
        
        if ($semesterId !== null) {
            $sql .= " AND semester_id = :semester_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $resultStats = $stmt->fetch(PDO::FETCH_ASSOC);

        return array_merge($stats, $resultStats);
    }

    /**
     * Search courses by code or title
     */
    public function search(string $query, array $filters = []): array
    {
        $sql = "SELECT * FROM v_courses_full 
                WHERE (code LIKE :query OR title LIKE :query)";
        
        $params = ['query' => "%{$query}%"];

        if (!empty($filters['department_id'])) {
            $sql .= " AND department_id = :department_id";
            $params['department_id'] = $filters['department_id'];
        }

        if (!empty($filters['level_id'])) {
            $sql .= " AND level_id = :level_id";
            $params['level_id'] = $filters['level_id'];
        }

        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params['is_active'] = $filters['is_active'];
        }
        
        $sql .= " ORDER BY code LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a course code already exists
     */
    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE code = :code";
        $params = ['code' => $code];

        if ($excludeId !== null) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Toggle course active status
     */
    public function toggleActive(int $id): bool
    {
        $sql = "UPDATE {$this->table} SET is_active = !is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get total credit units for a set of courses
     */
    public function getTotalCreditUnits(array $courseIds): int
    {
        if (empty($courseIds)) {
            return 0;
        }

        $placeholders = str_repeat('?,', count($courseIds) - 1) . '?';
        $sql = "SELECT SUM(credit_units) FROM {$this->table} WHERE id IN ({$placeholders})";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($courseIds);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get course count by department
     */
    public function getCountByDepartment(int $departmentId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE department_id = :department_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['department_id' => $departmentId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get course count by level
     */
    public function getCountByLevel(int $levelId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE level_id = :level_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['level_id' => $levelId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get active courses only
     */
    public function getActiveCourses(?int $departmentId = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1";
        $params = [];

        if ($departmentId !== null) {
            $sql .= " AND department_id = :department_id";
            $params['department_id'] = $departmentId;
        }

        $sql .= " ORDER BY code";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
