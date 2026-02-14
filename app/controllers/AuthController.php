<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication, registration, password reset, and email verification
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class AuthController extends Controller
{
    private $userModel;
    private $sessionHelper;
    private $emailHelper;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->sessionHelper = new SessionHelper();
        $this->emailHelper = new EmailHelper();
    }

    /**
     * Display login form
     * 
     * @return void
     */
    public function login(): void
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/login', $data, null);
    }

    /**
     * Process login form
     * 
     * @return void
     */
    public function processLogin(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCSRFToken($this->post('csrf_token'))) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/login');
            return;
        }

        // Sanitize input
        $username = $this->sanitize($this->post('username'));
        $password = $this->post('password');
        $rememberMe = $this->post('remember_me') ? true : false;

        // Validate input
        if (empty($username) || empty($password)) {
            $this->flash('error', 'Please enter both username and password');
            $this->redirect('auth/login');
            return;
        }

        // Get user by username or email
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $user = $this->userModel->findByEmail($username);
        }

        // Check if user exists
        if (!$user) {
            $this->flash('error', 'Invalid login credentials');
            $this->redirect('auth/login');
            return;
        }

        // Check if account is locked
        if ($this->userModel->isAccountLocked($user['id'])) {
            $this->flash('error', 'Your account has been locked due to multiple failed login attempts. Please try again later.');
            $this->redirect('auth/login');
            return;
        }

        // Check if account is active
        if (!$user['is_active']) {
            $this->flash('error', 'Your account has been deactivated. Please contact the administrator.');
            $this->redirect('auth/login');
            return;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Increment login attempts
            $this->userModel->incrementLoginAttempts($user['id']);
            
            // Lock account after 5 failed attempts
            $loginAttempts = $user['login_attempts'] + 1;
            if ($loginAttempts >= 5) {
                $this->userModel->lockAccount($user['id'], 30); // Lock for 30 minutes
                $this->flash('error', 'Too many failed login attempts. Your account has been locked for 30 minutes.');
            } else {
                $this->flash('error', 'Invalid login credentials');
            }
            
            $this->redirect('auth/login');
            return;
        }

        // Get user with role details
        $userWithRole = $this->userModel->getUserWithRole($user['id']);

        // Create session
        $this->sessionHelper->createSession($userWithRole, $rememberMe);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log audit trail
        $this->logAudit($user['id'], 'USER_LOGIN', 'users', $user['id']);

        // Flash success message
        $this->flash('success', 'Welcome back, ' . $userWithRole['first_name'] . '!');

        // Redirect to appropriate dashboard
        $this->redirectToDashboard();
    }

    /**
     * Display admin login form
     * 
     * @return void
     */
    public function adminLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Admin Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/admin-login', $data, null);
    }

    /**
     * Process admin login
     * 
     * @return void
     */
    public function processAdminLogin(): void
    {
        $this->processRoleLogin(['SUPER_ADMIN', 'ADMISSION_OFFICER'], 'admin-login', 'Admin');
    }

    /**
     * Display student login form
     * 
     * @return void
     */
    public function studentLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Student Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/student-login', $data, null);
    }

    /**
     * Process student login
     * 
     * @return void
     */
    public function processStudentLogin(): void
    {
        $this->processRoleLogin(['STUDENT'], 'student-login', 'Student');
    }

    /**
     * Display lecturer login form
     * 
     * @return void
     */
    public function lecturerLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Lecturer Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/lecturer-login', $data, null);
    }

    /**
     * Process lecturer login
     * 
     * @return void
     */
    public function processLecturerLogin(): void
    {
        $this->processRoleLogin(['LECTURER'], 'lecturer-login', 'Lecturer');
    }

    /**
     * Display HOD login form
     * 
     * @return void
     */
    public function hodLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'HOD Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/hod-login', $data, null);
    }

    /**
     * Process HOD login
     * 
     * @return void
     */
    public function processHodLogin(): void
    {
        $this->processRoleLogin(['HOD'], 'hod-login', 'HOD');
    }

    /**
     * Display bursar login form
     * 
     * @return void
     */
    public function bursarLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Bursar Login',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/bursar-login', $data, null);
    }

    /**
     * Process bursar login
     * 
     * @return void
     */
    public function processBursarLogin(): void
    {
        $this->processRoleLogin(['BURSAR'], 'bursar-login', 'Bursar');
    }

    /**
     * Process role-specific login
     * 
     * @param array $allowedRoles Array of allowed role codes
     * @param string $loginRoute Route to redirect on error
     * @param string $roleLabel Human-readable role label
     * @return void
     */
    private function processRoleLogin(array $allowedRoles, string $loginRoute, string $roleLabel): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCSRFToken($this->post('csrf_token'))) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Sanitize input
        $username = $this->sanitize($this->post('username'));
        $password = $this->post('password');
        $rememberMe = $this->post('remember_me') ? true : false;

        // Validate input
        if (empty($username) || empty($password)) {
            $this->flash('error', 'Please enter both username and password');
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Get user by username or email
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            $user = $this->userModel->findByEmail($username);
        }

        // Check if user exists
        if (!$user) {
            $this->flash('error', 'Invalid login credentials');
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Get user with role details to check role
        $userWithRole = $this->userModel->getUserWithRole($user['id']);
        
        // Validate user has correct role for this portal
        if (!in_array($userWithRole['role_code'], $allowedRoles)) {
            $this->flash('error', "You don't have access to the {$roleLabel} portal. Please use the correct login page for your role.");
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Check if account is locked
        if ($this->userModel->isAccountLocked($user['id'])) {
            $this->flash('error', 'Your account has been locked due to multiple failed login attempts. Please try again later.');
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Check if account is active
        if (!$user['is_active']) {
            $this->flash('error', 'Your account has been deactivated. Please contact the administrator.');
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            // Increment login attempts
            $this->userModel->incrementLoginAttempts($user['id']);
            
            // Lock account after 5 failed attempts
            $loginAttempts = $user['login_attempts'] + 1;
            if ($loginAttempts >= 5) {
                $this->userModel->lockAccount($user['id'], 30); // Lock for 30 minutes
                $this->flash('error', 'Too many failed login attempts. Your account has been locked for 30 minutes.');
            } else {
                $this->flash('error', 'Invalid login credentials');
            }
            
            $this->redirect('auth/' . $loginRoute);
            return;
        }

        // Create session
        $this->sessionHelper->createSession($userWithRole, $rememberMe);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log audit trail
        $this->logAudit($user['id'], 'USER_LOGIN', 'users', $user['id']);

        // Flash success message
        $this->flash('success', 'Welcome back, ' . $userWithRole['first_name'] . '!');

        // Redirect to appropriate dashboard
        $this->redirectToDashboard();
    }

    /**
     * Display registration form
     * 
     * @return void
     */
    public function register(): void
    {
        // Redirect if already logged in
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        $data = [
            'title' => 'Register',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/register', $data, null);
    }

    /**
     * Process registration form
     * 
     * @return void
     */
    public function processRegister(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/register');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCSRFToken($this->post('csrf_token'))) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/register');
            return;
        }

        // Sanitize input
        $username = $this->sanitize($this->post('username'));
        $email = $this->sanitize($this->post('email'));
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        $firstName = $this->sanitize($this->post('first_name'));
        $lastName = $this->sanitize($this->post('last_name'));

        // Validate input
        $errors = $this->validateRegistration($username, $email, $password, $confirmPassword, $firstName, $lastName);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->flash('error', $error);
            }
            $this->redirect('auth/register');
            return;
        }

        // Check if username already exists
        if ($this->userModel->findByUsername($username)) {
            $this->flash('error', 'Username already exists');
            $this->redirect('auth/register');
            return;
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            $this->flash('error', 'Email already exists');
            $this->redirect('auth/register');
            return;
        }

        // Generate email verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Create user data
        $userData = [
            'role_id' => 6, // Student role by default (change as needed)
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email_verification_token' => $verificationToken,
            'is_active' => 1,
            'is_email_verified' => 0
        ];

        // Create user
        try {
            $userId = $this->userModel->createUser($userData);

            // Send verification email
            $this->emailHelper->sendVerificationEmail($email, $firstName, $verificationToken);

            // Log audit trail
            $this->logAudit($userId, 'USER_REGISTRATION', 'users', $userId);

            $this->flash('success', 'Registration successful! Please check your email to verify your account.');
            $this->redirect('auth/login');
        } catch (Exception $e) {
            $this->flash('error', 'Registration failed. Please try again.');
            $this->redirect('auth/register');
        }
    }

    /**
     * Verify email address
     * 
     * @param string $token Verification token
     * @return void
     */
    public function verifyEmail(string $token = ''): void
    {
        if (empty($token)) {
            $this->flash('error', 'Invalid verification token');
            $this->redirect('auth/login');
            return;
        }

        // Find user by verification token
        $user = $this->userModel->findWhere(['email_verification_token' => $token]);

        if (!$user) {
            $this->flash('error', 'Invalid or expired verification token');
            $this->redirect('auth/login');
            return;
        }

        // Update user as verified
        $this->userModel->update($user['id'], [
            'is_email_verified' => 1,
            'email_verification_token' => null
        ]);

        // Log audit trail
        $this->logAudit($user['id'], 'EMAIL_VERIFIED', 'users', $user['id']);

        $this->flash('success', 'Email verified successfully! You can now login.');
        $this->redirect('auth/login');
    }

    /**
     * Display forgot password form
     * 
     * @return void
     */
    public function forgotPassword(): void
    {
        $data = [
            'title' => 'Forgot Password',
            'csrf_token' => $this->generateCSRFToken()
        ];

        $this->view('auth/forgot-password', $data, null);
    }

    /**
     * Process forgot password form
     * 
     * @return void
     */
    public function processForgotPassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/forgot-password');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCSRFToken($this->post('csrf_token'))) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/forgot-password');
            return;
        }

        $email = $this->sanitize($this->post('email'));

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address');
            $this->redirect('auth/forgot-password');
            return;
        }

        // Find user by email
        $user = $this->userModel->findByEmail($email);

        // Always show success message for security (don't reveal if email exists)
        $this->flash('success', 'If an account exists with this email, you will receive password reset instructions.');

        if ($user) {
            // Generate password reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Update user with reset token
            $this->userModel->update($user['id'], [
                'password_reset_token' => $resetToken,
                'password_reset_expires' => $resetExpires
            ]);

            // Send password reset email
            $this->emailHelper->sendPasswordResetEmail($email, $user['first_name'], $resetToken);

            // Log audit trail
            $this->logAudit($user['id'], 'PASSWORD_RESET_REQUEST', 'users', $user['id']);
        }

        $this->redirect('auth/forgot-password');
    }

    /**
     * Display reset password form
     * 
     * @param string $token Reset token
     * @return void
     */
    public function resetPassword(string $token = ''): void
    {
        if (empty($token)) {
            $this->flash('error', 'Invalid reset token');
            $this->redirect('auth/login');
            return;
        }

        // Verify token exists and not expired
        $user = $this->userModel->findWhere(['password_reset_token' => $token]);

        if (!$user || strtotime($user['password_reset_expires']) < time()) {
            $this->flash('error', 'Invalid or expired reset token');
            $this->redirect('auth/forgot-password');
            return;
        }

        $data = [
            'title' => 'Reset Password',
            'csrf_token' => $this->generateCSRFToken(),
            'token' => $token
        ];

        $this->view('auth/reset-password', $data, null);
    }

    /**
     * Process reset password form
     * 
     * @return void
     */
    public function processResetPassword(): void
    {
        if (!$this->isPost()) {
            $this->redirect('auth/login');
            return;
        }

        // Verify CSRF token
        if (!$this->verifyCSRFToken($this->post('csrf_token'))) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('auth/login');
            return;
        }

        $token = $this->post('token');
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');

        // Validate passwords
        if (empty($password) || empty($confirmPassword)) {
            $this->flash('error', 'Please enter and confirm your new password');
            $this->redirect('auth/reset-password/' . $token);
            return;
        }

        if ($password !== $confirmPassword) {
            $this->flash('error', 'Passwords do not match');
            $this->redirect('auth/reset-password/' . $token);
            return;
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters long');
            $this->redirect('auth/reset-password/' . $token);
            return;
        }

        // Find user by token
        $user = $this->userModel->findWhere(['password_reset_token' => $token]);

        if (!$user || strtotime($user['password_reset_expires']) < time()) {
            $this->flash('error', 'Invalid or expired reset token');
            $this->redirect('auth/forgot-password');
            return;
        }

        // Update password
        $this->userModel->updatePassword($user['id'], $password);

        // Log audit trail
        $this->logAudit($user['id'], 'PASSWORD_RESET', 'users', $user['id']);

        $this->flash('success', 'Password reset successfully! You can now login with your new password.');
        $this->redirect('auth/login');
    }

    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        // Log audit trail
        if ($userId) {
            $this->logAudit($userId, 'USER_LOGOUT', 'users', $userId);
        }

        // Destroy session
        $this->sessionHelper->destroySession();

        $this->flash('success', 'You have been logged out successfully');
        $this->redirect('auth/login');
    }

    /**
     * Validate registration data
     * 
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @param string $firstName
     * @param string $lastName
     * @return array Validation errors
     */
    private function validateRegistration(string $username, string $email, string $password, string $confirmPassword, string $firstName, string $lastName): array
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 4) {
            $errors[] = 'Username must be at least 4 characters long';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        if (empty($firstName)) {
            $errors[] = 'First name is required';
        }

        if (empty($lastName)) {
            $errors[] = 'Last name is required';
        }

        return $errors;
    }

    /**
     * Redirect to appropriate dashboard based on user role
     * 
     * @return void
     */
    private function redirectToDashboard(): void
    {
        $roleCode = $_SESSION['role_code'] ?? '';

        switch ($roleCode) {
            case 'SUPER_ADMIN':
                $this->redirect('admin/dashboard');
                break;
            case 'ADMISSION_OFFICER':
                $this->redirect('admission/dashboard');
                break;
            case 'BURSAR':
                $this->redirect('bursar/dashboard');
                break;
            case 'HOD':
                $this->redirect('hod/dashboard');
                break;
            case 'LECTURER':
                $this->redirect('lecturer/dashboard');
                break;
            case 'STUDENT':
                $this->redirect('student/dashboard');
                break;
            default:
                $this->redirect('dashboard');
                break;
        }
    }

    /**
     * Log audit trail
     * 
     * @param int $userId
     * @param string $action
     * @param string $entityType
     * @param int $entityId
     * @return void
     */
    private function logAudit(int $userId, string $action, string $entityType, int $entityId): void
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, user_agent, created_at) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :ip_address, :user_agent, NOW())";
        
        $db->query($sql)
           ->bind(':user_id', $userId)
           ->bind(':action', $action)
           ->bind(':entity_type', $entityType)
           ->bind(':entity_id', $entityId)
           ->bind(':ip_address', $_SERVER['REMOTE_ADDR'])
           ->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '')
           ->execute();
    }
}
