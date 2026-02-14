<?php
/**
 * Lecturer Dashboard Controller
 * 
 * Handles Lecturer dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class LecturerController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole('LECTURER');
    }

    /**
     * Lecturer dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $data = [
            'title' => 'Lecturer Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ]
        ];

        $this->view('lecturer/dashboard', $data, 'dashboard');
    }
}
