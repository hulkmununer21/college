<?php

declare(strict_types=1);

require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/Course.php';
require_once __DIR__ . '/Semester.php';
require_once __DIR__ . '/Level.php';

/**
 * CourseRegistration Model
 * Handles student course registration, validation, and management
 */
class CourseRegistration extends Model
{
    protected string $table = 'course_registrations';

    /**
     * Register a student for a course
     */
    public function registerCourse(int $studentId, int $courseId, int $semesterId): array
    {
        // Validate registration
        $validation = $this->validateRegistration($studentId, $courseId, $semesterId);
        
        if (!$validation['success']) {
            return $validation;
        }

        // Insert registration
        $sql = "INSERT INTO {$this->table} 
                (student_id, course_id, semester_id, status) 
                VALUES (:student_id, :course_id, :semester_id, 'pending')";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'semester_id' => $semesterId
        ]);

        if ($success) {
            return [
                'success' => true,
                'message' => 'Course registered successfully. Awaiting approval.'
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to register course. Please try again.'
        ];
    }

    /**
     * Validate if a student can register for a course
     */
    public function validateRegistration(int $studentId, int $courseId, int $semesterId): array
    {
        // Check if already registered
        if ($this->isAlreadyRegistered($studentId, $courseId, $semesterId)) {
            return [
                'success' => false,
                'message' => 'You are already registered for this course.'
            ];
        }

        // Check if registration is open
        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);
        
        if (!$semester || !$semesterModel->isRegistrationOpen($semesterId)) {
            return [
                'success' => false,
                'message' => 'Course registration is not open for this semester.'
            ];
        }

        // Check prerequisites
        $prerequisitesCheck = $this->checkPrerequisites($studentId, $courseId);
        if (!$prerequisitesCheck['success']) {
            return $prerequisitesCheck;
        }

        // Check credit unit limits
        $creditCheck = $this->validateCreditUnits($studentId, $courseId, $semesterId);
        if (!$creditCheck['success']) {
            return $creditCheck;
        }

        return ['success' => true];
    }

    /**
     * Check if student is already registered for a course in a semester
     */
    public function isAlreadyRegistered(int $studentId, int $courseId, int $semesterId): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE student_id = :student_id 
                AND course_id = :course_id 
                AND semester_id = :semester_id
                AND dropped = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'semester_id' => $semesterId
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if student has passed all prerequisites for a course
     */
    public function checkPrerequisites(int $studentId, int $courseId): array
    {
        $courseModel = new Course();
        $prerequisites = $courseModel->getPrerequisites($courseId);

        if (empty($prerequisites)) {
            return ['success' => true];
        }

        $failedPrerequisites = [];

        foreach ($prerequisites as $prereq) {
            if (!$this->hasPassedCourse($studentId, $prereq['id'])) {
                $failedPrerequisites[] = $prereq['code'] . ' - ' . $prereq['title'];
            }
        }

        if (!empty($failedPrerequisites)) {
            return [
                'success' => false,
                'message' => 'You must pass the following prerequisite(s) first: ' . implode(', ', $failedPrerequisites)
            ];
        }

        return ['success' => true];
    }

    /**
     * Check if student has passed a specific course
     */
    private function hasPassedCourse(int $studentId, int $courseId): bool
    {
        $sql = "SELECT COUNT(*) FROM course_results 
                WHERE student_id = :student_id 
                AND course_id = :course_id
                AND grade IN ('A', 'B', 'C', 'D', 'E')
                AND status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'course_id' => $courseId
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Validate credit units for registration
     */
    public function validateCreditUnits(int $studentId, int $courseId, int $semesterId): array
    {
        // Get student's level
        $sql = "SELECT level_id FROM users WHERE id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        $levelId = $stmt->fetchColumn();

        if (!$levelId) {
            return [
                'success' => false,
                'message' => 'Student level not found.'
            ];
        }

        // Get current total credit units for this semester
        $currentCredits = $this->getTotalCreditUnits($studentId, $semesterId);

        // Get credit units for the course being registered
        $courseModel = new Course();
        $course = $courseModel->find($courseId);
        $newCourseCredits = $course['credit_units'] ?? 0;

        // Get level credit limits
        $levelModel = new Level();
        $creditRange = $levelModel->getCreditUnitRange($levelId);

        $totalAfterRegistration = $currentCredits + $newCourseCredits;

        if ($totalAfterRegistration > $creditRange['max_credits']) {
            return [
                'success' => false,
                'message' => "Registering this course would exceed the maximum credit limit ({$creditRange['max_credits']} units). Current: {$currentCredits} units, Course: {$newCourseCredits} units."
            ];
        }

        return ['success' => true];
    }

    /**
     * Get total credit units for a student in a semester
     */
    public function getTotalCreditUnits(int $studentId, int $semesterId, bool $approvedOnly = false): int
    {
        $sql = "SELECT COALESCE(SUM(c.credit_units), 0) 
                FROM {$this->table} cr
                INNER JOIN courses c ON cr.course_id = c.id
                WHERE cr.student_id = :student_id 
                AND cr.semester_id = :semester_id
                AND cr.dropped = 0";
        
        if ($approvedOnly) {
            $sql .= " AND cr.status = 'approved'";
        } else {
            $sql .= " AND cr.status IN ('pending', 'approved')";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'semester_id' => $semesterId
        ]);
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all registrations for a student
     */
    public function getStudentRegistrations(int $studentId, ?int $semesterId = null, bool $includeDropped = false): array
    {
        $sql = "SELECT * FROM v_student_registrations WHERE student_id = :student_id";
        $params = ['student_id' => $studentId];

        if ($semesterId !== null) {
            $sql .= " AND semester_id = :semester_id";
            $params['semester_id'] = $semesterId;
        }

        if (!$includeDropped) {
            $sql .= " AND dropped = 0";
        }

        $sql .= " ORDER BY registration_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get available courses for student registration
     */
    public function getAvailableCoursesForStudent(int $studentId, int $semesterId): array
    {
        // Get student details
        $sql = "SELECT department_id, level_id FROM users WHERE id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            return [];
        }

        // Get semester details
        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);

        if (!$semester) {
            return [];
        }

        // Get courses for the student's department, level, and semester number
        $courseModel = new Course();
        $courses = $courseModel->getCoursesForSemester(
            (int) $student['department_id'],
            (int) $student['level_id'],
            (int) $semester['semester_number'],
            true
        );

        // Add registration status and prerequisite info
        foreach ($courses as &$course) {
            $course['is_registered'] = $this->isAlreadyRegistered($studentId, (int) $course['id'], $semesterId);
            $course['prerequisites'] = $courseModel->getPrerequisites((int) $course['id']);
            $course['prerequisites_met'] = $this->checkPrerequisites($studentId, (int) $course['id'])['success'];
        }

        return $courses;
    }

    /**
     * Approve a course registration
     */
    public function approveCourse(int $registrationId, int $approvedBy): bool
    {
        $sql = "UPDATE {$this->table} 
                SET status = 'approved', 
                    approved_by = :approved_by, 
                    approved_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $registrationId,
            'approved_by' => $approvedBy
        ]);
    }

    /**
     * Reject a course registration
     */
    public function rejectCourse(int $registrationId): bool
    {
        $sql = "UPDATE {$this->table} SET status = 'rejected' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $registrationId]);
    }

    /**
     * Drop a course
     */
    public function dropCourse(int $registrationId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET dropped = 1, dropped_at = NOW() 
                WHERE id = :id 
                AND status != 'rejected'";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $registrationId]);
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(array $registrationIds, int $approvedBy): int
    {
        if (empty($registrationIds)) {
            return 0;
        }

        $placeholders = str_repeat('?,', count($registrationIds) - 1) . '?';
        $sql = "UPDATE {$this->table} 
                SET status = 'approved', 
                    approved_by = ?, 
                    approved_at = NOW() 
                WHERE id IN ({$placeholders})
                AND status = 'pending'";
        
        $params = array_merge([$approvedBy], $registrationIds);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }

    /**
     * Get pending registrations for approval
     */
    public function getPendingRegistrations(?int $departmentId = null, ?int $semesterId = null): array
    {
        $sql = "SELECT * FROM v_student_registrations WHERE status = 'pending'";
        $params = [];

        if ($departmentId !== null) {
            $sql .= " AND department_id = :department_id";
            $params['department_id'] = $departmentId;
        }

        if ($semesterId !== null) {
            $sql .= " AND semester_id = :semester_id";
            $params['semester_id'] = $semesterId;
        }

        $sql .= " ORDER BY registration_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get registration statistics for a semester
     */
    public function getRegistrationStats(int $semesterId, ?int $departmentId = null): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_registrations,
                    COUNT(DISTINCT student_id) AS unique_students,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    SUM(CASE WHEN dropped = 1 THEN 1 ELSE 0 END) AS dropped_count
                FROM {$this->table} cr";
        
        $params = ['semester_id' => $semesterId];

        if ($departmentId !== null) {
            $sql .= " INNER JOIN users u ON cr.student_id = u.id";
            $sql .= " WHERE cr.semester_id = :semester_id AND u.department_id = :department_id";
            $params['department_id'] = $departmentId;
        } else {
            $sql .= " WHERE semester_id = :semester_id";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get students registered for a course
     */
    public function getStudentsByCourse(int $courseId, int $semesterId, ?string $status = null): array
    {
        $sql = "SELECT 
                    u.id,
                    u.matric_number,
                    CONCAT(u.first_name, ' ', u.last_name) AS full_name,
                    u.email,
                    cr.status,
                    cr.registration_date,
                    cr.dropped,
                    d.name AS department_name,
                    l.name AS level_name
                FROM {$this->table} cr
                INNER JOIN users u ON cr.student_id = u.id
                INNER JOIN departments d ON u.department_id = d.id
                INNER JOIN levels l ON u.level_id = l.id
                WHERE cr.course_id = :course_id 
                AND cr.semester_id = :semester_id";
        
        $params = [
            'course_id' => $courseId,
            'semester_id' => $semesterId
        ];

        if ($status !== null) {
            $sql .= " AND cr.status = :status";
            $params['status'] = $status;
        }

        $sql .= " AND cr.dropped = 0 ORDER BY u.matric_number";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if a student meets minimum credit requirements
     */
    public function hasMinimumCredits(int $studentId, int $semesterId): array
    {
        // Get student's level
        $sql = "SELECT level_id FROM users WHERE id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['student_id' => $studentId]);
        $levelId = $stmt->fetchColumn();

        if (!$levelId) {
            return ['success' => false, 'message' => 'Student level not found.'];
        }

        // Get level credit limits
        $levelModel = new Level();
        $creditRange = $levelModel->getCreditUnitRange($levelId);

        // Get current total credit units
        $currentCredits = $this->getTotalCreditUnits($studentId, $semesterId, true);

        if ($currentCredits < $creditRange['min_credits']) {
            return [
                'success' => false,
                'message' => "You have registered for {$currentCredits} credit units. Minimum required: {$creditRange['min_credits']} units.",
                'current' => $currentCredits,
                'minimum' => $creditRange['min_credits']
            ];
        }

        return [
            'success' => true,
            'current' => $currentCredits,
            'minimum' => $creditRange['min_credits'],
            'maximum' => $creditRange['max_credits']
        ];
    }

    /**
     * Get registration summary for a student in a semester
     */
    public function getRegistrationSummary(int $studentId, int $semesterId): array
    {
        $registrations = $this->getStudentRegistrations($studentId, $semesterId, false);
        
        $summary = [
            'total_courses' => count($registrations),
            'approved_courses' => 0,
            'pending_courses' => 0,
            'rejected_courses' => 0,
            'total_credits' => 0,
            'approved_credits' => 0,
            'courses' => $registrations
        ];

        foreach ($registrations as $reg) {
            $summary['total_credits'] += $reg['credit_units'];
            
            switch ($reg['status']) {
                case 'approved':
                    $summary['approved_courses']++;
                    $summary['approved_credits'] += $reg['credit_units'];
                    break;
                case 'pending':
                    $summary['pending_courses']++;
                    break;
                case 'rejected':
                    $summary['rejected_courses']++;
                    break;
            }
        }

        // Check minimum credit requirement
        $creditCheck = $this->hasMinimumCredits($studentId, $semesterId);
        $summary['meets_minimum'] = $creditCheck['success'];
        $summary['credit_check'] = $creditCheck;

        return $summary;
    }

    /**
     * Delete a registration (for admin/system use only)
     */
    public function deleteRegistration(int $registrationId): bool
    {
        return $this->delete($registrationId);
    }
}
