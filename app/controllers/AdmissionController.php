<?php
/**
 * Admission Officer Dashboard Controller
 * 
 * Handles Admission Officer dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class AdmissionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole(['SUPER_ADMIN', 'ADMISSION_OFFICER']);
    }

    /**
     * Admission dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $data = [
            'title' => 'Admission Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ]
        ];

        $this->view('admission/dashboard', $data, 'dashboard');
    }
}
