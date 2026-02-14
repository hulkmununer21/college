<?php
/**
 * Bursar Dashboard Controller
 * 
 * Handles Bursar dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class BursarController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole('BURSAR');
    }

    /**
     * Bursar dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $data = [
            'title' => 'Bursar Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ]
        ];

        $this->view('bursar/dashboard', $data, 'dashboard');
    }
}
