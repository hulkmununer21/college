<?php

declare(strict_types=1);

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/CourseRegistration.php';
require_once __DIR__ . '/../models/Faculty.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Level.php';
require_once __DIR__ . '/../models/Session.php';
require_once __DIR__ . '/../models/Semester.php';
require_once __DIR__ . '/../models/User.php';

/**
 * CourseController
 * Handles course management, registration, and assignment
 */
class CourseController extends Controller
{
    private Course $courseModel;
    private CourseRegistration $registrationModel;

    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
        $this->registrationModel = new CourseRegistration();
    }

    // ========================================
    // ADMIN/HOD COURSE MANAGEMENT
    // ========================================

    /**
     * Display all courses (Admin/HOD)
     */
    public function index(): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);

        $filters = [];
        
        // Apply filters
        if (!empty($_GET['department_id'])) {
            $filters['department_id'] = (int) $_GET['department_id'];
        }

        if (!empty($_GET['level_id'])) {
            $filters['level_id'] = (int) $_GET['level_id'];
        }

        if (!empty($_GET['semester_number'])) {
            $filters['semester_number'] = (int) $_GET['semester_number'];
        }

        if (isset($_GET['is_active'])) {
            $filters['is_active'] = (int) $_GET['is_active'];
        }

        if (isset($_GET['is_elective'])) {
            $filters['is_elective'] = (int) $_GET['is_elective'];
        }

        // HOD can only see their department's courses
        if ($this->user['role_name'] === 'HOD') {
            $filters['department_id'] = $this->user['department_id'];
        }

        $courses = $this->courseModel->getAllCoursesWithDetails($filters);

        // Get filter options
        $facultyModel = new Faculty();
        $departmentModel = new Department();
        $levelModel = new Level();

        $faculties = $facultyModel->getActiveFaculties();
        $departments = $departmentModel->getActiveDepartments();
        $levels = $levelModel->getActiveLevels();

        $this->view('courses/index', [
            'title' => 'Course Management',
            'courses' => $courses,
            'faculties' => $faculties,
            'departments' => $departments,
            'levels' => $levels,
            'filters' => $filters
        ]);
    }

    /**
     * Show create course form
     */
    public function create(): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);

        $departmentModel = new Department();
        $levelModel = new Level();

        // HOD can only create courses for their department
        if ($this->user['role_name'] === 'HOD') {
            $departments = [$departmentModel->find($this->user['department_id'])];
        } else {
            $departments = $departmentModel->getActiveDepartments();
        }

        $levels = $levelModel->getActiveLevels();

        $this->view('courses/create', [
            'title' => 'Create Course',
            'departments' => $departments,
            'levels' => $levels
        ]);
    }

    /**
     * Store a new course
     */
    public function store(): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $errors = $this->validateCourseData($_POST);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/courses/create');
            return;
        }

        // HOD can only create courses for their department
        if ($this->user['role_name'] === 'HOD' && (int)$_POST['department_id'] !== $this->user['department_id']) {
            $_SESSION['error'] = 'You can only create courses for your department.';
            $this->redirect('/courses/create');
            return;
        }

        $data = [
            'code' => strtoupper(trim($_POST['code'])),
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description'] ?? ''),
            'department_id' => (int) $_POST['department_id'],
            'level_id' => (int) $_POST['level_id'],
            'semester_number' => (int) $_POST['semester_number'],
            'credit_units' => (int) $_POST['credit_units'],
            'is_elective' => isset($_POST['is_elective']) ? 1 : 0,
            'is_active' => 1
        ];

        $courseId = $this->courseModel->create($data);

        if ($courseId) {
            $_SESSION['success'] = 'Course created successfully.';
            $this->redirect('/courses/' . $courseId);
        } else {
            $_SESSION['error'] = 'Failed to create course.';
            $this->redirect('/courses/create');
        }
    }

    /**
     * View a single course
     */
    public function view(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD', 'LECTURER']);

        $course = $this->courseModel->getCourseWithDetails($id);

        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/courses');
            return;
        }

        // HOD can only view their department's courses
        if ($this->user['role_name'] === 'HOD' && (int)$course['department_id'] !== $this->user['department_id']) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/courses');
            return;
        }

        // Get course statistics
        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();
        $stats = [];
        
        if ($currentSemester) {
            $stats = $this->courseModel->getCourseStats($id, (int)$currentSemester['id']);
        }

        $this->view('courses/view', [
            'title' => $course['code'] . ' - ' . $course['title'],
            'course' => $course,
            'stats' => $stats
        ]);
    }

    /**
     * Show edit course form
     */
    public function edit(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);

        $course = $this->courseModel->find($id);

        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/courses');
            return;
        }

        // HOD can only edit their department's courses
        if ($this->user['role_name'] === 'HOD' && (int)$course['department_id'] !== $this->user['department_id']) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/courses');
            return;
        }

        $departmentModel = new Department();
        $levelModel = new Level();

        if ($this->user['role_name'] === 'HOD') {
            $departments = [$departmentModel->find($this->user['department_id'])];
        } else {
            $departments = $departmentModel->getActiveDepartments();
        }

        $levels = $levelModel->getActiveLevels();

        $this->view('courses/edit', [
            'title' => 'Edit Course',
            'course' => $course,
            'departments' => $departments,
            'levels' => $levels
        ]);
    }

    /**
     * Update a course
     */
    public function update(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $course = $this->courseModel->find($id);

        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/courses');
            return;
        }

        // HOD can only edit their department's courses
        if ($this->user['role_name'] === 'HOD' && (int)$course['department_id'] !== $this->user['department_id']) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/courses');
            return;
        }

        $errors = $this->validateCourseData($_POST, $id);

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/courses/' . $id . '/edit');
            return;
        }

        $data = [
            'code' => strtoupper(trim($_POST['code'])),
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description'] ?? ''),
            'department_id' => (int) $_POST['department_id'],
            'level_id' => (int) $_POST['level_id'],
            'semester_number' => (int) $_POST['semester_number'],
            'credit_units' => (int) $_POST['credit_units'],
            'is_elective' => isset($_POST['is_elective']) ? 1 : 0
        ];

        if ($this->courseModel->update($id, $data)) {
            $_SESSION['success'] = 'Course updated successfully.';
            $this->redirect('/courses/' . $id);
        } else {
            $_SESSION['error'] = 'Failed to update course.';
            $this->redirect('/courses/' . $id . '/edit');
        }
    }

    /**
     * Toggle course active status
     */
    public function toggle(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $course = $this->courseModel->find($id);

        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/courses');
            return;
        }

        // HOD can only toggle their department's courses
        if ($this->user['role_name'] === 'HOD' && (int)$course['department_id'] !== $this->user['department_id']) {
            $_SESSION['error'] = 'Access denied.';
            $this->redirect('/courses');
            return;
        }

        if ($this->courseModel->toggleActive($id)) {
            $newStatus = $course['is_active'] ? 'deactivated' : 'activated';
            $_SESSION['success'] = "Course {$newStatus} successfully.";
        } else {
            $_SESSION['error'] = 'Failed to toggle course status.';
        }

        $this->redirect('/courses');
    }

    // ========================================
    // PREREQUISITE MANAGEMENT
    // ========================================

    /**
     * Add prerequisite to a course
     */
    public function addPrerequisite(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $course = $this->courseModel->find($id);

        if (!$course) {
            $this->jsonResponse(['success' => false, 'message' => 'Course not found.']);
            return;
        }

        $prerequisiteId = (int) ($_POST['prerequisite_id'] ?? 0);

        if (!$prerequisiteId) {
            $this->jsonResponse(['success' => false, 'message' => 'Prerequisite course is required.']);
            return;
        }

        if ($id === $prerequisiteId) {
            $this->jsonResponse(['success' => false, 'message' => 'A course cannot be its own prerequisite.']);
            return;
        }

        if ($this->courseModel->addPrerequisite($id, $prerequisiteId)) {
            $this->jsonResponse(['success' => true, 'message' => 'Prerequisite added successfully.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add prerequisite. This may create a circular dependency.']);
        }
    }

    /**
     * Remove prerequisite from a course
     */
    public function removePrerequisite(int $id, int $prerequisiteId): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        if ($this->courseModel->removePrerequisite($id, $prerequisiteId)) {
            $_SESSION['success'] = 'Prerequisite removed successfully.';
        } else {
            $_SESSION['error'] = 'Failed to remove prerequisite.';
        }

        $this->redirect('/courses/' . $id);
    }

    // ========================================
    // LECTURER ASSIGNMENT
    // ========================================

    /**
     * Assign lecturer to course
     */
    public function assignLecturer(int $id): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $lecturerId = (int) ($_POST['lecturer_id'] ?? 0);
        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $isCoordinator = isset($_POST['is_coordinator']);

        if (!$lecturerId || !$sessionId) {
            $this->jsonResponse(['success' => false, 'message' => 'Lecturer and session are required.']);
            return;
        }

        if ($this->courseModel->assignLecturer($id, $lecturerId, $sessionId, $isCoordinator)) {
            $this->jsonResponse(['success' => true, 'message' => 'Lecturer assigned successfully.']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Failed to assign lecturer.']);
        }
    }

    /**
     * Remove lecturer from course
     */
    public function removeLecturer(int $id, int $lecturerId, int $sessionId): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        if ($this->courseModel->removeLecturer($id, $lecturerId, $sessionId)) {
            $_SESSION['success'] = 'Lecturer removed successfully.';
        } else {
            $_SESSION['error'] = 'Failed to remove lecturer.';
        }

        $this->redirect('/courses/' . $id);
    }

    // ========================================
    // STUDENT COURSE REGISTRATION
    // ========================================

    /**
     * Show available courses for student registration
     */
    public function available(): void
    {
        $this->requireAuth(['STUDENT']);

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemesterWithSession();

        if (!$currentSemester) {
            $this->view('courses/available', [
                'title' => 'Available Courses',
                'semester' => null,
                'courses' => [],
                'error' => 'No active semester found.'
            ]);
            return;
        }

        // Check if registration is open
        $registrationStatus = $semesterModel->getRegistrationStatus((int)$currentSemester['id']);

        $courses = $this->registrationModel->getAvailableCoursesForStudent(
            (int)$this->user['id'],
            (int)$currentSemester['id']
        );

        $summary = $this->registrationModel->getRegistrationSummary(
            (int)$this->user['id'],
            (int)$currentSemester['id']
        );

        $this->view('registration/available', [
            'title' => 'Available Courses',
            'semester' => $currentSemester,
            'courses' => $courses,
            'registration_status' => $registrationStatus,
            'summary' => $summary
        ]);
    }

    /**
     * Register for a course
     */
    public function register(): void
    {
        $this->requireAuth(['STUDENT']);
        $this->requirePost();
        $this->validateCsrf();

        $courseId = (int) ($_POST['course_id'] ?? 0);

        if (!$courseId) {
            $this->jsonResponse(['success' => false, 'message' => 'Course ID is required.']);
            return;
        }

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();

        if (!$currentSemester) {
            $this->jsonResponse(['success' => false, 'message' => 'No active semester found.']);
            return;
        }

        $result = $this->registrationModel->registerCourse(
            (int)$this->user['id'],
            $courseId,
            (int)$currentSemester['id']
        );

        $this->jsonResponse($result);
    }

    /**
     * Drop a course
     */
    public function drop(int $registrationId): void
    {
        $this->requireAuth(['STUDENT']);
        $this->requirePost();
        $this->validateCsrf();

        // Verify this registration belongs to the student
        $registration = $this->registrationModel->find($registrationId);

        if (!$registration || (int)$registration['student_id'] !== (int)$this->user['id']) {
            $_SESSION['error'] = 'Invalid registration.';
            $this->redirect('/courses/my-registrations');
            return;
        }

        if ($this->registrationModel->dropCourse($registrationId)) {
            $_SESSION['success'] = 'Course dropped successfully.';
        } else {
            $_SESSION['error'] = 'Failed to drop course.';
        }

        $this->redirect('/courses/my-registrations');
    }

    /**
     * View student's registered courses
     */
    public function myRegistrations(): void
    {
        $this->requireAuth(['STUDENT']);

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemesterWithSession();

        $registrations = [];
        $summary = [];

        if ($currentSemester) {
            $registrations = $this->registrationModel->getStudentRegistrations(
                (int)$this->user['id'],
                (int)$currentSemester['id']
            );

            $summary = $this->registrationModel->getRegistrationSummary(
                (int)$this->user['id'],
                (int)$currentSemester['id']
            );
        }

        $this->view('registration/my-registrations', [
            'title' => 'My Course Registrations',
            'semester' => $currentSemester,
            'registrations' => $registrations,
            'summary' => $summary
        ]);
    }

    // ========================================
    // APPROVAL MANAGEMENT (HOD/ADMIN)
    // ========================================

    /**
     * View pending registrations for approval
     */
    public function pendingApprovals(): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemesterWithSession();

        $departmentId = null;
        if ($this->user['role_name'] === 'HOD') {
            $departmentId = $this->user['department_id'];
        }

        $pending = [];
        if ($currentSemester) {
            $pending = $this->registrationModel->getPendingRegistrations(
                $departmentId,
                (int)$currentSemester['id']
            );
        }

        $this->view('registration/pending', [
            'title' => 'Pending Course Registrations',
            'semester' => $currentSemester,
            'registrations' => $pending
        ]);
    }

    /**
     * Approve a registration
     */
    public function approve(int $registrationId): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        if ($this->registrationModel->approveCourse($registrationId, (int)$this->user['id'])) {
            $_SESSION['success'] = 'Registration approved successfully.';
        } else {
            $_SESSION['error'] = 'Failed to approve registration.';
        }

        $this->redirect('/courses/pending-approvals');
    }

    /**
     * Reject a registration
     */
    public function reject(int $registrationId): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        if ($this->registrationModel->rejectCourse($registrationId)) {
            $_SESSION['success'] = 'Registration rejected.';
        } else {
            $_SESSION['error'] = 'Failed to reject registration.';
        }

        $this->redirect('/courses/pending-approvals');
    }

    /**
     * Bulk approve registrations
     */
    public function bulkApprove(): void
    {
        $this->requireAuth(['SUPER_ADMIN', 'HOD']);
        $this->requirePost();
        $this->validateCsrf();

        $registrationIds = $_POST['registration_ids'] ?? [];

        if (empty($registrationIds) || !is_array($registrationIds)) {
            $_SESSION['error'] = 'No registrations selected.';
            $this->redirect('/courses/pending-approvals');
            return;
        }

        $count = $this->registrationModel->bulkApprove(
            array_map('intval', $registrationIds),
            (int)$this->user['id']
        );

        $_SESSION['success'] = "Approved {$count} registration(s) successfully.";
        $this->redirect('/courses/pending-approvals');
    }

    // ========================================
    // LECTURER VIEWS
    // ========================================

    /**
     * View lecturer's assigned courses
     */
    public function myCourses(): void
    {
        $this->requireAuth(['LECTURER']);

        $sessionModel = new Session();
        $currentSession = $sessionModel->getCurrentSession();

        $courses = [];
        if ($currentSession) {
            $courses = $this->courseModel->getCoursesByLecturer(
                (int)$this->user['id'],
                (int)$currentSession['id']
            );
        }

        $this->view('courses/my-courses', [
            'title' => 'My Courses',
            'session' => $currentSession,
            'courses' => $courses
        ]);
    }

    /**
     * View students in a course
     */
    public function students(int $courseId): void
    {
        $this->requireAuth(['LECTURER', 'HOD', 'SUPER_ADMIN']);

        $course = $this->courseModel->find($courseId);

        if (!$course) {
            $_SESSION['error'] = 'Course not found.';
            $this->redirect('/courses/my-courses');
            return;
        }

        $semesterModel = new Semester();
        $currentSemester = $semesterModel->getCurrentSemester();

        $students = [];
        if ($currentSemester) {
            $students = $this->registrationModel->getStudentsByCourse(
                $courseId,
                (int)$currentSemester['id'],
                'approved'
            );
        }

        $this->view('courses/students', [
            'title' => 'Students - ' . $course['code'],
            'course' => $course,
            'semester' => $currentSemester,
            'students' => $students
        ]);
    }

    // ========================================
    // VALIDATION
    // ========================================

    /**
     * Validate course form data
     */
    private function validateCourseData(array $data, ?int $excludeId = null): array
    {
        $errors = [];

        // Code
        if (empty($data['code'])) {
            $errors['code'] = 'Course code is required.';
        } elseif (strlen($data['code']) > 20) {
            $errors['code'] = 'Course code must not exceed 20 characters.';
        } elseif ($this->courseModel->codeExists($data['code'], $excludeId)) {
            $errors['code'] = 'This course code already exists.';
        }

        // Title
        if (empty($data['title'])) {
            $errors['title'] = 'Course title is required.';
        } elseif (strlen($data['title']) > 200) {
            $errors['title'] = 'Course title must not exceed 200 characters.';
        }

        // Department
        if (empty($data['department_id']) || !is_numeric($data['department_id'])) {
            $errors['department_id'] = 'Please select a department.';
        }

        // Level
        if (empty($data['level_id']) || !is_numeric($data['level_id'])) {
            $errors['level_id'] = 'Please select a level.';
        }

        // Semester number
        if (empty($data['semester_number']) || !in_array((int)$data['semester_number'], [1, 2])) {
            $errors['semester_number'] = 'Please select a valid semester (1 or 2).';
        }

        // Credit units
        if (empty($data['credit_units']) || !is_numeric($data['credit_units'])) {
            $errors['credit_units'] = 'Credit units must be a number.';
        } elseif ((int)$data['credit_units'] < 1 || (int)$data['credit_units'] > 10) {
            $errors['credit_units'] = 'Credit units must be between 1 and 10.';
        }

        return $errors;
    }
}
