<?php
/**
 * Admin Dashboard Controller
 * 
 * Handles Super Administrator dashboard and operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class AdminController extends Controller
{
    private $userModel;
    private $roleModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Require authentication
        $this->requireAuth();
        
        // Require Super Admin or Admission Officer role
        $this->requireRole(['SUPER_ADMIN', 'ADMISSION_OFFICER']);

        $this->userModel = $this->model('User');
        $this->roleModel = $this->model('Role');
    }

    /**
     * Admin dashboard
     * 
     * @return void
     */
    public function dashboard(): void
    {
        // Get statistics
        $totalUsers = $this->userModel->count();
        $activeUsers = $this->userModel->count(['is_active' => 1]);
        $totalRoles = $this->roleModel->count();
        
        // Get recent users
        $recentUsers = $this->userModel->raw(
            "SELECT u.*, r.role_name 
             FROM users u 
             INNER JOIN roles r ON u.role_id = r.id 
             ORDER BY u.created_at DESC 
             LIMIT 10"
        );

        $data = [
            'title' => 'Admin Dashboard',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ],
            'stats' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_roles' => $totalRoles
            ],
            'recent_users' => $recentUsers
        ];

        $this->view('admin/dashboard', $data, 'dashboard');
    }

    /**
     * Manage users
     * 
     * @return void
     */
    public function users(): void
    {
        $users = $this->userModel->raw(
            "SELECT u.*, r.role_name 
             FROM users u 
             INNER JOIN roles r ON u.role_id = r.id 
             ORDER BY u.created_at DESC"
        );

        $data = [
            'title' => 'Manage Users',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ],
            'users' => $users
        ];

        $this->view('admin/users', $data, 'dashboard');
    }

    /**
     * Manage roles
     * 
     * @return void
     */
    public function roles(): void
    {
        $roles = $this->roleModel->findAll();

        $data = [
            'title' => 'Manage Roles',
            'user' => [
                'name' => $_SESSION['full_name'],
                'role' => $_SESSION['role_name']
            ],
            'roles' => $roles
        ];

        $this->view('admin/roles', $data, 'dashboard');
    }
}
