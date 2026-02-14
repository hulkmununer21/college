<?php
declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Semester.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/User.php';

/**
 * GradeController
 * Handles grade entry, result viewing, approval workflow, and transcript generation
 */
class GradeController extends Controller
{
    private Grade $gradeModel;
    private Course $courseModel;

    public function __construct()
    {
        parent::__construct();
        $this->gradeModel = new Grade();
        $this->courseModel = new Course();
    }

    // ========================================
    // LECTURER: GRADE ENTRY
    // ========================================

    /**
     * Show lecturer's courses for grade entry
     */
    public function index(): void
    {
        $this->requireAuth(['LECTURER']);

        $sessionModel = new Session();
        $semesterModel = new Semester();
        
        $currentSession = $sessionModel->getCurrentSession();
        $currentSemester = $semesterModel->getCurrentSemester();

        $courses = [];
        if ($currentSession) {
            $courses = $this->courseModel->getCoursesByLecturer(
                (int)$this->user['id'],
                (int)$currentSession['id']
            );
        }

        $this->view('grades/index', [
            'title' => 'Grade Entry',
            'session' => $currentSession,
            'semester' => $currentSemester,
            'courses' => $courses
        ]);
    }

    /**
     * Show grade entry form for a course
     */
    public function entry(int $courseId): void
    {
        $this->requireAuth(['LECTURER', 'HOD', 'SUPER_ADMIN']);

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/grades');
            return;
        }

        // Verify lecturer is assigned to this course (for lecturers only)
        if ($this->user['role_name'] === 'LECTURER') {
            $sessionModel = new Session();
            $currentSession = $sessionModel->getCurrentSession();
            if (!$currentSession) {
                $_SESSION['error'] = 'No active session found.';
                $this->redirect('/grades');
                return;
            }

            $lecturerCourses = $this->courseModel->getCoursesByLecturer(
                (int)$this->user['id'],
                (int)$currentSession['id']
            );

            $isAssigned = false;
            foreach ($lecturerCourses as $lc) {
                if ($lc['course_id'] == $courseId) {
                    $isAssigned = true;
                    break;
                }
            }

            if (!$isAssigned) {
                $_SESSION['error'] = 'You are not assigned to this course.';
                $this->redirect('/grades');
                return;
            }
        }

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();

        if (!$currentSemester) {
            $_SESSION['error'] = 'No active semester found.';
            $this->redirect('/grades');
            return;
        }

        // Get students and their grades
        $students = $this->gradeModel->getGradesByCourse($courseId, (int)$currentSemester['id']);

        // Get statistics
        $stats = $this->gradeModel->getCourseStatistics($courseId, (int)$currentSemester['id']);

