<?php

declare(strict_types=1);

namespace App\Models\Base;

use mysqli;
use mysqli_stmt;
use mysqli_result;
use Exception;

/**
 * BaseModel - Abstract base class for all models
 * 
 * Provides common database operations and utilities following DRY principle
 * 
 * @category Model
 * @package  DocuTrack
 * @version  3.0.0
 */
abstract class BaseModel
{
    protected mysqli $db;
    
    /**
     * Table name - must be defined in child classes
     */
    protected string $table;
    
    /**
     * Primary key column name
     */
    protected string $primaryKey = 'id';
    
    /**
     * Fillable columns for mass assignment
     */
    protected array $fillable = [];
    
    /**
     * Constructor with flexible DB injection
     */
    public function __construct(?mysqli $db = null)
    {
        if ($db instanceof mysqli) {
            $this->db = $db;
            return;
        }

        // Fallback: try global db() helper
        if (function_exists('db')) {
            $this->db = db();
            return;
        }

        // Last resort: Database singleton
        try {
            $this->db = \App\Core\Database::getInstance()->getConnection();
        } catch (Exception $e) {
            throw new Exception("Database connection failed in " . static::class . ": " . $e->getMessage());
        }
    }
    
    /**
     * Prepare and execute a query safely
     * 
     * @param string $sql SQL query with placeholders
     * @param string $types Parameter types (i, d, s, b)
     * @param array $params Parameters to bind
     * @return mysqli_stmt
     * @throws Exception
     */
    protected function prepare(string $sql, string $types = '', array $params = []): mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);
        
        if ($stmt === false) {
            $error = $this->db->error;
            error_log("SQL Prepare Error in " . static::class . ": {$error}");
            error_log("Query: {$sql}");
            throw new Exception("Failed to prepare statement: {$error}");
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        return $stmt;
    }
    
    /**
     * Execute prepared statement and get result
     * 
     * @param mysqli_stmt $stmt
     * @return mysqli_result|bool
     * @throws Exception
     */
    protected function execute(mysqli_stmt $stmt): mysqli_result|bool
    {
        if (!$stmt->execute()) {
            $error = $stmt->error;
            error_log("SQL Execute Error in " . static::class . ": {$error}");
            throw new Exception("Failed to execute statement: {$error}");
        }
        
        return $stmt->get_result();
    }
    
    /**
     * Find record by primary key
     * 
     * @param int|string $id
     * @return array|null
     */
    public function find($id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        $stmt = $this->prepare($sql, 'i', [$id]);
        $result = $this->execute($stmt);
        
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data ?: null;
    }
    
    /**
     * Get all records with optional conditions
     * 
     * @param array $where ['column' => 'value']
     * @param string $orderBy 'column DESC'
     * @param int|null $limit
     * @return array
     */
    public function all(array $where = [], string $orderBy = '', ?int $limit = null): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        $types = '';
        
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $conditions[] = "{$column} = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : 's';
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        $stmt = $this->prepare($sql, $types, $params);
        $result = $this->execute($stmt);
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    /**
     * Insert new record
     * 
     * @param array $data
     * @return int Insert ID
     * @throws Exception
     */
    public function insert(array $data): int
    {
        // Filter only fillable columns
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            throw new Exception("No fillable data provided for insert");
        }
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $types = '';
        $values = [];
        
        foreach ($data as $value) {
            $values[] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->prepare($sql, $types, $values);
        $this->execute($stmt);
        
        $insertId = $this->db->insert_id;
        $stmt->close();
        
        return $insertId;
    }
    
    /**
     * Update record by ID
     * 
     * @param int|string $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function update($id, array $data): bool
    {
        // Filter only fillable columns
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        if (empty($data)) {
            throw new Exception("No fillable data provided for update");
        }
        
        $sets = [];
        $types = '';
        $values = [];
        
        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
            $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
        }
        
        $values[] = $id;
        $types .= 'i';
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " 
                WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->prepare($sql, $types, $values);
        $this->execute($stmt);
        
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected > 0;
    }
    
    /**
     * Delete record by ID
     * 
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->prepare($sql, 'i', [$id]);
        $this->execute($stmt);
        
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected > 0;
    }
    
    /**
     * Count records with optional conditions
     * 
     * @param array $where
     * @return int
     */
    public function count(array $where = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $params = [];
        $types = '';
        
        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $column => $value) {
                $conditions[] = "{$column} = ?";
                $params[] = $value;
                $types .= is_int($value) ? 'i' : 's';
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->prepare($sql, $types, $params);
        $result = $this->execute($stmt);
        
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return (int)($row['total'] ?? 0);
    }
    
    /**
     * Begin transaction
     */
    protected function beginTransaction(): void
    {
        $this->db->begin_transaction();
    }
    
    /**
     * Commit transaction
     */
    protected function commit(): void
    {
        $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    protected function rollback(): void
    {
        $this->db->rollback();
    }
    
    /**
     * Check if connection is alive
     */
    public function ping(): bool
    {
        return $this->db->ping();
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId(): int
    {
        return $this->db->insert_id;
    }
    
    /**
     * Escape string for SQL
     */
    protected function escape(string $value): string
    {
        return $this->db->real_escape_string($value);
    }
}
