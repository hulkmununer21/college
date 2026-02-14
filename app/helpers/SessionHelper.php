<?php
/**
 * Session Helper Class
 * 
 * Handles secure session management
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class SessionHelper
{
    /**
     * Create user session
     * 
     * @param array $user User data with role
     * @param bool $rememberMe Whether to remember user
     * @return void
     */
    public function createSession(array $user, bool $rememberMe = false): void
    {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['full_name'] = trim($user['first_name'] . ' ' . ($user['middle_name'] ?? '') . ' ' . $user['last_name']);
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['role_code'] = $user['role_code'];
        $_SESSION['permissions'] = json_decode($user['permissions'], true);
        $_SESSION['is_logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Store session in database
        $this->storeSessionInDatabase($user['id']);

        // Set remember me cookie if requested
        if ($rememberMe) {
            $this->setRememberMeCookie($user['id'], $user['username']);
        }
    }

    /**
     * Destroy user session
     * 
     * @return void
     */
    public function destroySession(): void
    {
        // Remove session from database
        if (isset($_SESSION['user_id'])) {
            $this->removeSessionFromDatabase();
        }

        // Clear remember me cookie
        $this->clearRememberMeCookie();

        // Unset all session variables
        $_SESSION = [];

        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy session
        session_destroy();

        // Start new session
        session_start();
    }

    /**
     * Check if session is valid
     * 
     * @return bool
     */
    public function isSessionValid(): bool
    {
        // Check if user is logged in
        if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            $inactiveTime = time() - $_SESSION['last_activity'];
            
            if ($inactiveTime > SESSION_LIFETIME) {
                $this->destroySession();
                return false;
            }
        }

        // Update last activity
        $_SESSION['last_activity'] = time();

        return true;
    }

    /**
     * Check if user has permission
     * 
     * @param string $permission Permission to check
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        if (!isset($_SESSION['permissions'])) {
            return false;
        }

        $permissions = $_SESSION['permissions'];

        // Super admin has all permissions
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Get current user ID
     * 
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Get current user role
     * 
     * @return string|null
     */
    public function getUserRole(): ?string
    {
        return $_SESSION['role_code'] ?? null;
    }

    /**
     * Store session in database
     * 
     * @param int $userId
     * @return void
     */
    private function storeSessionInDatabase(int $userId): void
    {
        $db = Database::getInstance();
        
        // Delete old sessions for this user
        $deleteSql = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $db->query($deleteSql)->bind(':user_id', $userId)->execute();

        // Insert new session
        $insertSql = "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, last_activity) 
                      VALUES (:user_id, :session_id, :ip_address, :user_agent, NOW())";
        
        $db->query($insertSql)
           ->bind(':user_id', $userId)
           ->bind(':session_id', session_id())
           ->bind(':ip_address', $_SERVER['REMOTE_ADDR'])
           ->bind(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '')
           ->execute();
    }

    /**
     * Remove session from database
     * 
     * @return void
     */
    private function removeSessionFromDatabase(): void
    {
        $db = Database::getInstance();
        $sql = "DELETE FROM user_sessions WHERE session_id = :session_id";
        $db->query($sql)->bind(':session_id', session_id())->execute();
    }

    /**
     * Set remember me cookie
     * 
     * @param int $userId
     * @param string $username
     * @return void
     */
    private function setRememberMeCookie(int $userId, string $username): void
    {
        $token = bin2hex(random_bytes(32));
        $cookieValue = $userId . ':' . $token;
        
        // Hash the token for storage
        $hashedToken = password_hash($token, PASSWORD_BCRYPT);
        
        // Store hashed token in database (you may want to create a remember_tokens table)
        $db = Database::getInstance();
        $sql = "UPDATE users SET remember_token = :token WHERE id = :user_id";
        $db->query($sql)
           ->bind(':token', $hashedToken)
           ->bind(':user_id', $userId)
           ->execute();
        
        // Set cookie for 30 days
        setcookie('remember_me', $cookieValue, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    }

    /**
     * Clear remember me cookie
     * 
     * @return void
     */
    private function clearRememberMeCookie(): void
    {
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
            unset($_COOKIE['remember_me']);
        }
    }

    /**
     * Check and restore remember me session
     * 
     * @return bool
     */
    public function checkRememberMe(): bool
    {
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }

        $cookieValue = $_COOKIE['remember_me'];
        $parts = explode(':', $cookieValue);

        if (count($parts) !== 2) {
            $this->clearRememberMeCookie();
            return false;
        }

        list($userId, $token) = $parts;

        // Get user from database
        $db = Database::getInstance();
        $sql = "SELECT u.*, r.role_name, r.role_code, r.permissions 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.id = :user_id AND u.is_active = 1";
        
        $user = $db->query($sql)->bind(':user_id', $userId)->fetch();

        if (!$user || !isset($user['remember_token'])) {
            $this->clearRememberMeCookie();
            return false;
        }

        // Verify token
        if (!password_verify($token, $user['remember_token'])) {
            $this->clearRememberMeCookie();
            return false;
        }

        // Create session
        $this->createSession($user, true);

        return true;
    }
}
