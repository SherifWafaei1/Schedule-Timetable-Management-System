<?php
/**
 * Timetable Event Model
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';

class TimetableEvent {
    private $conn;
    private $table_name = 'timetable_events';
    
    public $id;
    public $title;
    public $description;
    public $event_type;
    public $subject_id;
    public $class_id;
    public $teacher_id;
    public $room_id;
    public $time_slot_id;
    public $event_date;
    public $is_recurring;
    public $recurrence_pattern;
    public $recurrence_end_date;
    public $parent_event_id;
    public $is_cancelled;
    public $created_by;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new timetable event
     */
    public function create() {
        // Check for conflicts before creating
        $conflicts = $this->checkConflicts();
        if (!empty($conflicts)) {
            return ['success' => false, 'conflicts' => $conflicts];
        }
        
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, description, event_type, subject_id, class_id, teacher_id, 
                   room_id, time_slot_id, event_date, is_recurring, recurrence_pattern, 
                   recurrence_end_date, created_by)
                  VALUES (:title, :description, :event_type, :subject_id, :class_id, :teacher_id, 
                          :room_id, :time_slot_id, :event_date, :is_recurring, :recurrence_pattern, 
                          :recurrence_end_date, :created_by)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':event_type', $this->event_type);
        $stmt->bindParam(':subject_id', $this->subject_id);
        $stmt->bindParam(':class_id', $this->class_id);
        $stmt->bindParam(':teacher_id', $this->teacher_id);
        $stmt->bindParam(':room_id', $this->room_id);
        $stmt->bindParam(':time_slot_id', $this->time_slot_id);
        $stmt->bindParam(':event_date', $this->event_date);
        $stmt->bindParam(':is_recurring', $this->is_recurring);
        $stmt->bindParam(':recurrence_pattern', $this->recurrence_pattern);
        $stmt->bindParam(':recurrence_end_date', $this->recurrence_end_date);
        $stmt->bindParam(':created_by', $this->created_by);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Handle recurring events
            if ($this->is_recurring && $this->recurrence_pattern && $this->recurrence_end_date) {
                $this->createRecurringEvents();
            }
            
            logAuditTrail('CREATE_EVENT', $this->table_name, $this->id, null, [
                'title' => $this->title,
                'event_type' => $this->event_type,
                'event_date' => $this->event_date
            ]);
            
            return ['success' => true, 'id' => $this->id];
        }
        
        return ['success' => false, 'error' => 'Failed to create event'];
    }
    
    /**
     * Get event by ID
     */
    public function readOne() {
        $query = "SELECT te.*, s.subject_name, r.room_name, r.room_code, ts.slot_name, 
                         ts.start_time, ts.end_time, u.first_name as teacher_first_name, 
                         u.last_name as teacher_last_name, c.class_name, c.class_code,
                         creator.first_name as created_by_first_name, 
                         creator.last_name as created_by_last_name
                  FROM " . $this->table_name . " te
                  LEFT JOIN subjects s ON te.subject_id = s.id
                  LEFT JOIN rooms r ON te.room_id = r.id
                  LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                  LEFT JOIN users u ON te.teacher_id = u.id
                  LEFT JOIN classes c ON te.class_id = c.id
                  LEFT JOIN users creator ON te.created_by = creator.id
                  WHERE te.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->title = $row['title'];
            $this->description = $row['description'];
            $this->event_type = $row['event_type'];
            $this->subject_id = $row['subject_id'];
            $this->class_id = $row['class_id'];
            $this->teacher_id = $row['teacher_id'];
            $this->room_id = $row['room_id'];
            $this->time_slot_id = $row['time_slot_id'];
            $this->event_date = $row['event_date'];
            $this->is_recurring = $row['is_recurring'];
            $this->recurrence_pattern = $row['recurrence_pattern'];
            $this->recurrence_end_date = $row['recurrence_end_date'];
            $this->parent_event_id = $row['parent_event_id'];
            $this->is_cancelled = $row['is_cancelled'];
            $this->created_by = $row['created_by'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            
            // Add related data
            $this->subject_name = $row['subject_name'];
            $this->room_name = $row['room_name'];
            $this->room_code = $row['room_code'];
            $this->slot_name = $row['slot_name'];
            $this->start_time = $row['start_time'];
            $this->end_time = $row['end_time'];
            $this->teacher_name = $row['teacher_first_name'] . ' ' . $row['teacher_last_name'];
            $this->class_name = $row['class_name'];
            $this->class_code = $row['class_code'];
            $this->created_by_name = $row['created_by_first_name'] . ' ' . $row['created_by_last_name'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get events with filters and pagination
     */
    public function readAll($filters = [], $from_record_num = 0, $records_per_page = RECORDS_PER_PAGE) {
        $where_conditions = [];
        $params = [];
        
        // Apply filters
        if (!empty($filters['start_date'])) {
            $where_conditions[] = "te.event_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_conditions[] = "te.event_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['teacher_id'])) {
            $where_conditions[] = "te.teacher_id = :teacher_id";
            $params[':teacher_id'] = $filters['teacher_id'];
        }
        
        if (!empty($filters['room_id'])) {
            $where_conditions[] = "te.room_id = :room_id";
            $params[':room_id'] = $filters['room_id'];
        }
        
        if (!empty($filters['class_id'])) {
            $where_conditions[] = "te.class_id = :class_id";
            $params[':class_id'] = $filters['class_id'];
        }
        
        if (!empty($filters['event_type'])) {
            $where_conditions[] = "te.event_type = :event_type";
            $params[':event_type'] = $filters['event_type'];
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "te.title LIKE :search";
            $params[':search'] = $filters['search'];
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $query = "SELECT te.*, s.subject_name, r.room_name, r.room_code, ts.slot_name, 
                         ts.start_time, ts.end_time, u.first_name as teacher_first_name, 
                         u.last_name as teacher_last_name, c.class_name, c.class_code
                  FROM " . $this->table_name . " te
                  LEFT JOIN subjects s ON te.subject_id = s.id
                  LEFT JOIN rooms r ON te.room_id = r.id
                  LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                  LEFT JOIN users u ON te.teacher_id = u.id
                  LEFT JOIN classes c ON te.class_id = c.id
                  $where_clause
                  ORDER BY te.event_date, ts.start_time
                  LIMIT :from_record_num, :records_per_page";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(':from_record_num', $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count events with filters
     */
    public function countAll($filters = []) {
        $where_conditions = [];
        $params = [];
        
        // Apply same filters as readAll
        if (!empty($filters['start_date'])) {
            $where_conditions[] = "event_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_conditions[] = "event_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['teacher_id'])) {
            $where_conditions[] = "teacher_id = :teacher_id";
            $params[':teacher_id'] = $filters['teacher_id'];
        }
        
        if (!empty($filters['room_id'])) {
            $where_conditions[] = "room_id = :room_id";
            $params[':room_id'] = $filters['room_id'];
        }
        
        if (!empty($filters['class_id'])) {
            $where_conditions[] = "class_id = :class_id";
            $params[':class_id'] = $filters['class_id'];
        }
        
        if (!empty($filters['event_type'])) {
            $where_conditions[] = "event_type = :event_type";
            $params[':event_type'] = $filters['event_type'];
        }
        
        if (!empty($filters['search'])) {
            $where_conditions[] = "title LIKE :search";
            $params[':search'] = $filters['search'];
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " " . $where_clause;
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
    
    /**
     * Update event
     */
    public function update() {
        // Get old data for audit
        $oldData = [];
        $oldEventForAudit = new TimetableEvent($this->conn);
        $oldEventForAudit->id = $this->id;
        if ($oldEventForAudit->readOne()) {
            $oldData = [
                'title' => $oldEventForAudit->title,
                'event_type' => $oldEventForAudit->event_type,
                'event_date' => $oldEventForAudit->event_date,
                'teacher_id' => $oldEventForAudit->teacher_id,
                'room_id' => $oldEventForAudit->room_id,
                'time_slot_id' => $oldEventForAudit->time_slot_id
            ];
        }
        
        // Check for conflicts (excluding current event)
        $conflicts = $this->checkConflicts($this->id);
        if (!empty($conflicts)) {
            return ['success' => false, 'conflicts' => $conflicts];
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, event_type = :event_type, 
                      subject_id = :subject_id, class_id = :class_id, teacher_id = :teacher_id, 
                      room_id = :room_id, time_slot_id = :time_slot_id, event_date = :event_date
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Bind values with explicit types and handle NULLs to avoid accidental 0 inserts
        $stmt->bindValue(':title', $this->title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $this->description ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':event_type', $this->event_type, PDO::PARAM_STR);

        if ($this->subject_id === null || $this->subject_id === '') {
            $stmt->bindValue(':subject_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':subject_id', (int)$this->subject_id, PDO::PARAM_INT);
        }

        if ($this->class_id === null || $this->class_id === '') {
            $stmt->bindValue(':class_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':class_id', (int)$this->class_id, PDO::PARAM_INT);
        }

        // teacher_id should be present, but guard against empty values
        if ($this->teacher_id === null || $this->teacher_id === '' || (int)$this->teacher_id <= 0) {
            $stmt->bindValue(':teacher_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':teacher_id', (int)$this->teacher_id, PDO::PARAM_INT);
        }

        if ($this->room_id === null || $this->room_id === '') {
            $stmt->bindValue(':room_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':room_id', (int)$this->room_id, PDO::PARAM_INT);
        }

        if ($this->time_slot_id === null || $this->time_slot_id === '') {
            $stmt->bindValue(':time_slot_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':time_slot_id', (int)$this->time_slot_id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':event_date', $this->event_date, PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$this->id, PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                logAuditTrail('UPDATE_EVENT', $this->table_name, $this->id, $oldData, [
                    'title' => $this->title,
                    'description' => $this->description,
                    'event_type' => $this->event_type,
                    'event_date' => $this->event_date,
                    'subject_id' => $this->subject_id,
                    'class_id' => $this->class_id,
                    'teacher_id' => $this->teacher_id,
                    'room_id' => $this->room_id,
                    'time_slot_id' => $this->time_slot_id
                ]);

                return ['success' => true];
            }

            $err = $stmt->errorInfo();
            error_log('TimetableEvent::update failed: ' . json_encode($err));
            return ['success' => false, 'error' => $err[2] ?? 'Failed to update event in the database.'];
        } catch (PDOException $e) {
            error_log('TimetableEvent::update exception: ' . $e->getMessage());
            $err = isset($stmt) ? $stmt->errorInfo() : null;
            if ($err) error_log('PDO errorInfo: ' . json_encode($err));
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Delete event
     */
    public function delete() {
        // Get old data for audit
        $oldData = [];
        $this->readOne();
        $oldData = [
            'title' => $this->title,
            'event_type' => $this->event_type,
            'event_date' => $this->event_date
        ];
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            logAuditTrail('DELETE_EVENT', $this->table_name, $this->id, $oldData);
            return ['success' => true];
        }
        
        return ['success' => false, 'error' => 'Failed to delete event'];
    }
    
    /**
     * Check for scheduling conflicts
     */
    public function checkConflicts($excludeEventId = null) {
        $conflicts = [];
        
        // Get time slot details
        $stmt = $this->conn->prepare("SELECT start_time, end_time FROM time_slots WHERE id = ?");
        $stmt->execute([$this->time_slot_id]);
        $timeSlot = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$timeSlot) {
            return $conflicts;
        }
        
        $where_conditions = [
            "te.event_date = :event_date",
            "te.time_slot_id = :time_slot_id"
        ];
        $params = [
            ':event_date' => $this->event_date,
            ':time_slot_id' => $this->time_slot_id
        ];
        
        if ($excludeEventId) {
            $where_conditions[] = "te.id != :exclude_id";
            $params[':exclude_id'] = $excludeEventId;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Check room conflicts
        if ($this->room_id) {
            $stmt = $this->conn->prepare("
                SELECT te.*, r.room_name 
                FROM " . $this->table_name . " te
                LEFT JOIN rooms r ON te.room_id = r.id
                WHERE $where_clause AND te.room_id = :room_id
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':room_id', $this->room_id);
            $stmt->execute();
            
            $roomConflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($roomConflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'room',
                    'message' => "Room conflict with '{$conflict['title']}' in {$conflict['room_name']}",
                    'conflicting_event' => $conflict
                ];
            }
        }
        
        // Check teacher conflicts
        if ($this->teacher_id) {
            $stmt = $this->conn->prepare("
                SELECT te.*, u.first_name, u.last_name 
                FROM " . $this->table_name . " te
                LEFT JOIN users u ON te.teacher_id = u.id
                WHERE $where_clause AND te.teacher_id = :teacher_id
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':teacher_id', $this->teacher_id);
            $stmt->execute();
            
            $teacherConflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($teacherConflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'teacher',
                    'message' => "Teacher conflict with '{$conflict['title']}' taught by {$conflict['first_name']} {$conflict['last_name']}",
                    'conflicting_event' => $conflict
                ];
            }
        }
        
        // Check class conflicts
        if ($this->class_id) {
            $stmt = $this->conn->prepare("
                SELECT te.*, c.class_name 
                FROM " . $this->table_name . " te
                LEFT JOIN classes c ON te.class_id = c.id
                WHERE $where_clause AND te.class_id = :class_id
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':class_id', $this->class_id);
            $stmt->execute();
            
            $classConflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($classConflicts as $conflict) {
                $conflicts[] = [
                    'type' => 'class',
                    'message' => "Class conflict with '{$conflict['title']}' for class {$conflict['class_name']}",
                    'conflicting_event' => $conflict
                ];
            }
        }
        
        return $conflicts;
    }
    
    /**
     * Create recurring events
     */
    private function createRecurringEvents() {
        $currentDate = new DateTime($this->event_date);
        $endDate = new DateTime($this->recurrence_end_date);
        
        while ($currentDate <= $endDate) {
            // Add interval based on pattern
            switch ($this->recurrence_pattern) {
                case 'daily':
                    $currentDate->add(new DateInterval('P1D'));
                    break;
                case 'weekly':
                    $currentDate->add(new DateInterval('P7D'));
                    break;
                case 'monthly':
                    $currentDate->add(new DateInterval('P1M'));
                    break;
            }
            
            if ($currentDate <= $endDate) {
                $query = "INSERT INTO " . $this->table_name . " 
                          (title, description, event_type, subject_id, class_id, teacher_id, 
                           room_id, time_slot_id, event_date, is_recurring, recurrence_pattern, 
                           recurrence_end_date, parent_event_id, created_by)
                          VALUES (:title, :description, :event_type, :subject_id, :class_id, :teacher_id, 
                                  :room_id, :time_slot_id, :event_date, 0, NULL, NULL, :parent_event_id, :created_by)";
                
                $stmt = $this->conn->prepare($query);
                
                $stmt->bindParam(':title', $this->title);
                $stmt->bindParam(':description', $this->description);
                $stmt->bindParam(':event_type', $this->event_type);
                $stmt->bindParam(':subject_id', $this->subject_id);
                $stmt->bindParam(':class_id', $this->class_id);
                $stmt->bindParam(':teacher_id', $this->teacher_id);
                $stmt->bindParam(':room_id', $this->room_id);
                $stmt->bindParam(':time_slot_id', $this->time_slot_id);
                $stmt->bindParam(':event_date', $currentDate->format('Y-m-d'));
                $stmt->bindParam(':parent_event_id', $this->id);
                $stmt->bindParam(':created_by', $this->created_by);
                
                $stmt->execute();
            }
        }
    }
    
    /**
     * Get events for calendar view
     */
    public function getCalendarEvents($startDate, $endDate, $userId = null, $userRole = null) {
        $where_conditions = [
            "te.event_date BETWEEN :start_date AND :end_date"
        ];
        $params = [
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ];
        
        // Filter by user role
        if ($userRole === ROLE_TEACHER && $userId) {
            $where_conditions[] = "te.teacher_id = :user_id";
            $params[':user_id'] = $userId;
        } elseif ($userRole === ROLE_STUDENT && $userId) {
            $where_conditions[] = "se.student_id = :user_id";
            $params[':user_id'] = $userId;
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $query = "SELECT te.*, s.subject_name, r.room_name, r.room_code, ts.slot_name, 
                         ts.start_time, ts.end_time, u.first_name as teacher_first_name, 
                         u.last_name as teacher_last_name, c.class_name, c.class_code
                  FROM " . $this->table_name . " te
                  LEFT JOIN subjects s ON te.subject_id = s.id
                  LEFT JOIN rooms r ON te.room_id = r.id
                  LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                  LEFT JOIN users u ON te.teacher_id = u.id
                  LEFT JOIN classes c ON te.class_id = c.id";
        
        if ($userRole === ROLE_STUDENT && $userId) {
            $query .= " LEFT JOIN student_enrollments se ON te.class_id = se.class_id";
        }
        
        $query .= " WHERE $where_clause ORDER BY te.event_date, ts.start_time";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
