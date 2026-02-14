<?php
/**
 * Role Model
 * 
 * Handles role-related database operations
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class Role extends Model
{
    /**
     * Table name
     */
    protected string $table = 'roles';

    /**
     * Primary key
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable columns
     */
    protected array $fillable = [
        'role_name',
        'role_code',
        'description',
        'permissions',
        'is_active'
    ];

    /**
     * Timestamps enabled
     */
    protected bool $timestamps = true;

    /**
     * Get role by code
     * 
     * @param string $roleCode
     * @return mixed
     */
    public function findByCode(string $roleCode)
    {
        return $this->findWhere(['role_code' => $roleCode]);
    }

    /**
     * Get all active roles
     * 
     * @return array
     */
    public function getActiveRoles(): array
    {
        return $this->where(['is_active' => 1]);
    }

    /**
     * Get role permissions
     * 
     * @param int $roleId
     * @return array
     */
    public function getPermissions(int $roleId): array
    {
        $role = $this->find($roleId);
        
        if (!$role || empty($role['permissions'])) {
            return [];
        }

        return json_decode($role['permissions'], true);
    }

    /**
     * Check if role has permission
     * 
     * @param int $roleId
     * @param string $permission
     * @return bool
     */
    public function hasPermission(int $roleId, string $permission): bool
    {
        $permissions = $this->getPermissions($roleId);

        // Super admin has all permissions
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Add permission to role
     * 
     * @param int $roleId
     * @param string $permission
     * @return bool
     */
    public function addPermission(int $roleId, string $permission): bool
    {
        $permissions = $this->getPermissions($roleId);

        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            
            return $this->update($roleId, [
                'permissions' => json_encode($permissions)
            ]);
        }

        return true;
    }

    /**
     * Remove permission from role
     * 
     * @param int $roleId
     * @param string $permission
     * @return bool
     */
    public function removePermission(int $roleId, string $permission): bool
    {
        $permissions = $this->getPermissions($roleId);
        $key = array_search($permission, $permissions);

        if ($key !== false) {
            unset($permissions[$key]);
            
            return $this->update($roleId, [
                'permissions' => json_encode(array_values($permissions))
            ]);
        }

        return true;
    }

    /**
     * Get users count by role
     * 
     * @param int $roleId
     * @return int
     */
    public function getUsersCount(int $roleId): int
    {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role_id = :role_id AND is_active = 1";
        $result = $this->db->query($sql)->bind(':role_id', $roleId)->fetch();
        return (int) $result['total'];
    }
}
