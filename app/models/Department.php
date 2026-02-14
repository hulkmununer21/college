<?php
declare(strict_types=1);

/**
 * Department Model
 * Handles all database operations related to departments
 */
class Department extends Model {
    protected string $table = 'departments';
    protected array $fillable = ['faculty_id', 'name', 'code', 'description', 'hod_id', 'is_active'];

    /**
     * Find department by code
     */
    public function findByCode(string $code): ?array {
        return $this->where('code', $code)->fetch();
    }

    /**
     * Get all active departments
     */
    public function getActiveDepartments(): array {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->fetchAll();
    }

    /**
     * Get departments by faculty
     */
    public function getDepartmentsByFaculty(int $facultyId, bool $activeOnly = false): array {
        $this->where('faculty_id', $facultyId);
        
        if ($activeOnly) {
            $this->where('is_active', 1);
        }
        
        return $this->orderBy('name', 'ASC')->fetchAll();
    }

    /**
     * Get department with full details (faculty and HOD)
     */
    public function getDepartmentWithDetails(int $id): ?array {
        $sql = "SELECT * FROM v_departments_full WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Get all departments with full details
     */
    public function getAllDepartmentsWithDetails(): array {
        $sql = "SELECT * FROM v_departments_full ORDER BY faculty_name, department_name";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Get department statistics
     */
    public function getDepartmentStats(int $departmentId): array {
        $sql = "SELECT 
                    d.id,
                    d.name,
                    d.code,
                    f.name AS faculty_name,
                    COUNT(DISTINCT CASE WHEN u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT') THEN u.id END) AS student_count,
                    COUNT(DISTINCT CASE WHEN u.role_id = (SELECT id FROM roles WHERE code = 'LECTURER') THEN u.id END) AS lecturer_count,
                    COUNT(DISTINCT u.level_id) AS level_count
                FROM departments d
                LEFT JOIN faculties f ON d.faculty_id = f.id
                LEFT JOIN users u ON d.id = u.department_id
                WHERE d.id = :department_id
                GROUP BY d.id";
        
        $this->db->query($sql);
        $this->db->bind(':department_id', $departmentId);
        return $this->db->fetch() ?? [];
    }

    /**
     * Set HOD for department
     */
    public function setHod(int $departmentId, ?int $hodId): bool {
        return $this->update($departmentId, ['hod_id' => $hodId]);
    }

    /**
     * Toggle department active status
     */
    public function toggleActive(int $id): bool {
        $department = $this->find($id);
        if (!$department) {
            return false;
        }
        
        $newStatus = $department['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if department code exists (for validation)
     */
    public function codeExists(string $code, ?int $excludeId = null): bool {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE code = :code AND id != :exclude_id";
            $this->db->query($sql);
            $this->db->bind(':code', $code);
            $this->db->bind(':exclude_id', $excludeId);
            $result = $this->db->fetch();
            return $result['count'] > 0;
        }
        
        return $this->exists('code', $code);
    }

    /**
     * Check if department name exists in faculty (for validation)
     */
    public function nameExistsInFaculty(int $facultyId, string $name, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE faculty_id = :faculty_id 
                AND name = :name";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':faculty_id', $facultyId);
        $this->db->bind(':name', $name);
        
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        $result = $this->db->fetch();
        return $result['count'] > 0;
    }

    /**
     * Get students in department
     */
    public function getStudents(int $departmentId, ?int $levelId = null): array {
        $sql = "SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.matric_number,
                    l.name AS level_name,
                    u.is_active
                FROM users u
                LEFT JOIN levels l ON u.level_id = l.id
                WHERE u.department_id = :department_id
                AND u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT')";
        
        if ($levelId) {
            $sql .= " AND u.level_id = :level_id";
        }
        
        $sql .= " ORDER BY u.matric_number ASC";
        
        $this->db->query($sql);
        $this->db->bind(':department_id', $departmentId);
        
        if ($levelId) {
            $this->db->bind(':level_id', $levelId);
        }
        
        return $this->db->fetchAll();
    }

    /**
     * Get lecturers in department
     */
    public function getLecturers(int $departmentId): array {
        $sql = "SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.staff_id,
                    u.phone,
                    u.is_active
                FROM users u
                WHERE u.department_id = :department_id
                AND u.role_id IN (SELECT id FROM roles WHERE code IN ('LECTURER', 'HOD'))
                ORDER BY u.last_name ASC";
        
        $this->db->query($sql);
        $this->db->bind(':department_id', $departmentId);
        return $this->db->fetchAll();
    }

    /**
     * Search departments
     */
    public function search(string $searchTerm, ?int $facultyId = null): array {
        $sql = "SELECT 
                    d.*,
                    f.name AS faculty_name,
                    CONCAT(u.first_name, ' ', u.last_name) AS hod_name
                FROM departments d
                LEFT JOIN faculties f ON d.faculty_id = f.id
                LEFT JOIN users u ON d.hod_id = u.id
                WHERE (d.name LIKE :search 
                   OR d.code LIKE :search 
                   OR d.description LIKE :search
                   OR f.name LIKE :search)";
        
        if ($facultyId) {
            $sql .= " AND d.faculty_id = :faculty_id";
        }
        
        $sql .= " ORDER BY f.name, d.name ASC";
        
        $this->db->query($sql);
        $this->db->bind(':search', "%{$searchTerm}%");
        
        if ($facultyId) {
            $this->db->bind(':faculty_id', $facultyId);
        }
        
        return $this->db->fetchAll();
    }

    /**
     * Get department count by faculty
     */
    public function getCountByFaculty(int $facultyId): int {
        return $this->where('faculty_id', $facultyId)->count();
    }
}
