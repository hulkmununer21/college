<?php
/**
 * Database Class
 * 
 * Singleton Pattern Implementation for Database Connection using PDO
 * Prevents SQL Injection through prepared statements
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class Database
{
    /**
     * Singleton instance
     */
    private static ?Database $instance = null;

    /**
     * PDO connection object
     */
    private ?PDO $connection = null;

    /**
     * Prepared statement
     */
    private ?PDOStatement $statement = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Prevent cloning of instance
     */
    private function __clone() {}

    /**
     * Prevent unserialization of instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    /**
     * Get singleton instance
     * 
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establish database connection
     * 
     * @return void
     */
    private function connect(): void
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Get PDO connection
     * 
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Prepare SQL query
     * 
     * @param string $sql
     * @return Database
     */
    public function query(string $sql): Database
    {
        try {
            $this->statement = $this->connection->prepare($sql);
        } catch (PDOException $e) {
            $this->handleError($e);
        }
        return $this;
    }

    /**
     * Bind values to prepared statement
     * 
     * @param mixed $param Parameter identifier
     * @param mixed $value The value to bind
     * @param int|null $type PDO parameter type
     * @return Database
     */
    public function bind($param, $value, ?int $type = null): Database
    {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR
            };
        }

        $this->statement->bindValue($param, $value, $type);
        return $this;
    }

    /**
     * Execute prepared statement
     * 
     * @return bool
     */
    public function execute(): bool
    {
        try {
            return $this->statement->execute();
        } catch (PDOException $e) {
            $this->handleError($e);
            return false;
        }
    }

    /**
     * Fetch all results as associative array
     * 
     * @return array
     */
    public function fetchAll(): array
    {
        $this->execute();
        return $this->statement->fetchAll();
    }

    /**
     * Fetch single row as associative array
     * 
     * @return mixed
     */
    public function fetch(): mixed
    {
        $this->execute();
        return $this->statement->fetch();
    }

    /**
     * Fetch single column value
     * 
     * @return mixed
     */
    public function fetchColumn(): mixed
    {
        $this->execute();
        return $this->statement->fetchColumn();
    }

    /**
     * Get row count
     * 
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * Get last inserted ID
     * 
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Begin transaction
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit transaction
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback transaction
     * 
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Check if inside a transaction
     * 
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->connection->inTransaction();
    }

    /**
     * Handle database errors
     * 
     * @param PDOException $e
     * @return void
     */
    private function handleError(PDOException $e): void
    {
        // Log error (implement proper logging in production)
        error_log("Database Error: " . $e->getMessage());
        
        // In production, show generic error to user
        if (ini_get('display_errors') == 0) {
            die("A database error occurred. Please contact the administrator.");
        } else {
            die("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Close database connection
     * 
     * @return void
     */
    public function close(): void
    {
        $this->statement = null;
        $this->connection = null;
    }
}
