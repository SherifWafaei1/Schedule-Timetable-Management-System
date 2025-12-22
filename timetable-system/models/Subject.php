<?php
/**
 * Subject Model
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

class Subject {
    private $conn;
    private $table_name = 'subjects';

    public $id;
    public $subject_code;
    public $subject_name;
    public $description;
    public $credits;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new subject
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (subject_code, subject_name, description, credits, is_active)
                  VALUES (:subject_code, :subject_name, :description, :credits, :is_active)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_code', $this->subject_code);
        $stmt->bindParam(':subject_name', $this->subject_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':credits', $this->credits);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            logAuditTrail('CREATE_SUBJECT', $this->table_name, $this->id, null, ['subject_code' => $this->subject_code, 'subject_name' => $this->subject_name]);
            return true;
        }
        return false;
    }

    /**
     * Read all subjects with pagination
     */
    public function readAll($from_record_num, $records_per_page) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY subject_name ASC LIMIT :from_record_num, :records_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from_record_num', $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all subjects
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Read one subject by ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->subject_code = $row['subject_code'];
            $this->subject_name = $row['subject_name'];
            $this->description = $row['description'];
            $this->credits = $row['credits'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    /**
     * Update a subject
     */
    public function update() {
        $oldData = [];
        $currentSubject = new Subject($this->conn);
        $currentSubject->id = $this->id;
        if ($currentSubject->readOne()) {
            $oldData = [
                'subject_code' => $currentSubject->subject_code,
                'subject_name' => $currentSubject->subject_name,
                'is_active' => $currentSubject->is_active
            ];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET subject_code = :subject_code, subject_name = :subject_name, description = :description, 
                      credits = :credits, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_code', $this->subject_code);
        $stmt->bindParam(':subject_name', $this->subject_name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':credits', $this->credits);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('UPDATE_SUBJECT', $this->table_name, $this->id, $oldData, [
                'subject_code' => $this->subject_code,
                'subject_name' => $this->subject_name,
                'is_active' => $this->is_active
            ]);
            return true;
        }
        return false;
    }

    /**
     * Delete a subject
     */
    public function delete() {
        $oldData = [];
        if ($this->readOne()) {
            $oldData = ['subject_code' => $this->subject_code, 'subject_name' => $this->subject_name];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('DELETE_SUBJECT', $this->table_name, $this->id, $oldData);
            return true;
        }
        return false;
    }

    /**
     * Check if subject code exists
     */
    public function subjectCodeExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE subject_code = :subject_code";
        if ($this->id) {
            $query .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_code', $this->subject_code);
        if ($this->id) {
            $stmt->bindParam(':id', $this->id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>