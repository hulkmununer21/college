<?php
declare(strict_types=1);

require_once __DIR__ . '/Model.php';

/**
 * Grade Model
 * Handles grade entry, GPA/CGPA computation, and result management
 */
class Grade extends Model
{
    protected string $table = 'course_results';

    // Grading scale (5.0 system)
    private const GRADING_SCALE = [
        'A' => ['min' => 70, 'max' => 100, 'point' => 5.0],
        'B' => ['min' => 60, 'max' => 69, 'point' => 4.0],
        'C' => ['min' => 50, 'max' => 59, 'point' => 3.0],
        'D' => ['min' => 45, 'max' => 49, 'point' => 2.0],
        'E' => ['min' => 40, 'max' => 44, 'point' => 1.0],
        'F' => ['min' => 0, 'max' => 39, 'point' => 0.0],
    ];

    // =====================================================
    // GRADE ENTRY & MANAGEMENT
    // =====================================================

    /**
     * Enter or update grade for a student
     */
    public function enterGrade(array $data): array
    {
        try {
            $registrationId = (int)$data['registration_id'];
            $studentId = (int)$data['student_id'];
            $courseId = (int)$data['course_id'];
            $semesterId = (int)$data['semester_id'];
            $caScore = (float)$data['ca_score'];
            $examScore = (float)$data['exam_score'];
            $enteredBy = (int)$data['entered_by'];

            // Validate scores
            if ($caScore < 0 || $caScore > 40) {
                return ['success' => false, 'message' => 'CA score must be between 0 and 40'];
            }
            if ($examScore < 0 || $examScore > 60) {
                return ['success' => false, 'message' => 'Exam score must be between 0 and 60'];
            }

            // Calculate grade
            $totalScore = $caScore + $examScore;
            $gradeInfo = $this->calculateGrade($totalScore);

            // Check if grade already exists
            $existing = $this->getGrade($registrationId);

            if ($existing) {
                // Update existing grade
                $sql = "UPDATE {$this->table} SET
                        ca_score = :ca_score,
                        exam_score = :exam_score,
                        grade = :grade,
                        grade_point = :grade_point,
                        status = 'draft',
                        entered_by = :entered_by,
                        entered_at = NOW(),
                        updated_at = NOW()
                        WHERE registration_id = :registration_id";
            } else {
                // Insert new grade
                $sql = "INSERT INTO {$this->table} 
                        (registration_id, student_id, course_id, semester_id, 
                         ca_score, exam_score, grade, grade_point, status, entered_by, entered_at)
                        VALUES 
                        (:registration_id, :student_id, :course_id, :semester_id, 
                         :ca_score, :exam_score, :grade, :grade_point, 'draft', :entered_by, NOW())";
            }

            $stmt = $this->db->prepare($sql);
            $params = [
                'registration_id' => $registrationId,
                'student_id' => $studentId,
                'course_id' => $courseId,
                'semester_id' => $semesterId,
                'ca_score' => $caScore,
                'exam_score' => $examScore,
                'grade' => $gradeInfo['letter'],
                'grade_point' => $gradeInfo['point'],
                'entered_by' => $enteredBy
            ];

            if ($stmt->execute($params)) {
                return [
                    'success' => true,
                    'message' => 'Grade saved successfully',
                    'grade' => $gradeInfo
                ];
            }

            return ['success' => false, 'message' => 'Failed to save grade'];
        } catch (PDOException $e) {
            error_log("Error entering grade: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    /**
     * Calculate letter grade and grade point from total score
     */
    public function calculateGrade(float $totalScore): array
    {
        foreach (self::GRADING_SCALE as $letter => $range) {
            if ($totalScore >= $range['min'] && $totalScore <= $range['max']) {
                return [
                    'letter' => $letter,
                    'point' => $range['point'],
                    'total' => $totalScore
                ];
            }
        }
        return ['letter' => 'F', 'point' => 0.0, 'total' => $totalScore];
    }

    /**
     * Get grade for a specific registration
     */
    public function getGrade(int $registrationId): ?array
    {
        $sql = "SELECT g.*, 
                c.code AS course_code, 
                c.title AS course_title,
                c.credit_units,
                CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                u.matric_number
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                INNER JOIN users u ON g.student_id = u.id
                WHERE g.registration_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$registrationId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all grades for a course in a semester
     */
    public function getGradesByCourse(int $courseId, int $semesterId): array
    {
        $sql = "SELECT g.*,
                CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                u.matric_number,
                cr.status AS registration_status
                FROM course_registrations cr
                LEFT JOIN {$this->table} g ON cr.id = g.registration_id
                INNER JOIN users u ON cr.student_id = u.id
                WHERE cr.course_id = :course_id 
                AND cr.semester_id = :semester_id
                AND cr.status = 'approved'
                ORDER BY u.matric_number";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'semester_id' => $semesterId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Bulk enter grades for multiple students
     */
    public function bulkEnterGrades(array $grades, int $enteredBy): array
    {
        $successCount = 0;
        $errors = [];

        $this->db->beginTransaction();

        try {
            foreach ($grades as $gradeData) {
                $gradeData['entered_by'] = $enteredBy;
                $result = $this->enterGrade($gradeData);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errors[] = $result['message'];
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'count' => $successCount,
                'errors' => $errors
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Bulk grade entry error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save grades',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    // =====================================================
    // RESULT APPROVAL WORKFLOW
    // =====================================================

    /**
     * Submit grades for approval
     */
    public function submitGrades(int $courseId, int $semesterId, int $lecturerId): bool
    {
        try {
            $sql = "UPDATE {$this->table} g
                    INNER JOIN course_registrations cr ON g.registration_id = cr.id
                    SET g.status = 'submitted',
                        g.submitted_by = :lecturer_id,
                        g.submitted_at = NOW()
                    WHERE cr.course_id = :course_id 
                    AND cr.semester_id = :semester_id
                    AND g.status = 'draft'";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'course_id' => $courseId,
                'semester_id' => $semesterId,
                'lecturer_id' => $lecturerId
            ]);
        } catch (PDOException $e) {
            error_log("Error submitting grades: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve grades (HOD/Admin)
     */
    public function approveGrades(array $gradeIds, int $approvedBy): bool
    {
        try {
            if (empty($gradeIds)) {
                return false;
            }

            $placeholders = str_repeat('?,', count($gradeIds) - 1) . '?';
            $sql = "UPDATE {$this->table} 
                    SET status = 'approved',
                        approved_by = ?,
                        approved_at = NOW()
                    WHERE id IN ($placeholders)
                    AND status = 'submitted'";
            
            $stmt = $this->db->prepare($sql);
            $params = array_merge([$approvedBy], $gradeIds);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error approving grades: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get pending grades for approval
     */
    public function getPendingGrades(?int $departmentId = null): array
    {
        $sql = "SELECT g.*,
                c.code AS course_code,
                c.title AS course_title,
                d.name AS department_name,
                sem.name AS semester_name,
                sess.name AS session_name,
                CONCAT(lecturer.first_name, ' ', lecturer.last_name) AS lecturer_name,
                COUNT(*) as student_count
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                INNER JOIN departments d ON c.department_id = d.id
                INNER JOIN semesters sem ON g.semester_id = sem.id
                INNER JOIN sessions sess ON sem.session_id = sess.id
                LEFT JOIN users lecturer ON g.submitted_by = lecturer.id
                WHERE g.status = 'submitted'";
        
        $params = [];
        if ($departmentId !== null) {
            $sql .= " AND c.department_id = ?";
            $params[] = $departmentId;
        }

        $sql .= " GROUP BY g.course_id, g.semester_id
                  ORDER BY g.submitted_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // STUDENT RESULTS
    // =====================================================

    /**
     * Get all results for a student
     */
    public function getStudentResults(int $studentId, ?int $semesterId = null): array
    {
        $sql = "SELECT g.*,
                c.code AS course_code,
                c.title AS course_title,
                c.credit_units,
                sem.name AS semester_name,
                sess.name AS session_name,
                l.name AS level_name
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                INNER JOIN semesters sem ON g.semester_id = sem.id
                INNER JOIN sessions sess ON sem.session_id = sess.id
                INNER JOIN levels l ON c.level_id = l.id
                WHERE g.student_id = :student_id
                AND g.status = 'approved'";
        
        $params = ['student_id' => $studentId];

        if ($semesterId !== null) {
            $sql .= " AND g.semester_id = :semester_id";
            $params['semester_id'] = $semesterId;
        }

        $sql .= " ORDER BY sem.start_date DESC, c.code";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get student results grouped by semester
     */
    public function getStudentResultsBySemester(int $studentId): array
    {
        $sql = "SELECT 
                sem.id AS semester_id,
                sem.name AS semester_name,
                sess.name AS session_name,
                l.name AS level_name,
                COUNT(*) as total_courses,
                SUM(c.credit_units) as total_credits,
                SUM(c.credit_units * g.grade_point) as total_points,
                ROUND(SUM(c.credit_units * g.grade_point) / SUM(c.credit_units), 2) as gpa
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                INNER JOIN semesters sem ON g.semester_id = sem.id
                INNER JOIN sessions sess ON sem.session_id = sess.id
                INNER JOIN levels l ON c.level_id = l.id
                WHERE g.student_id = ?
                AND g.status = 'approved'
                AND g.grade != 'F'
                GROUP BY sem.id
                ORDER BY sem.start_date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // GPA/CGPA COMPUTATION
    // =====================================================

    /**
     * Calculate GPA for a student in a semester
     */
    public function calculateGPA(int $studentId, int $semesterId): array
    {
        $sql = "SELECT 
                COUNT(*) as total_courses,
                SUM(c.credit_units) as total_credits,
                SUM(CASE WHEN g.grade != 'F' THEN c.credit_units ELSE 0 END) as passed_credits,
                SUM(c.credit_units * g.grade_point) as total_points,
                ROUND(SUM(c.credit_units * g.grade_point) / SUM(c.credit_units), 2) as gpa
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                WHERE g.student_id = :student_id
                AND g.semester_id = :semester_id
                AND g.status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'student_id' => $studentId,
            'semester_id' => $semesterId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_courses' => (int)($result['total_courses'] ?? 0),
            'total_credits' => (int)($result['total_credits'] ?? 0),
            'passed_credits' => (int)($result['passed_credits'] ?? 0),
            'total_points' => (float)($result['total_points'] ?? 0),
            'gpa' => (float)($result['gpa'] ?? 0.0)
        ];
    }

    /**
     * Calculate CGPA for a student (all semesters)
     */
    public function calculateCGPA(int $studentId): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT g.semester_id) as total_semesters,
                COUNT(*) as total_courses,
                SUM(c.credit_units) as cumulative_credits,
                SUM(CASE WHEN g.grade != 'F' THEN c.credit_units ELSE 0 END) as passed_credits,
                SUM(c.credit_units * g.grade_point) as cumulative_points,
                ROUND(SUM(c.credit_units * g.grade_point) / SUM(c.credit_units), 2) as cgpa
                FROM {$this->table} g
                INNER JOIN courses c ON g.course_id = c.id
                WHERE g.student_id = ?
                AND g.status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_semesters' => (int)($result['total_semesters'] ?? 0),
            'total_courses' => (int)($result['total_courses'] ?? 0),
            'cumulative_credits' => (int)($result['cumulative_credits'] ?? 0),
            'passed_credits' => (int)($result['passed_credits'] ?? 0),
            'cumulative_points' => (float)($result['cumulative_points'] ?? 0),
            'cgpa' => (float)($result['cgpa'] ?? 0.0)
        ];
    }

    /**
     * Get class of degree based on CGPA
     */
    public function getClassOfDegree(float $cgpa): string
    {
        if ($cgpa >= 4.50) {
            return 'First Class Honours';
        } elseif ($cgpa >= 3.50) {
            return 'Second Class Honours (Upper Division)';
        } elseif ($cgpa >= 2.40) {
            return 'Second Class Honours (Lower Division)';
        } elseif ($cgpa >= 1.50) {
            return 'Third Class Honours';
        } elseif ($cgpa >= 1.00) {
            return 'Pass';
        } else {
            return 'Fail';
        }
    }

    // =====================================================
    // STATISTICS & REPORTS
    // =====================================================

    /**
     * Get grade distribution for a course
     */
    public function getGradeDistribution(int $courseId, int $semesterId): array
    {
        $sql = "SELECT 
                g.grade,
                COUNT(*) as count,
                ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM {$this->table} 
                    WHERE course_id = :course_id AND semester_id = :semester_id AND status = 'approved')), 2) as percentage
                FROM {$this->table} g
                WHERE g.course_id = :course_id
                AND g.semester_id = :semester_id
                AND g.status = 'approved'
                GROUP BY g.grade
                ORDER BY g.grade_point DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'semester_id' => $semesterId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get course statistics
     */
    public function getCourseStatistics(int $courseId, int $semesterId): array
    {
        $sql = "SELECT 
                COUNT(*) as total_students,
                AVG(g.total_score) as average_score,
                MAX(g.total_score) as highest_score,
                MIN(g.total_score) as lowest_score,
                AVG(g.grade_point) as average_gp,
                COUNT(CASE WHEN g.grade != 'F' THEN 1 END) as passed,
                COUNT(CASE WHEN g.grade = 'F' THEN 1 END) as failed,
                ROUND((COUNT(CASE WHEN g.grade != 'F' THEN 1 END) * 100.0 / COUNT(*)), 2) as pass_rate
                FROM {$this->table} g
                WHERE g.course_id = ?
                AND g.semester_id = ?
                AND g.status = 'approved'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId, $semesterId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get top performers in a semester
     */
    public function getTopPerformers(int $semesterId, int $limit = 10): array
    {
        $sql = "SELECT 
                u.id,
                CONCAT(u.first_name, ' ', u.last_name) AS student_name,
                u.matric_number,
                d.name AS department_name,
                SUM(c.credit_units * g.grade_point) / SUM(c.credit_units) as gpa
                FROM {$this->table} g
                INNER JOIN users u ON g.student_id = u.id
                INNER JOIN departments d ON u.department_id = d.id
                INNER JOIN courses c ON g.course_id = c.id
                WHERE g.semester_id = ?
                AND g.status = 'approved'
                GROUP BY u.id
                ORDER BY gpa DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$semesterId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate transcript data for a student
     */
    public function generateTranscript(int $studentId): array
    {
        // Get student info
        $studentSql = "SELECT u.*, d.name AS department_name, d.code AS department_code,
                       f.name AS faculty_name, l.name AS level_name
                       FROM users u
                       INNER JOIN departments d ON u.department_id = d.id
                       INNER JOIN faculties f ON d.faculty_id = f.id
                       INNER JOIN levels l ON u.level_id = l.id
                       WHERE u.id = ?";
        
        $stmt = $this->db->prepare($studentSql);
        $stmt->execute([$studentId]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student) {
            return [];
        }

        // Get all results grouped by semester
        $results = $this->getStudentResultsBySemester($studentId);

        // Get detailed courses for each semester
        $semesters = [];
        foreach ($results as $semester) {
            $semesterCourses = $this->getStudentResults($studentId, (int)$semester['semester_id']);
            $semesters[] = [
                'info' => $semester,
                'courses' => $semesterCourses
            ];
        }

        // Calculate CGPA
        $cgpa = $this->calculateCGPA($studentId);

        return [
            'student' => $student,
            'semesters' => $semesters,
            'cgpa' => $cgpa,
            'class_of_degree' => $this->getClassOfDegree($cgpa['cgpa'])
        ];
    }
}
