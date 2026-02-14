<?php
declare(strict_types=1);

/**
 * Level Model
 * Handles all database operations related to academic levels
 */
class Level extends Model {
    protected string $table = 'levels';
    protected array $fillable = ['name', 'level_number', 'description', 'min_credit_units', 'max_credit_units', 'is_active'];

    /**
     * Find level by level number
     */
    public function findByLevelNumber(int $levelNumber): ?array {
        return $this->where('level_number', $levelNumber)->fetch();
    }

    /**
     * Get all active levels
     */
    public function getActiveLevels(): array {
        return $this->where('is_active', 1)
                    ->orderBy('level_number', 'ASC')
                    ->fetchAll();
    }

    /**
     * Get all levels ordered by level number
     */
    public function getAllLevelsOrdered(): array {
        return $this->orderBy('level_number', 'ASC')->fetchAll();
    }

    /**
     * Get level with student count
     */
    public function getLevelWithStats(int $id): ?array {
        $sql = "SELECT 
                    l.*,
                    COUNT(DISTINCT u.id) AS student_count,
                    COUNT(DISTINCT u.department_id) AS department_count
                FROM levels l
                LEFT JOIN users u ON l.id = u.level_id
                WHERE l.id = :id
                GROUP BY l.id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Get all levels with statistics
     */
    public function getAllLevelsWithStats(): array {
        $sql = "SELECT 
                    l.*,
                    COUNT(DISTINCT u.id) AS student_count
                FROM levels l
                LEFT JOIN users u ON l.id = u.level_id
                    AND u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT')
                GROUP BY l.id
                ORDER BY l.level_number ASC";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Get students in level
     */
    public function getStudents(int $levelId, ?int $departmentId = null): array {
        $sql = "SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.matric_number,
                    d.name AS department_name,
                    d.code AS department_code,
                    u.is_active
                FROM users u
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE u.level_id = :level_id
                AND u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT')";
        
        if ($departmentId) {
            $sql .= " AND u.department_id = :department_id";
        }
        
        $sql .= " ORDER BY u.matric_number ASC";
        
        $this->db->query($sql);
        $this->db->bind(':level_id', $levelId);
        
        if ($departmentId) {
            $this->db->bind(':department_id', $departmentId);
        }
        
        return $this->db->fetchAll();
    }

    /**
     * Toggle level active status
     */
    public function toggleActive(int $id): bool {
        $level = $this->find($id);
        if (!$level) {
            return false;
        }
        
        $newStatus = $level['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if level number exists (for validation)
     */
    public function levelNumberExists(int $levelNumber, ?int $excludeId = null): bool {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE level_number = :level_number AND id != :exclude_id";
            $this->db->query($sql);
            $this->db->bind(':level_number', $levelNumber);
            $this->db->bind(':exclude_id', $excludeId);
            $result = $this->db->fetch();
            return $result['count'] > 0;
        }
        
        return $this->exists('level_number', $levelNumber);
    }

    /**
     * Validate credit units for level
     */
    public function validateCreditUnits(int $levelId, int $creditUnits): array {
        $level = $this->find($levelId);
        
        if (!$level) {
            return [
                'valid' => false,
                'message' => 'Level not found'
            ];
        }
        
        if ($creditUnits < $level['min_credit_units']) {
            return [
                'valid' => false,
                'message' => "Minimum credit units for {$level['name']} is {$level['min_credit_units']}"
            ];
        }
        
        if ($creditUnits > $level['max_credit_units']) {
            return [
                'valid' => false,
                'message' => "Maximum credit units for {$level['name']} is {$level['max_credit_units']}"
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Credit units are valid'
        ];
    }

    /**
     * Get credit unit range for level
     */
    public function getCreditUnitRange(int $levelId): ?array {
        $level = $this->find($levelId);
        
        if (!$level) {
            return null;
        }
        
        return [
            'min' => $level['min_credit_units'],
            'max' => $level['max_credit_units']
        ];
    }
}
