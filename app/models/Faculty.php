<?php
declare(strict_types=1);

/**
 * Faculty Model
 * Handles all database operations related to faculties
 */
class Faculty extends Model {
    protected string $table = 'faculties';
    protected array $fillable = ['name', 'code', 'description', 'dean_id', 'is_active'];

    /**
     * Find faculty by code
     */
    public function findByCode(string $code): ?array {
        return $this->where('code', $code)->fetch();
    }

    /**
     * Get all active faculties
     */
    public function getActiveFaculties(): array {
        return $this->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->fetchAll();
    }

    /**
     * Get faculty with dean information
     */
    public function getFacultyWithDean(int $id): ?array {
        $sql = "SELECT 
                    f.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS dean_name,
                    u.email AS dean_email,
                    u.phone AS dean_phone
                FROM faculties f
                LEFT JOIN users u ON f.dean_id = u.id
                WHERE f.id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Get all faculties with dean information
     */
    public function getAllFacultiesWithDeans(): array {
        $sql = "SELECT 
                    f.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS dean_name,
                    u.email AS dean_email
                FROM faculties f
                LEFT JOIN users u ON f.dean_id = u.id
                ORDER BY f.name ASC";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Get faculty statistics
     */
    public function getFacultyStats(int $facultyId): array {
        $sql = "SELECT 
                    f.id,
                    f.name,
                    f.code,
                    COUNT(DISTINCT d.id) AS department_count,
                    COUNT(DISTINCT CASE WHEN u.role_id = (SELECT id FROM roles WHERE code = 'STUDENT') THEN u.id END) AS student_count,
                    COUNT(DISTINCT CASE WHEN u.role_id = (SELECT id FROM roles WHERE code = 'LECTURER') THEN u.id END) AS lecturer_count
                FROM faculties f
                LEFT JOIN departments d ON f.id = d.faculty_id
                LEFT JOIN users u ON d.id = u.department_id
                WHERE f.id = :faculty_id
                GROUP BY f.id";
        
        $this->db->query($sql);
        $this->db->bind(':faculty_id', $facultyId);
        return $this->db->fetch() ?? [];
    }

    /**
     * Get all faculties with statistics
     */
    public function getAllFacultiesWithStats(): array {
        $sql = "SELECT 
                    f.id,
                    f.name,
                    f.code,
                    f.description,
                    f.is_active,
                    CONCAT(u.first_name, ' ', u.last_name) AS dean_name,
                    COUNT(DISTINCT d.id) AS department_count,
                    COUNT(DISTINCT CASE WHEN usr.role_id = (SELECT id FROM roles WHERE code = 'STUDENT') THEN usr.id END) AS student_count,
                    COUNT(DISTINCT CASE WHEN usr.role_id = (SELECT id FROM roles WHERE code = 'LECTURER') THEN usr.id END) AS lecturer_count,
                    f.created_at
                FROM faculties f
                LEFT JOIN users u ON f.dean_id = u.id
                LEFT JOIN departments d ON f.id = d.faculty_id
                LEFT JOIN users usr ON d.id = usr.department_id
                GROUP BY f.id
                ORDER BY f.name ASC";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Set dean for faculty
     */
    public function setDean(int $facultyId, ?int $deanId): bool {
        return $this->update($facultyId, ['dean_id' => $deanId]);
    }

    /**
     * Toggle faculty active status
     */
    public function toggleActive(int $id): bool {
        $faculty = $this->find($id);
        if (!$faculty) {
            return false;
        }
        
        $newStatus = $faculty['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if faculty code exists (for validation)
     */
    public function codeExists(string $code, ?int $excludeId = null): bool {
        $this->where('code', $code);
        
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
     * Check if faculty name exists (for validation)
     */
    public function nameExists(string $name, ?int $excludeId = null): bool {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name AND id != :exclude_id";
            $this->db->query($sql);
            $this->db->bind(':name', $name);
            $this->db->bind(':exclude_id', $excludeId);
            $result = $this->db->fetch();
            return $result['count'] > 0;
        }
        
        return $this->exists('name', $name);
    }

    /**
     * Get departments in faculty
     */
    public function getDepartments(int $facultyId): array {
        $sql = "SELECT 
                    d.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS hod_name
                FROM departments d
                LEFT JOIN users u ON d.hod_id = u.id
                WHERE d.faculty_id = :faculty_id
                ORDER BY d.name ASC";
        
        $this->db->query($sql);
        $this->db->bind(':faculty_id', $facultyId);
        return $this->db->fetchAll();
    }

    /**
     * Search faculties
     */
    public function search(string $searchTerm): array {
        $sql = "SELECT 
                    f.*,
                    CONCAT(u.first_name, ' ', u.last_name) AS dean_name
                FROM faculties f
                LEFT JOIN users u ON f.dean_id = u.id
                WHERE f.name LIKE :search 
                   OR f.code LIKE :search 
                   OR f.description LIKE :search
                ORDER BY f.name ASC";
        
        $this->db->query($sql);
        $this->db->bind(':search', "%{$searchTerm}%");
        return $this->db->fetchAll();
    }
}
