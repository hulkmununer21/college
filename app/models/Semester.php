<?php
declare(strict_types=1);

/**
 * Semester Model
 * Handles all database operations related to semesters
 */
class Semester extends Model {
    protected string $table = 'semesters';
    protected array $fillable = ['session_id', 'name', 'semester_number', 'start_date', 'end_date', 
                                 'registration_start_date', 'registration_end_date', 'is_current', 'is_active'];

    /**
     * Get current semester
     */
    public function getCurrentSemester(): ?array {
        return $this->where('is_current', 1)->fetch();
    }

    /**
     * Get current semester with session information
     */
    public function getCurrentSemesterWithSession(): ?array {
        $sql = "SELECT * FROM v_current_semester";
        $this->db->query($sql);
        return $this->db->fetch();
    }

    /**
     * Get semesters by session
     */
    public function getSemestersBySession(int $sessionId): array {
        return $this->where('session_id', $sessionId)
                    ->orderBy('semester_number', 'ASC')
                    ->fetchAll();
    }

    /**
     * Get active semesters
     */
    public function getActiveSemesters(): array {
        return $this->where('is_active', 1)
                    ->orderBy('start_date', 'DESC')
                    ->fetchAll();
    }

    /**
     * Get semester with session details
     */
    public function getSemesterWithSession(int $id): ?array {
        $sql = "SELECT 
                    sem.*,
                    sess.name AS session_name,
                    sess.start_year,
                    sess.end_year
                FROM semesters sem
                INNER JOIN sessions sess ON sem.session_id = sess.id
                WHERE sem.id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Set current semester
     */
    public function setCurrentSemester(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            // Use stored procedure
            $this->db->query("CALL sp_set_current_semester(:semester_id)");
            $this->db->bind(':semester_id', $id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Check if registration is open
     */
    public function isRegistrationOpen(?int $semesterId = null): bool {
        if ($semesterId) {
            $semester = $this->find($semesterId);
        } else {
            $semester = $this->getCurrentSemester();
        }
        
        if (!$semester) {
            return false;
        }
        
        $today = date('Y-m-d');
        return $today >= $semester['registration_start_date'] && 
               $today <= $semester['registration_end_date'];
    }

    /**
     * Get registration status
     */
    public function getRegistrationStatus(?int $semesterId = null): array {
        if ($semesterId) {
            $semester = $this->find($semesterId);
        } else {
            $semester = $this->getCurrentSemester();
        }
        
        if (!$semester) {
            return [
                'status' => 'unavailable',
                'message' => 'No semester found'
            ];
        }
        
        $today = date('Y-m-d');
        
        if ($today < $semester['registration_start_date']) {
            return [
                'status' => 'upcoming',
                'message' => 'Registration opens on ' . date('F j, Y', strtotime($semester['registration_start_date'])),
                'start_date' => $semester['registration_start_date']
            ];
        }
        
        if ($today > $semester['registration_end_date']) {
            return [
                'status' => 'closed',
                'message' => 'Registration closed on ' . date('F j, Y', strtotime($semester['registration_end_date'])),
                'end_date' => $semester['registration_end_date']
            ];
        }
        
        return [
            'status' => 'open',
            'message' => 'Registration is currently open',
            'start_date' => $semester['registration_start_date'],
            'end_date' => $semester['registration_end_date'],
            'days_remaining' => ceil((strtotime($semester['registration_end_date']) - time()) / 86400)
        ];
    }

    /**
     * Toggle semester active status
     */
    public function toggleActive(int $id): bool {
        $semester = $this->find($id);
        if (!$semester) {
            return false;
        }
        
        // Don't deactivate current semester
        if ($semester['is_current'] && $semester['is_active']) {
            return false;
        }
        
        $newStatus = $semester['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Check if semester number exists for session (for validation)
     */
    public function semesterExistsInSession(int $sessionId, int $semesterNumber, ?int $excludeId = null): bool {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE session_id = :session_id 
                AND semester_number = :semester_number";
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':session_id', $sessionId);
        $this->db->bind(':semester_number', $semesterNumber);
        
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        $result = $this->db->fetch();
        return $result['count'] > 0;
    }

    /**
     * Check if dates are within session dates
     */
    public function datesWithinSession(int $sessionId, string $startDate, string $endDate): bool {
        $sql = "SELECT start_date, end_date FROM sessions WHERE id = :session_id";
        $this->db->query($sql);
        $this->db->bind(':session_id', $sessionId);
        $session = $this->db->fetch();
        
        if (!$session) {
            return false;
        }
        
        return $startDate >= $session['start_date'] && $endDate <= $session['end_date'];
    }

    /**
     * Get all semesters with session information
     */
    public function getAllSemestersWithSession(): array {
        $sql = "SELECT 
                    sem.*,
                    sess.name AS session_name,
                    sess.start_year,
                    sess.end_year,
                    sess.is_current AS session_is_current
                FROM semesters sem
                INNER JOIN sessions sess ON sem.session_id = sess.id
                ORDER BY sess.start_year DESC, sem.semester_number ASC";
        
        $this->db->query($sql);
        return $this->db->fetchAll();
    }

    /**
     * Check if semester is current
     */
    public function isCurrentSemester(int $id): bool {
        $semester = $this->find($id);
        return $semester && $semester['is_current'];
    }
}
