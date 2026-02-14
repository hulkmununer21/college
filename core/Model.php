<?php
/**
 * Base Model Class
 * 
 * Parent class for all models
 * Provides common database operations and query building
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class Model
{
    /**
     * Database instance
     */
    protected Database $db;

    /**
     * Table name
     */
    protected string $table = '';

    /**
     * Primary key column
     */
    protected string $primaryKey = 'id';

    /**
     * Fillable columns (for mass assignment protection)
     */
    protected array $fillable = [];

    /**
     * Timestamps
     */
    protected bool $timestamps = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find record by ID
     * 
     * @param int $id
     * @return mixed
     */
    public function find(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->db->query($sql)->bind(':id', $id)->fetch();
    }

    /**
     * Find all records
     * 
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Find records with conditions
     * 
     * @param array $conditions Key-value pairs of conditions
     * @return array
     */
    public function where(array $conditions): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $whereClause = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
        }
        
        $sql .= implode(' AND ', $whereClause);
        
        $query = $this->db->query($sql);
        
        foreach ($conditions as $column => $value) {
            $query->bind(":{$column}", $value);
        }
        
        return $query->fetchAll();
    }

    /**
     * Find single record with conditions
     * 
     * @param array $conditions Key-value pairs of conditions
     * @return mixed
     */
    public function findWhere(array $conditions)
    {
        $sql = "SELECT * FROM {$this->table} WHERE ";
        $whereClause = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
        }
        
        $sql .= implode(' AND ', $whereClause) . " LIMIT 1";
        
        $query = $this->db->query($sql);
        
        foreach ($conditions as $column => $value) {
            $query->bind(":{$column}", $value);
        }
        
        return $query->fetch();
    }

    /**
     * Insert new record
     * 
     * @param array $data Data to insert
     * @return string Last inserted ID
     */
    public function insert(array $data): string
    {
        // Filter data based on fillable columns
        $data = $this->filterFillable($data);

        // Add timestamps if enabled
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $query = $this->db->query($sql);
        
        foreach ($data as $column => $value) {
            $query->bind(":{$column}", $value);
        }
        
        $query->execute();
        
        return $this->db->lastInsertId();
    }

    /**
     * Update record
     * 
     * @param int $id Record ID
     * @param array $data Data to update
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        // Filter data based on fillable columns
        $data = $this->filterFillable($data);

        // Add updated timestamp if enabled
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "{$column} = :{$column}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . 
               " WHERE {$this->primaryKey} = :id";
        
        $query = $this->db->query($sql);
        
        foreach ($data as $column => $value) {
            $query->bind(":{$column}", $value);
        }
        
        $query->bind(':id', $id);
        
        return $query->execute();
    }

    /**
     * Delete record
     * 
     * @param int $id Record ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->query($sql)->bind(':id', $id)->execute();
    }

    /**
     * Count records
     * 
     * @param array $conditions Optional conditions
     * @return int
     */
    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if (!empty($conditions)) {
            $whereClause = [];
            
            foreach ($conditions as $column => $value) {
                $whereClause[] = "{$column} = :{$column}";
            }
            
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $query = $this->db->query($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $column => $value) {
                $query->bind(":{$column}", $value);
            }
        }
        
        $result = $query->fetch();
        return (int) $result['total'];
    }

    /**
     * Check if record exists
     * 
     * @param array $conditions
     * @return bool
     */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }

    /**
     * Filter data based on fillable columns
     * 
     * @param array $data
     * @return array
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Begin database transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * Commit database transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback database transaction
     * 
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->db->rollBack();
    }

    /**
     * Execute raw query
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function raw(string $sql, array $params = []): array
    {
        $query = $this->db->query($sql);
        
        foreach ($params as $param => $value) {
            $query->bind($param, $value);
        }
        
        return $query->fetchAll();
    }
}
