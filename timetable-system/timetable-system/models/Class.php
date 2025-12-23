<?php
/**
 * Class Model
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

class SchoolClass {
    private $conn;
    private $table_name = 'classes';

    public $id;
    public $class_code;
    public $class_name;
    public $description;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new class
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (class_code, class_name, description, is_active)
                  VALUES (:class_code, :class_name, :description, :is_active)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':class_code', $this->class_code);
        $stmt->bindParam(':class_name', $this->class_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            logAuditTrail('CREATE_CLASS', $this->table_name, $this->id, null, ['class_code' => $this->class_code, 'class_name' => $this->class_name]);
            return true;
        }
        return false;
    }

    /**
     * Read all classes with pagination
     */
    public function readAll($from_record_num, $records_per_page) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY class_name ASC LIMIT :from_record_num, :records_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from_record_num', $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all classes
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Read one class by ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->class_code = $row['class_code'];
            $this->class_name = $row['class_name'];
            $this->description = $row['description'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    /**
     * Update a class
     */
    public function update() {
        $oldData = [];
        $currentClass = new SchoolClass($this->conn);
        $currentClass->id = $this->id;
        if ($currentClass->readOne()) {
            $oldData = [
                'class_code' => $currentClass->class_code,
                'class_name' => $currentClass->class_name,
                'is_active' => $currentClass->is_active
            ];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET class_code = :class_code, class_name = :class_name, description = :description, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':class_code', $this->class_code);
        $stmt->bindParam(':class_name', $this->class_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('UPDATE_CLASS', $this->table_name, $this->id, $oldData, [
                'class_code' => $this->class_code,
                'class_name' => $this->class_name,
                'is_active' => $this->is_active
            ]);
            return true;
        }
        return false;
    }

    /**
     * Delete a class
     */
    public function delete() {
        $oldData = [];
        if ($this->readOne()) {
            $oldData = ['class_code' => $this->class_code, 'class_name' => $this->class_name];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('DELETE_CLASS', $this->table_name, $this->id, $oldData);
            return true;
        }
        return false;
    }

    /**
     * Check if class code exists
     */
    public function classCodeExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE class_code = :class_code";
        if ($this->id) {
            $query .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_code', $this->class_code);
        if ($this->id) {
            $stmt->bindParam(':id', $this->id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>