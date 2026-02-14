<?php
/**
 * Router Class
 * 
 * Handles URL routing and dispatches requests to appropriate controllers
 * Supports clean URLs like: school.com/controller/method/param1/param2
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class Router
{
    /**
     * Current controller name
     */
    protected string $controller = 'Home';

    /**
     * Current controller instance
     */
    protected object $controllerInstance;

    /**
     * Current method name
     */
    protected string $method = 'index';

    /**
     * Parameters array
     */
    protected array $params = [];

    /**
     * Default controller
     */
    private string $defaultController = 'Home';

    /**
     * Default method
     */
    private string $defaultMethod = 'index';

    /**
     * Constructor - Parse URL and route the request
     */
    public function __construct()
    {
        $url = $this->parseUrl();

        // Check if controller exists
        if (!empty($url[0])) {
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        } else {
            // Use default controller
            $this->controller = $this->defaultController . 'Controller';
        }

        // Require the controller
        $controllerPath = APP_PATH . '/controllers/' . $this->controller . '.php';
        
        if (!file_exists($controllerPath)) {
            $this->handleError404();
            return;
        }

        require_once $controllerPath;

        // Instantiate controller
        if (!class_exists($this->controller)) {
            $this->handleError404();
            return;
        }

        $this->controllerInstance = new $this->controller;

        // Check if method exists
        if (isset($url[1])) {
            // Convert kebab-case to camelCase (e.g., process-login to processLogin)
            $methodName = $this->toCamelCase($url[1]);
            
            if (method_exists($this->controllerInstance, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            } else {
                $this->handleError404();
                return;
            }
        }

        // Get parameters
        $this->params = $url ? array_values($url) : [];

        // Call controller method with parameters
        try {
            call_user_func_array([$this->controllerInstance, $this->method], $this->params);
        } catch (Exception $e) {
            $this->handleError500($e);
        }
    }

    /**
     * Parse URL from query string
     * 
     * @return array
     */
    private function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }

        return [];
    }

    /**
     * Handle 404 Not Found errors
     * 
     * @return void
     */
    private function handleError404(): void
    {
        http_response_code(404);
        
        $errorController = APP_PATH . '/controllers/ErrorController.php';
        
        if (file_exists($errorController)) {
            require_once $errorController;
            $controller = new ErrorController();
            $controller->notFound();
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The page you are looking for does not exist.</p>";
        }
        exit;
    }

    /**
     * Handle 500 Internal Server Error
     * 
     * @param Exception $e
     * @return void
     */
    private function handleError500(Exception $e): void
    {
        http_response_code(500);
        
        error_log("Router Error: " . $e->getMessage());
        
        if (ini_get('display_errors') == 1) {
            echo "<h1>500 - Internal Server Error</h1>";
            echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
            echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
        } else {
            echo "<h1>500 - Internal Server Error</h1>";
            echo "<p>An error occurred. Please contact the administrator.</p>";
        }
        exit;
    }

    /**
     * Get current controller
     * 
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Get current method
     * 
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get parameters
     * 
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Convert kebab-case to camelCase
     * Example: process-login becomes processLogin
     * 
     * @param string $string
     * @return string
     */
    private function toCamelCase(string $string): string
    {
        // Convert to lowercase first
        $string = strtolower($string);
        
        // Replace hyphens with spaces, capitalize first letter of each word, then remove spaces
        $string = str_replace('-', ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        
        // Make sure first letter is lowercase
        return lcfirst($string);
    }
}
