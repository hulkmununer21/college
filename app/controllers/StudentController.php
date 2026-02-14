<?php
/**
 * Student Dashboard Controller
 * 
 * Handles Student dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class StudentController extends Controller
{
    private $userModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Require authentication
        $this->requireAuth();
        
        // Require Student role
        $this->requireRole('STUDENT');

        $this->userModel = $this->model('User');
    }

    /**
     * Student dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $userId = $_SESSION['user_id'];
        $student = $this->userModel->find($userId);

        $data = [
            'title' => 'Student Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role_name']
            ],
            'student' => $student
        ];

        $this->view('student/dashboard', $data, 'dashboard');
    }

    /**
     * View profile
     * 
     * @return void
     */
    public function profile(): void
    {
        $userId = $_SESSION['user_id'];
        $student = $this->userModel->getUserWithRole($userId);

        $data = [
            'title' => 'My Profile',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ],
            'student' => $student
        ];

        $this->view('student/profile', $data, 'dashboard');
    }
}
