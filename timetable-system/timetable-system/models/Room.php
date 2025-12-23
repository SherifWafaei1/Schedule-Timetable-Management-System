<?php
/**
 * Room Model
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

class Room {
    private $conn;
    private $table_name = 'rooms';

    public $id;
    public $room_code;
    public $room_name;
    public $capacity;
    public $room_type;
    public $location;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new room
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (room_code, room_name, capacity, room_type, location, is_active)
                  VALUES (:room_code, :room_name, :capacity, :room_type, :location, :is_active)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':room_code', $this->room_code);
        $stmt->bindParam(':room_name', $this->room_name);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':room_type', $this->room_type);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':is_active', $this->is_active);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            logAuditTrail('CREATE_ROOM', $this->table_name, $this->id, null, ['room_code' => $this->room_code, 'room_name' => $this->room_name]);
            return true;
        }
        return false;
    }

    /**
     * Read all rooms with pagination
     */
    public function readAll($from_record_num, $records_per_page) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY room_name ASC LIMIT :from_record_num, :records_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':from_record_num', $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all rooms
     */
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Read one room by ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->room_code = $row['room_code'];
            $this->room_name = $row['room_name'];
            $this->capacity = $row['capacity'];
            $this->room_type = $row['room_type'];
            $this->location = $row['location'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }

    /**
     * Update a room
     */
    public function update() {
        $oldData = [];
        $currentRoom = new Room($this->conn);
        $currentRoom->id = $this->id;
        if ($currentRoom->readOne()) {
            $oldData = [
                'room_code' => $currentRoom->room_code,
                'room_name' => $currentRoom->room_name,
                'capacity' => $currentRoom->capacity,
                'is_active' => $currentRoom->is_active
            ];
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET room_code = :room_code, room_name = :room_name, capacity = :capacity, 
                      room_type = :room_type, location = :location, is_active = :is_active
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':room_code', $this->room_code);
        $stmt->bindParam(':room_name', $this->room_name);
        $stmt->bindParam(':capacity', $this->capacity);
        $stmt->bindParam(':room_type', $this->room_type);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('UPDATE_ROOM', $this->table_name, $this->id, $oldData, [
                'room_code' => $this->room_code,
                'room_name' => $this->room_name,
                'is_active' => $this->is_active
            ]);
            return true;
        }
        return false;
    }

    /**
     * Delete a room
     */
    public function delete() {
        $oldData = [];
        if ($this->readOne()) {
            $oldData = ['room_code' => $this->room_code, 'room_name' => $this->room_name];
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            logAuditTrail('DELETE_ROOM', $this->table_name, $this->id, $oldData);
            return true;
        }
        return false;
    }

    /**
     * Check if room code exists
     */
    public function roomCodeExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE room_code = :room_code";
        if ($this->id) {
            $query .= " AND id != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':room_code', $this->room_code);
        if ($this->id) {
            $stmt->bindParam(':id', $this->id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>