<?php
declare(strict_types=1);

/**
 * Academic Structure Controller
 * Handles faculty, department, level, session, and semester management
 * Access restricted to Super Admin and HOD roles
 */
class AcademicStructureController extends Controller {
    
    private Faculty $facultyModel;
    private Department $departmentModel;
    private Level $levelModel;
    private Session $sessionModel;
    private Semester $semesterModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireRole(['SUPER_ADMIN', 'HOD']);
        
        $this->facultyModel = $this->model('Faculty');
        $this->departmentModel = $this->model('Department');
        $this->levelModel = $this->model('Level');
        $this->sessionModel = $this->model('Session');
        $this->semesterModel = $this->model('Semester');
    }

    /**
     * Academic Structure Dashboard
     */
    public function index(): void {
        $data = [
            'title' => 'Academic Structure',
            'faculties' => $this->facultyModel->getAllFacultiesWithStats(),
            'departments' => $this->departmentModel->getAllDepartmentsWithDetails(),
            'levels' => $this->levelModel->getAllLevelsWithStats(),
            'sessions' => $this->sessionModel->getAllSessionsWithStats(),
            'current_semester' => $this->semesterModel->getCurrentSemesterWithSession()
        ];
        
        $this->view('academic/index', $data, 'dashboard');
    }

    // ============================================
    // FACULTY MANAGEMENT
    // ============================================

    /**
     * List all faculties
     */
    public function faculties(): void {
        $data = [
            'title' => 'Manage Faculties',
            'faculties' => $this->facultyModel->getAllFacultiesWithStats()
        ];
        
        $this->view('academic/faculties/index', $data, 'dashboard');
    }

    /**
     * Show create faculty form
     */
    public function createFaculty(): void {
        $data = [
            'title' => 'Create Faculty',
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/faculties/create', $data, 'dashboard');
    }

    /**
     * Process faculty creation
     */
    public function storeFaculty(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/faculties');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/create-faculty');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        // Validation
        $errors = $this->validateFaculty($name, $code);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/academic-structure/create-faculty');
            return;
        }

        // Create faculty
        $facultyId = $this->facultyModel->insert([
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        if ($facultyId) {
            $_SESSION['success'] = "Faculty '{$name}' created successfully";
            $this->redirect('/academic-structure/faculties');
        } else {
            $_SESSION['error'] = 'Failed to create faculty';
            $this->redirect('/academic-structure/create-faculty');
        }
    }

    /**
     * Show edit faculty form
     */
    public function editFaculty(int $id): void {
        $faculty = $this->facultyModel->find($id);
        
        if (!$faculty) {
            $_SESSION['error'] = 'Faculty not found';
            $this->redirect('/academic-structure/faculties');
            return;
        }

        $data = [
            'title' => 'Edit Faculty',
            'faculty' => $faculty,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/faculties/edit', $data, 'dashboard');
    }

    /**
     * Process faculty update
     */
    public function updateFaculty(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/faculties');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/edit-faculty/' . $id);
            return;
        }

        $faculty = $this->facultyModel->find($id);
        if (!$faculty) {
            $_SESSION['error'] = 'Faculty not found';
            $this->redirect('/academic-structure/faculties');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        // Validation
        $errors = $this->validateFaculty($name, $code, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/academic-structure/edit-faculty/' . $id);
            return;
        }

        // Update faculty
        $success = $this->facultyModel->update($id, [
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        if ($success) {
            $_SESSION['success'] = "Faculty updated successfully";
            $this->redirect('/academic-structure/faculties');
        } else {
            $_SESSION['error'] = 'Failed to update faculty';
            $this->redirect('/academic-structure/edit-faculty/' . $id);
        }
    }

    /**
     * Toggle faculty active status
     */
    public function toggleFaculty(int $id): void {
        $success = $this->facultyModel->toggleActive($id);
        
        if ($success) {
            $_SESSION['success'] = 'Faculty status updated';
        } else {
            $_SESSION['error'] = 'Failed to update faculty status';
        }
        
        $this->redirect('/academic-structure/faculties');
    }

    /**
     * View faculty details
     */
    public function viewFaculty(int $id): void {
        $faculty = $this->facultyModel->getFacultyWithDean($id);
        
        if (!$faculty) {
            $_SESSION['error'] = 'Faculty not found';
            $this->redirect('/academic-structure/faculties');
            return;
        }

        $data = [
            'title' => $faculty['name'],
            'faculty' => $faculty,
            'stats' => $this->facultyModel->getFacultyStats($id),
            'departments' => $this->facultyModel->getDepartments($id)
        ];
        
        $this->view('academic/faculties/view', $data, 'dashboard');
    }

    // ============================================
    // DEPARTMENT MANAGEMENT
    // ============================================

    /**
     * List all departments
     */
    public function departments(): void {
        $data = [
            'title' => 'Manage Departments',
            'departments' => $this->departmentModel->getAllDepartmentsWithDetails(),
            'faculties' => $this->facultyModel->getActiveFaculties()
        ];
        
        $this->view('academic/departments/index', $data, 'dashboard');
    }

    /**
     * Show create department form
     */
    public function createDepartment(): void {
        $data = [
            'title' => 'Create Department',
            'faculties' => $this->facultyModel->getActiveFaculties(),
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/departments/create', $data, 'dashboard');
    }

    /**
     * Process department creation
     */
    public function storeDepartment(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/departments');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/create-department');
            return;
        }

        $facultyId = (int)($_POST['faculty_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        // Validation
        $errors = $this->validateDepartment($facultyId, $name, $code);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/academic-structure/create-department');
            return;
        }

        // Create department
        $departmentId = $this->departmentModel->insert([
            'faculty_id' => $facultyId,
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        if ($departmentId) {
            $_SESSION['success'] = "Department '{$name}' created successfully";
            $this->redirect('/academic-structure/departments');
        } else {
            $_SESSION['error'] = 'Failed to create department';
            $this->redirect('/academic-structure/create-department');
        }
    }

    /**
     * Show edit department form
     */
    public function editDepartment(int $id): void {
        $department = $this->departmentModel->find($id);
        
        if (!$department) {
            $_SESSION['error'] = 'Department not found';
            $this->redirect('/academic-structure/departments');
            return;
        }

        $data = [
            'title' => 'Edit Department',
            'department' => $department,
            'faculties' => $this->facultyModel->getActiveFaculties(),
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/departments/edit', $data, 'dashboard');
    }

    /**
     * Process department update
     */
    public function updateDepartment(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/departments');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/edit-department/' . $id);
            return;
        }

        $department = $this->departmentModel->find($id);
        if (!$department) {
            $_SESSION['error'] = 'Department not found';
            $this->redirect('/academic-structure/departments');
            return;
        }

        $facultyId = (int)($_POST['faculty_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        // Validation
        $errors = $this->validateDepartment($facultyId, $name, $code, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/academic-structure/edit-department/' . $id);
            return;
        }

        // Update department
        $success = $this->departmentModel->update($id, [
            'faculty_id' => $facultyId,
            'name' => $name,
            'code' => $code,
            'description' => $description
        ]);

        if ($success) {
            $_SESSION['success'] = "Department updated successfully";
            $this->redirect('/academic-structure/departments');
        } else {
            $_SESSION['error'] = 'Failed to update department';
            $this->redirect('/academic-structure/edit-department/' . $id);
        }
    }

    /**
     * Toggle department active status
     */
    public function toggleDepartment(int $id): void {
        $success = $this->departmentModel->toggleActive($id);
        
        if ($success) {
            $_SESSION['success'] = 'Department status updated';
        } else {
            $_SESSION['error'] = 'Failed to update department status';
        }
        
        $this->redirect('/academic-structure/departments');
    }

    /**
     * View department details
     */
    public function viewDepartment(int $id): void {
        $department = $this->departmentModel->getDepartmentWithDetails($id);
        
        if (!$department) {
            $_SESSION['error'] = 'Department not found';
            $this->redirect('/academic-structure/departments');
            return;
        }

        $data = [
            'title' => $department['department_name'],
            'department' => $department,
            'stats' => $this->departmentModel->getDepartmentStats($id),
            'students' => $this->departmentModel->getStudents($id),
            'lecturers' => $this->departmentModel->getLecturers($id)
        ];
        
        $this->view('academic/departments/view', $data, 'dashboard');
    }

    // ============================================
    // LEVEL MANAGEMENT
    // ============================================

    /**
     * List all levels
     */
    public function levels(): void {
        $data = [
            'title' => 'Manage Levels',
            'levels' => $this->levelModel->getAllLevelsWithStats()
        ];
        
        $this->view('academic/levels/index', $data, 'dashboard');
    }

    /**
     * Show create level form
     */
    public function createLevel(): void {
        $data = [
            'title' => 'Create Level',
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/levels/create', $data, 'dashboard');
    }

    /**
     * Process level creation
     */
    public function storeLevel(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/levels');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/create-level');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $levelNumber = (int)($_POST['level_number'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $minCreditUnits = (int)($_POST['min_credit_units'] ?? 15);
        $maxCreditUnits = (int)($_POST['max_credit_units'] ?? 24);

        // Validation
        $errors = $this->validateLevel($name, $levelNumber, $minCreditUnits, $maxCreditUnits);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/academic-structure/create-level');
            return;
        }

        // Create level
        $levelId = $this->levelModel->insert([
            'name' => $name,
            'level_number' => $levelNumber,
            'description' => $description,
            'min_credit_units' => $minCreditUnits,
            'max_credit_units' => $maxCreditUnits
        ]);

        if ($levelId) {
            $_SESSION['success'] = "Level '{$name}' created successfully";
            $this->redirect('/academic-structure/levels');
        } else {
            $_SESSION['error'] = 'Failed to create level';
            $this->redirect('/academic-structure/create-level');
        }
    }

    /**
     * Show edit level form
     */
    public function editLevel(int $id): void {
        $level = $this->levelModel->find($id);
        
        if (!$level) {
            $_SESSION['error'] = 'Level not found';
            $this->redirect('/academic-structure/levels');
            return;
        }

        $data = [
            'title' => 'Edit Level',
            'level' => $level,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/levels/edit', $data, 'dashboard');
    }

    /**
     * Process level update
     */
    public function updateLevel(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/levels');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/edit-level/' . $id);
            return;
        }

        $level = $this->levelModel->find($id);
        if (!$level) {
            $_SESSION['error'] = 'Level not found';
            $this->redirect('/academic-structure/levels');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $levelNumber = (int)($_POST['level_number'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $minCreditUnits = (int)($_POST['min_credit_units'] ?? 15);
        $maxCreditUnits = (int)($_POST['max_credit_units'] ?? 24);

        // Validation
        $errors = $this->validateLevel($name, $levelNumber, $minCreditUnits, $maxCreditUnits, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/academic-structure/edit-level/' . $id);
            return;
        }

        // Update level
        $success = $this->levelModel->update($id, [
            'name' => $name,
            'level_number' => $levelNumber,
            'description' => $description,
            'min_credit_units' => $minCreditUnits,
            'max_credit_units' => $maxCreditUnits
        ]);

        if ($success) {
            $_SESSION['success'] = "Level updated successfully";
            $this->redirect('/academic-structure/levels');
        } else {
            $_SESSION['error'] = 'Failed to update level';
            $this->redirect('/academic-structure/edit-level/' . $id);
        }
    }

    /**
     * Toggle level active status
     */
    public function toggleLevel(int $id): void {
        $success = $this->levelModel->toggleActive($id);
        
        if ($success) {
            $_SESSION['success'] = 'Level status updated';
        } else {
            $_SESSION['error'] = 'Failed to update level status';
        }
        
        $this->redirect('/academic-structure/levels');
    }

    // ============================================
    // SESSION MANAGEMENT
    // ============================================

    /**
     * List all sessions
     */
    public function sessions(): void {
        $data = [
            'title' => 'Manage Academic Sessions',
            'sessions' => $this->sessionModel->getAllSessionsWithStats(),
            'current_session' => $this->sessionModel->getCurrentSession()
        ];
        
        $this->view('academic/sessions/index', $data, 'dashboard');
    }

    /**
     * Show create session form
     */
    public function createSession(): void {
        $data = [
            'title' => 'Create Academic Session',
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/sessions/create', $data, 'dashboard');
    }

    /**
     * Process session creation
     */
    public function storeSession(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/sessions');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/create-session');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $startYear = (int)($_POST['start_year'] ?? 0);
        $endYear = (int)($_POST['end_year'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        // Validation
        $errors = $this->validateSession($name, $startYear, $endYear, $startDate, $endDate);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/academic-structure/create-session');
            return;
        }

        // Create session
        $sessionId = $this->sessionModel->insert([
            'name' => $name,
            'start_year' => $startYear,
            'end_year' => $endYear,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        if ($sessionId) {
            $_SESSION['success'] = "Session '{$name}' created successfully";
            $this->redirect('/academic-structure/sessions');
        } else {
            $_SESSION['error'] = 'Failed to create session';
            $this->redirect('/academic-structure/create-session');
        }
    }

    /**
     * Show edit session form
     */
    public function editSession(int $id): void {
        $session = $this->sessionModel->find($id);
        
        if (!$session) {
            $_SESSION['error'] = 'Session not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $data = [
            'title' => 'Edit Academic Session',
            'session' => $session,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/sessions/edit', $data, 'dashboard');
    }

    /**
     * Process session update
     */
    public function updateSession(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/sessions');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/edit-session/' . $id);
            return;
        }

        $session = $this->sessionModel->find($id);
        if (!$session) {
            $_SESSION['error'] = 'Session not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $startYear = (int)($_POST['start_year'] ?? 0);
        $endYear = (int)($_POST['end_year'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        // Validation
        $errors = $this->validateSession($name, $startYear, $endYear, $startDate, $endDate, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/academic-structure/edit-session/' . $id);
            return;
        }

        // Update session
        $success = $this->sessionModel->update($id, [
            'name' => $name,
            'start_year' => $startYear,
            'end_year' => $endYear,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        if ($success) {
            $_SESSION['success'] = "Session updated successfully";
            $this->redirect('/academic-structure/sessions');
        } else {
            $_SESSION['error'] = 'Failed to update session';
            $this->redirect('/academic-structure/edit-session/' . $id);
        }
    }

    /**
     * Set current session
     */
    public function setCurrentSession(int $id): void {
        $success = $this->sessionModel->setCurrentSession($id);
        
        if ($success) {
            $_SESSION['success'] = 'Current session updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to set current session';
        }
        
        $this->redirect('/academic-structure/sessions');
    }

    /**
     * View session with semesters
     */
    public function viewSession(int $id): void {
        $session = $this->sessionModel->getSessionWithSemesters($id);
        
        if (!$session) {
            $_SESSION['error'] = 'Session not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $data = [
            'title' => $session['name'],
            'session' => $session,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/sessions/view', $data, 'dashboard');
    }

    // ============================================
    // SEMESTER MANAGEMENT
    // ============================================

    /**
     * Show create semester form
     */
    public function createSemester(int $sessionId): void {
        $session = $this->sessionModel->find($sessionId);
        
        if (!$session) {
            $_SESSION['error'] = 'Session not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $data = [
            'title' => 'Create Semester',
            'session' => $session,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/semesters/create', $data, 'dashboard');
    }

    /**
     * Process semester creation
     */
    public function storeSemester(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/sessions');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $sessionId = (int)($_POST['session_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $semesterNumber = (int)($_POST['semester_number'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $regStartDate = trim($_POST['registration_start_date'] ?? '');
        $regEndDate = trim($_POST['registration_end_date'] ?? '');

        // Validation
        $errors = $this->validateSemester($sessionId, $name, $semesterNumber, $startDate, $endDate, $regStartDate, $regEndDate);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $_SESSION['form_data'] = $_POST;
            $this->redirect('/academic-structure/create-semester/' . $sessionId);
            return;
        }

        // Create semester
        $semesterId = $this->semesterModel->insert([
            'session_id' => $sessionId,
            'name' => $name,
            'semester_number' => $semesterNumber,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_start_date' => $regStartDate,
            'registration_end_date' => $regEndDate
        ]);

        if ($semesterId) {
            $_SESSION['success'] = "Semester created successfully";
            $this->redirect('/academic-structure/view-session/' . $sessionId);
        } else {
            $_SESSION['error'] = 'Failed to create semester';
            $this->redirect('/academic-structure/create-semester/' . $sessionId);
        }
    }

    /**
     * Show edit semester form
     */
    public function editSemester(int $id): void {
        $semester = $this->semesterModel->getSemesterWithSession($id);
        
        if (!$semester) {
            $_SESSION['error'] = 'Semester not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $data = [
            'title' => 'Edit Semester',
            'semester' => $semester,
            'csrf_token' => $this->generateCSRFToken()
        ];
        
        $this->view('academic/semesters/edit', $data, 'dashboard');
    }

    /**
     * Process semester update
     */
    public function updateSemester(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic-structure/sessions');
            return;
        }

        if (!$this->verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Invalid security token';
            $this->redirect('/academic-structure/edit-semester/' . $id);
            return;
        }

        $semester = $this->semesterModel->find($id);
        if (!$semester) {
            $_SESSION['error'] = 'Semester not found';
            $this->redirect('/academic-structure/sessions');
            return;
        }

        $sessionId = (int)($_POST['session_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $semesterNumber = (int)($_POST['semester_number'] ?? 0);
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $regStartDate = trim($_POST['registration_start_date'] ?? '');
        $regEndDate = trim($_POST['registration_end_date'] ?? '');

        // Validation
        $errors = $this->validateSemester($sessionId, $name, $semesterNumber, $startDate, $endDate, $regStartDate, $regEndDate, $id);
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            $this->redirect('/academic-structure/edit-semester/' . $id);
            return;
        }

        // Update semester
        $success = $this->semesterModel->update($id, [
            'name' => $name,
            'semester_number' => $semesterNumber,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_start_date' => $regStartDate,
            'registration_end_date' => $regEndDate
        ]);

        if ($success) {
            $_SESSION['success'] = "Semester updated successfully";
            $this->redirect('/academic-structure/view-session/' . $semester['session_id']);
        } else {
            $_SESSION['error'] = 'Failed to update semester';
            $this->redirect('/academic-structure/edit-semester/' . $id);
        }
    }

    /**
     * Set current semester
     */
    public function setCurrentSemester(int $id): void {
        $success = $this->semesterModel->setCurrentSemester($id);
        
        if ($success) {
            $_SESSION['success'] = 'Current semester updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to set current semester';
        }
        
        $this->redirect('/academic-structure/sessions');
    }

    // ============================================
    // VALIDATION METHODS
    // ============================================

    private function validateFaculty(string $name, string $code, ?int $excludeId = null): array {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Faculty name is required';
        } elseif ($this->facultyModel->nameExists($name, $excludeId)) {
            $errors[] = 'Faculty name already exists';
        }

        if (empty($code)) {
            $errors[] = 'Faculty code is required';
        } elseif (!preg_match('/^[A-Z]{2,5}$/', $code)) {
            $errors[] = 'Faculty code must be 2-5 uppercase letters';
        } elseif ($this->facultyModel->codeExists($code, $excludeId)) {
            $errors[] = 'Faculty code already exists';
        }

        return $errors;
    }

    private function validateDepartment(int $facultyId, string $name, string $code, ?int $excludeId = null): array {
        $errors = [];

        if ($facultyId <= 0) {
            $errors[] = 'Faculty is required';
        } elseif (!$this->facultyModel->find($facultyId)) {
            $errors[] = 'Invalid faculty selected';
        }

        if (empty($name)) {
            $errors[] = 'Department name is required';
        } elseif ($this->departmentModel->nameExistsInFaculty($facultyId, $name, $excludeId)) {
            $errors[] = 'Department name already exists in this faculty';
        }

        if (empty($code)) {
            $errors[] = 'Department code is required';
        } elseif (!preg_match('/^[A-Z]{2,5}$/', $code)) {
            $errors[] = 'Department code must be 2-5 uppercase letters';
        } elseif ($this->departmentModel->codeExists($code, $excludeId)) {
            $errors[] = 'Department code already exists';
        }

        return $errors;
    }

    private function validateLevel(string $name, int $levelNumber, int $minCredits, int $maxCredits, ?int $excludeId = null): array {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Level name is required';
        }

        if ($levelNumber <= 0) {
            $errors[] = 'Level number is required';
        } elseif ($this->levelModel->levelNumberExists($levelNumber, $excludeId)) {
            $errors[] = 'Level number already exists';
        }

        if ($minCredits < 0) {
            $errors[] = 'Minimum credit units cannot be negative';
        }

        if ($maxCredits < $minCredits) {
            $errors[] = 'Maximum credit units must be greater than minimum';
        }

        return $errors;
    }

    private function validateSession(string $name, int $startYear, int $endYear, string $startDate, string $endDate, ?int $excludeId = null): array {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Session name is required';
        } elseif ($this->sessionModel->nameExists($name, $excludeId)) {
            $errors[] = 'Session name already exists';
        }

        if ($startYear <= 0 || $endYear <= 0) {
            $errors[] = 'Valid start and end years are required';
        } elseif ($endYear != $startYear + 1) {
            $errors[] = 'End year must be one year after start year';
        }

        if (empty($startDate) || empty($endDate)) {
            $errors[] = 'Start and end dates are required';
        } elseif (strtotime($endDate) <= strtotime($startDate)) {
            $errors[] = 'End date must be after start date';
        } elseif ($this->sessionModel->datesOverlap($startDate, $endDate, $excludeId)) {
            $errors[] = 'Session dates overlap with existing session';
        }

        return $errors;
    }

    private function validateSemester(int $sessionId, string $name, int $semesterNumber, string $startDate, string $endDate, string $regStartDate, string $regEndDate, ?int $excludeId = null): array {
        $errors = [];

        if ($sessionId <= 0 || !$this->sessionModel->find($sessionId)) {
            $errors[] = 'Invalid session';
        }

        if (empty($name)) {
            $errors[] = 'Semester name is required';
        }

        if ($semesterNumber <= 0 || $semesterNumber > 2) {
            $errors[] = 'Semester number must be 1 or 2';
        } elseif ($this->semesterModel->semesterExistsInSession($sessionId, $semesterNumber, $excludeId)) {
            $errors[] = 'This semester already exists for the session';
        }

        if (empty($startDate) || empty($endDate)) {
            $errors[] = 'Start and end dates are required';
        } elseif (strtotime($endDate) <= strtotime($startDate)) {
            $errors[] = 'End date must be after start date';
        } elseif (!$this->semesterModel->datesWithinSession($sessionId, $startDate, $endDate)) {
            $errors[] = 'Semester dates must be within session dates';
        }

        if (empty($regStartDate) || empty($regEndDate)) {
            $errors[] = 'Registration dates are required';
        } elseif (strtotime($regEndDate) <= strtotime($regStartDate)) {
            $errors[] = 'Registration end date must be after start date';
        } elseif (strtotime($regStartDate) < strtotime($startDate)) {
            $errors[] = 'Registration must start on or after semester start date';
        }

        return $errors;
    }
}
