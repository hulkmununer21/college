<?php
/**
 * HOD Dashboard Controller
 * 
 * Handles Head of Department dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class HodController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole('HOD');
    }

    /**
     * HOD dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $data = [
            'title' => 'HOD Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ]
        ];

        $this->view('hod/dashboard', $data, 'dashboard');
    }
}
