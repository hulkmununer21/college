<?php
/**
 * Base Controller Class
 * 
 * Parent class for all controllers
 * Provides common functionality for view rendering, data validation, and security
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class Controller
{
    /**
     * Load and render a view file
     * 
     * @param string $view View file name (without .php extension)
     * @param array $data Data to pass to the view
     * @param string|null $layout Layout file (null for no layout)
     * @return void
     */
    protected function view(string $view, array $data = [], ?string $layout = 'default'): void
    {
        // Extract data array to variables
        extract($data);

        // Check if view file exists
        $viewFile = VIEW_PATH . '/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View not found: " . $view);
        }

        // If layout is specified, load it with the view
        if ($layout !== null) {
            $layoutFile = VIEW_PATH . '/layouts/' . $layout . '.php';
            
            if (file_exists($layoutFile)) {
                // Start output buffering for content
                ob_start();
                require_once $viewFile;
                $content = ob_get_clean();
                
                // Require layout
                require_once $layoutFile;
            } else {
                // Layout not found, load view directly
                require_once $viewFile;
            }
        } else {
            // No layout, load view directly
            require_once $viewFile;
        }
    }

    /**
     * Load a model
     * 
     * @param string $model Model name
     * @return object
     */
    protected function model(string $model): object
    {
        $modelFile = APP_PATH . '/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }

        die("Model not found: " . $model);
    }

    /**
     * Redirect to another URL
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        // If URL doesn't start with http, prepend BASE_URL
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }

        header("Location: " . $url, true, $statusCode);
        exit;
    }

    /**
     * Return JSON response
     * 
     * @param mixed $data Data to encode as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function json($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if request method matches
     * 
     * @param string $method HTTP method (GET, POST, PUT, DELETE, etc.)
     * @return bool
     */
    protected function isMethod(string $method): bool
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }

    /**
     * Check if request is POST
     * 
     * @return bool
     */
    protected function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    /**
     * Check if request is GET
     * 
     * @return bool
     */
    protected function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    /**
     * Check if request is AJAX
     * 
     * @return bool
     */
    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get POST data with optional default value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data with optional default value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Sanitize input data
     * 
     * @param mixed $data
     * @return mixed
     */
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate CSRF token
     * 
     * @return string
     */
    protected function generateCSRFToken(): string
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[CSRF_TOKEN_TIME_NAME] = time();
        }

        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Verify CSRF token
     * 
     * @param string $token
     * @return bool
     */
    protected function verifyCSRFToken(?string $token): bool
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || !isset($_SESSION[CSRF_TOKEN_TIME_NAME])) {
            return false;
        }

        // Check if token has expired
        if (time() - $_SESSION[CSRF_TOKEN_TIME_NAME] > CSRF_TOKEN_EXPIRE) {
            unset($_SESSION[CSRF_TOKEN_NAME]);
            unset($_SESSION[CSRF_TOKEN_TIME_NAME]);
            return false;
        }

        // Verify token
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Flash message to session
     * 
     * @param string $type Type of message (success, error, warning, info)
     * @param string $message Message content
     * @return void
     */
    protected function flash(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = [];
        }

        $_SESSION['flash'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Require authentication (redirect to login if not authenticated)
     * 
     * @param string $redirectUrl URL to redirect to if not authenticated
     * @return void
     */
    protected function requireAuth(string $redirectUrl = 'auth/login'): void
    {
        if (!$this->isLoggedIn()) {
            $this->flash('error', 'Please login to access this page');
            $this->redirect($redirectUrl);
        }
    }

    /**
     * Check if user has specific role
     * 
     * @param string|array $roles Role(s) to check
     * @return bool
     */
    protected function hasRole($roles): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userRole = $_SESSION['role_code'] ?? null;

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }

    /**
     * Require specific role (redirect if user doesn't have role)
     * 
     * @param string|array $roles Role(s) required
     * @param string $redirectUrl URL to redirect to if unauthorized
     * @return void
     */
    protected function requireRole($roles, string $redirectUrl = ''): void
    {
        $this->requireAuth();

        if (!$this->hasRole($roles)) {
            $this->flash('error', 'You do not have permission to access this page');
            $this->redirect($redirectUrl ?: 'dashboard');
        }
    }
}
