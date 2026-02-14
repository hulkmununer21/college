<?php
/**
 * User Model
 * 
 * Handles all user-related database operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class User extends Model
{
    /**
     * Table name
     */
    protected string $table = 'users';

    /**
     * Primary key
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable columns
     */
    protected array $fillable = [
        'role_id',
        'username',
        'email',
        'password_hash',
        'first_name',
        'middle_name',
        'last_name',
        'phone_number',
        'date_of_birth',
        'gender',
        'address',
        'profile_picture',
        'is_active',
        'is_email_verified'
    ];

    /**
     * Timestamps enabled
     */
    protected bool $timestamps = true;

    /**
     * Get user by username
     * 
     * @param string $username
     * @return mixed
     */
    public function findByUsername(string $username)
    {
        return $this->findWhere(['username' => $username]);
    }

    /**
     * Get user by email
     * 
     * @param string $email
     * @return mixed
     */
    public function findByEmail(string $email)
    {
        return $this->findWhere(['email' => $email]);
    }

    /**
     * Get user with role details
     * 
     * @param int $userId
     * @return mixed
     */
    public function getUserWithRole(int $userId)
    {
        $sql = "SELECT u.*, r.role_name, r.role_code, r.permissions 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.id = :id 
                LIMIT 1";
        
        return $this->db->query($sql)->bind(':id', $userId)->fetch();
    }

    /**
     * Verify user password
     * 
     * @param string $username
     * @param string $password
     * @return mixed User data or false
     */
    public function verifyPassword(string $username, string $password)
    {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    /**
     * Create new user with hashed password
     * 
     * @param array $data
     * @return string User ID
     */
    public function createUser(array $data): string
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }

        return $this->insert($data);
    }

    /**
     * Update user password
     * 
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $sql = "UPDATE {$this->table} 
                SET password_hash = :password_hash, 
                    password_reset_token = NULL, 
                    password_reset_expires = NULL,
                    updated_at = NOW()
                WHERE {$this->primaryKey} = :id";
        
        return $this->db->query($sql)
            ->bind(':password_hash', $passwordHash)
            ->bind(':id', $userId)
            ->execute();
    }

    /**
     * Update last login timestamp
     * 
     * @param int $userId
     * @return bool
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET last_login = NOW(), 
                    login_attempts = 0, 
                    locked_until = NULL 
                WHERE {$this->primaryKey} = :id";
        
        return $this->db->query($sql)->bind(':id', $userId)->execute();
    }

    /**
     * Increment login attempts
     * 
     * @param int $userId
     * @return bool
     */
    public function incrementLoginAttempts(int $userId): bool
    {
        $sql = "UPDATE {$this->table} 
                SET login_attempts = login_attempts + 1 
                WHERE {$this->primaryKey} = :id";
        
        return $this->db->query($sql)->bind(':id', $userId)->execute();
    }

    /**
     * Lock user account
     * 
     * @param int $userId
     * @param int $minutes
     * @return bool
     */
    public function lockAccount(int $userId, int $minutes = 30): bool
    {
        $sql = "UPDATE {$this->table} 
                SET locked_until = DATE_ADD(NOW(), INTERVAL :minutes MINUTE) 
                WHERE {$this->primaryKey} = :id";
        
        return $this->db->query($sql)
            ->bind(':minutes', $minutes)
            ->bind(':id', $userId)
            ->execute();
    }

    /**
     * Check if account is locked
     * 
     * @param int $userId
     * @return bool
     */
    public function isAccountLocked(int $userId): bool
    {
        $sql = "SELECT locked_until FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $result = $this->db->query($sql)->bind(':id', $userId)->fetch();
        
        if (!$result || !$result['locked_until']) {
            return false;
        }

        return strtotime($result['locked_until']) > time();
    }

    /**
     * Get users by role
     * 
     * @param string $roleCode
     * @return array
     */
    public function getUsersByRole(string $roleCode): array
    {
        $sql = "SELECT u.* 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE r.role_code = :role_code 
                AND u.is_active = 1";
        
        return $this->db->query($sql)->bind(':role_code', $roleCode)->fetchAll();
    }

    /**
     * Search users
     * 
     * @param string $searchTerm
     * @return array
     */
    public function searchUsers(string $searchTerm): array
    {
        $searchTerm = '%' . $searchTerm . '%';
        
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.first_name LIKE :search 
                OR u.last_name LIKE :search 
                OR u.email LIKE :search 
                OR u.username LIKE :search";
        
        return $this->db->query($sql)->bind(':search', $searchTerm)->fetchAll();
    }

    /**
     * Get active users count
     * 
     * @return int
     */
    public function getActiveUsersCount(): int
    {
        return $this->count(['is_active' => 1]);
    }
}