        $this->view('grades/entry', [
            'title' => 'Grade Entry - ' . $course['code'],
            'course' => $course,
            'semester' => $currentSemester,
            'students' => $students,
            'stats' => $stats
        ]);
    }

    /**
     * Save grade (AJAX)
     */
    public function save(): void
    {
        $this->requireAuth(['LECTURER', 'HOD', 'SUPER_ADMIN']);
        $this->requirePost();
        $this->validateCsrf();

        $data = [
            'registration_id' => (int)($_POST['registration_id'] ?? 0),
            'student_id' => (int)($_POST['student_id'] ?? 0),
            'course_id' => (int)($_POST['course_id'] ?? 0),
            'semester_id' => (int)($_POST['semester_id'] ?? 0),
            'ca_score' => (float)($_POST['ca_score'] ?? 0),
            'exam_score' => (float)($_POST['exam_score'] ?? 0),
            'entered_by' => (int)$this->user['id']
        ];

        $result = $this->gradeModel->enterGrade($data);
        $this->jsonResponse($result);
    }

    /**
     * Bulk save grades
     */
    public function bulkSave(): void
    {
        $this->requireAuth(['LECTURER', 'HOD', 'SUPER_ADMIN']);
        $this->requirePost();
        $this->validateCsrf();

        $grades = json_decode($_POST['grades'] ?? '[]', true);
        
        if (empty($grades)) {
            $this->jsonResponse(['success' => false, 'message' => 'No grades provided']);
            return;
        }

        $result = $this->gradeModel->bulkEnterGrades($grades, (int)$this->user['id']);
        $this->jsonResponse($result);
    }

    /**
     * Submit grades for approval
     */
    public function submit(int $courseId): void
    {
        $this->requireAuth(['LECTURER']);
        $this->requirePost();
        $this->validateCsrf();

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();

        if (!$currentSemester) {
            $_SESSION['error'] = 'No active semester found.';
            $this->redirect('/grades/entry/' . $courseId);
            return;
        }

        if ($this->gradeModel->submitGrades($courseId, (int)$currentSemester['id'], (int)$this->user['id'])) {
            $_SESSION['success'] = 'Grades submitted for approval successfully.';
        } else {
            $_SESSION['error'] = 'Failed to submit grades.';
        }

        $this->redirect('/grades/entry/' . $courseId);
    }

    // ========================================
    // HOD/ADMIN: GRADE APPROVAL
    // ========================================

    /**
     * View pending grades for approval
     */
    public function pending(): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);

        $departmentId = null;
        if ($this->user['role_name'] === 'HOD') {
            $departmentId = $this->user['department_id'];
        }

        $pendingGrades = $this->gradeModel->getPendingGrades($departmentId);

        $this->view('grades/pending', [
            'title' => 'Pending Grade Approvals',
            'pending' => $pendingGrades
        ]);
    }

    /**
     * View grades for a specific course (for approval)
     */
    public function review(int $courseId, int $semesterId): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/grades/pending');
            return;
        }

        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);

        // Get all grades for this course
        $grades = $this->gradeModel->getGradesByCourse($courseId, $semesterId);

        // Get statistics
        $stats = $this->gradeModel->getCourseStatistics($courseId, $semesterId);

        // Get grade distribution
        $distribution = $this->gradeModel->getGradeDistribution($courseId, $semesterId);

        $this->view('grades/review', [
            'title' => 'Review Grades - ' . $course['code'],
            'course' => $course,
            'semester' => $semester,
            'grades' => $grades,
            'stats' => $stats,
            'distribution' => $distribution
        ]);
    }

    /**
     * Approve grades
     */
    public function approve(): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);
        $this->requirePost();
        $this->validateCsrf();

        $gradeIds = $_POST['grade_ids'] ?? [];
        
        if (empty($gradeIds) || !is_array($gradeIds)) {
            $_SESSION['error'] = 'No grades selected for approval.';
            $this->redirect('/grades/pending');
            return;
        }

        $gradeIds = array_map('intval', $gradeIds);

        if ($this->gradeModel->approveGrades($gradeIds, (int)$this->user['id'])) {
            $_SESSION['success'] = 'Grades approved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to approve grades.';
        }

        $this->redirect('/grades/pending');
    }

    // ========================================
    // STUDENT: VIEW RESULTS
    // ========================================

    /**
     * Student results dashboard
     */
    public function myResults(): void
    {
        $this->requireAuth(['STUDENT']);

        // Get results by semester
        $semesters = $this->gradeModel->getStudentResultsBySemester((int)$this->user['id']);

        // Calculate CGPA
        $cgpa = $this->gradeModel->calculateCGPA((int)$this->user['id']);

        $this->view('grades/my-results', [
            'title' => 'My Results',
            'semesters' => $semesters,
            'cgpa' => $cgpa,
            'class_of_degree' => $this->gradeModel->getClassOfDegree($cgpa['cgpa'])
        ]);
    }

    /**
     * View results for a specific semester
     */
    public function semesterResults(int $semesterId): void
    {
        $this->requireAuth(['STUDENT']);

        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);

        if (!$semester) {
            $_SESSION['error'] = 'Semester not found.';
            $this->redirect('/grades/my-results');
            return;
        }

        // Get results
        $results = $this->gradeModel->getStudentResults((int)$this->user['id'], $semesterId);

        // Calculate GPA
        $gpa = $this->gradeModel->calculateGPA((int)$this->user['id'], $semesterId);

        // Calculate CGPA
        $cgpa = $this->gradeModel->calculateCGPA((int)$this->user['id']);

        $this->view('grades/semester-results', [
            'title' => 'Semester Results',
            'semester' => $semester,
            'results' => $results,
            'gpa' => $gpa,
            'cgpa' => $cgpa
        ]);
    }

    /**
     * Generate and view transcript
     */
    public function transcript(?int $studentId = null): void
    {
        // Students can only view their own transcript
        if ($this->user['role_name'] === 'STUDENT') {
            $studentId = (int)$this->user['id'];
        } else {
            // Admin/HOD can view any student's transcript
            $this->requireAuth(['HOD', 'SUPER_ADMIN']);
            
            if (!$studentId) {
                $_SESSION['error'] = 'Student ID is required.';
                $this->redirect('/admin/users');
                return;
            }
        }

        $transcript = $this->gradeModel->generateTranscript($studentId);

        if (empty($transcript)) {
            $_SESSION['error'] = 'Unable to generate transcript. Student not found or no results available.';
            
            if ($this->user['role_name'] === 'STUDENT') {
                $this->redirect('/grades/my-results');
            } else {
                $this->redirect('/admin/users');
            }
            return;
        }

        $this->view('grades/transcript', [
            'title' => 'Academic Transcript',
            'transcript' => $transcript,
            'print_mode' => isset($_GET['print'])
        ]);
    }

    /**
     * Download transcript as PDF
     */
    public function downloadTranscript(?int $studentId = null): void
    {
        // Students can only download their own transcript
        if ($this->user['role_name'] === 'STUDENT') {
            $studentId = (int)$this->user['id'];
        } else {
            $this->requireAuth(['HOD', 'SUPER_ADMIN']);
            
            if (!$studentId) {
                $_SESSION['error'] = 'Student ID is required.';
                $this->redirect('/admin/users');
                return;
            }
        }

        // For now, redirect to print view
        // In future, integrate PDF library (e.g., TCPDF, mPDF)
        $this->redirect('/grades/transcript/' . $studentId . '?print=1');
    }

    // ========================================
    // STATISTICS & REPORTS
    // ========================================

    /**
     * Grade statistics for a course
     */
    public function statistics(int $courseId, int $semesterId): void
    {
        $this->requireAuth(['LECTURER', 'HOD', 'SUPER_ADMIN']);

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/grades');
            return;
        }

        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);

        // Get statistics
        $stats = $this->gradeModel->getCourseStatistics($courseId, $semesterId);

        // Get grade distribution
        $distribution = $this->gradeModel->getGradeDistribution($courseId, $semesterId);

        // Get all grades for detailed view
        $grades = $this->gradeModel->getGradesByCourse($courseId, $semesterId);

        $this->view('grades/statistics', [
            'title' => 'Course Statistics - ' . $course['code'],
            'course' => $course,
            'semester' => $semester,
            'stats' => $stats,
            'distribution' => $distribution,
            'grades' => $grades
        ]);
    }

    /**
     * Top performers in a semester
     */
    public function topPerformers(int $semesterId): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);

        $semesterModel = new Semester();
        $semester = $semesterModel->find($semesterId);

        if (!$semester) {
            $_SESSION['error'] = 'Semester not found.';
            $this->redirect('/admin/dashboard');
            return;
        }

        $limit = (int)($_GET['limit'] ?? 20);
        $topPerformers = $this->gradeModel->getTopPerformers($semesterId, $limit);

        $this->view('grades/top-performers', [
            'title' => 'Top Performers - ' . $semester['name'],
            'semester' => $semester,
            'performers' => $topPerformers,
            'limit' => $limit
        ]);
    }

    /**
     * Department performance overview
     */
    public function departmentPerformance(): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();

        // Get department-level statistics
        // This could be expanded with more detailed queries

        $this->view('grades/department-performance', [
            'title' => 'Department Performance',
            'semester' => $currentSemester
        ]);
    }

    // ========================================
    // ADMIN: MANAGE RESULTS
    // ========================================

    /**
     * View all results (Admin)
     */
    public function allResults(): void
    {
        $this->requireAuth(['SUPER_ADMIN']);

        $filters = [
            'semester_id' => (int)($_GET['semester_id'] ?? 0),
            'department_id' => (int)($_GET['department_id'] ?? 0),
            'level_id' => (int)($_GET['level_id'] ?? 0)
        ];

        // Get filtering options
        $semesterModel = new Semester();
        $semesters = $semesterModel->findAll();

        $this->view('grades/all-results', [
            'title' => 'All Results',
            'filters' => $filters,
            'semesters' => $semesters
        ]);
    }

    /**
     * Search student results
     */
    public function search(): void
    {
        $this->requireAuth(['HOD', 'SUPER_ADMIN']);

        $matricNumber = $_GET['matric_number'] ?? '';
        $student = null;
        $results = [];

        if ($matricNumber) {
            $userModel = new User();
            $student = $userModel->findByMatricNumber($matricNumber);

            if ($student) {
                $results = $this->gradeModel->getStudentResultsBySemester((int)$student['id']);
            }
        }

        $this->view('grades/search', [
            'title' => 'Search Results',
            'matric_number' => $matricNumber,
            'student' => $student,
            'results' => $results
        ]);
    }
}
