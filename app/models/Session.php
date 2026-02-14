<?php
declare(strict_types=1);

/**
 * Session Model
 * Handles all database operations related to academic sessions
 */
class Session extends Model {
    protected string $table = 'sessions';
    protected array $fillable = ['name', 'start_year', 'end_year', 'start_date', 'end_date', 'is_current', 'is_active'];

    /**
     * Find session by name (e.g., "2025/2026")
     */
    public function findByName(string $name): ?array {
        return $this->where('name', $name)->fetch();
    }

    /**
     * Get current session
     */
    public function getCurrentSession(): ?array {
        return $this->where('is_current', 1)->fetch();
    }

    /**
     * Get all active sessions
     */
    public function getActiveSessions(): array {
        return $this->where('is_active', 1)
                    ->orderBy('start_year', 'DESC')
                    ->fetchAll();
    }

    /**
     * Get all sessions with semester count
     */
    public function getAllSessionsWithStats(): array {
        $sql = "SELECT 
                    s.*,
                    COUNT(sem.id) AS semester_count,
                    MAX(sem.is_current) AS has_current_semester
                FROM sessions s
                LEFT JOIN semesters sem ON s.id = sem.session_id
                GROUP BY s.id
                ORDER BY s.start_year DESC";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Set current session
     */
    public function setCurrentSession(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            // Use stored procedure
            $this->db->query("CALL sp_set_current_session(:session_id)");
            $this->db->bind(':session_id', $id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get session with semesters
     */
    public function getSessionWithSemesters(int $id): ?array {
        $session = $this->find($id);
        
        if (!$session) {
            return null;
        }
        
        $sql = "SELECT * FROM semesters WHERE session_id = :session_id ORDER BY semester_number ASC";
        $this->db->query($sql);
        $this->db->bind(':session_id', $id);
        $session['semesters'] = $this->db->fetchAll();
        
        return $session;
    }

    /**
     * Get semesters for session
     */
    public function getSemesters(int $sessionId): array {
        $sql = "SELECT * FROM semesters WHERE session_id = :session_id ORDER BY semester_number ASC";
        $this->db->query($sql);
        $this->db->bind(':session_id', $sessionId);
        return $this->db->fetchAll();
    }

    /**
     * Toggle session active status
     */
    public function toggleActive(int $id): bool {
        $session = $this->find($id);
        if (!$session) {
            return false;
        }
        
        // Don't deactivate current session
        if ($session['is_current'] && $session['is_active']) {
            return false;
        }
        
        $newStatus = $session['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if session name exists (for validation)
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
     * Check if dates overlap with existing sessions
     */
    public function datesOverlap(string $startDate, string $endDate, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE (
                    (start_date <= :start_date AND end_date >= :start_date) OR
                    (start_date <= :end_date AND end_date >= :end_date) OR
                    (start_date >= :start_date AND end_date <= :end_date)
                )";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        $result = $this->db->fetch();
        return $result['count'] > 0;
    }

    /**
     * Check if session is current
     */
    public function isCurrentSession(int $id): bool {
        $session = $this->find($id);
        return $session && $session['is_current'];
    }

    /**
     * Get upcoming sessions
     */
    public function getUpcomingSessions(int $limit = 5): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE start_date > CURDATE() 
                ORDER BY start_date ASC 
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }

    /**
     * Get past sessions
     */
    public function getPastSessions(int $limit = 5): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE end_date < CURDATE() 
                ORDER BY start_year DESC 
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }
}
