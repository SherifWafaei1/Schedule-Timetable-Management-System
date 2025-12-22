<?php
/**
 * Dashboard Controller Class
 */
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Show dashboard
     */
    public function index() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('../login.php');
        }

        $current_user = $this->session->getUser();
        $flash = $this->flash->get();

        $data = [
            'current_user' => $current_user,
            'flash' => $flash,
            'page_title' => 'Dashboard', // Added page_title
            'today_events' => [],
            'upcoming_events' => [],
            'stats' => []
        ];

        try {
            // Get today's events
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("
                SELECT te.*, s.subject_name, r.room_name, ts.slot_name, ts.start_time, ts.end_time,
                       u.first_name as teacher_first_name, u.last_name as teacher_last_name,
                       c.class_name
                FROM timetable_events te
                LEFT JOIN subjects s ON te.subject_id = s.id
                LEFT JOIN rooms r ON te.room_id = r.id
                LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                LEFT JOIN users u ON te.teacher_id = u.id
                LEFT JOIN classes c ON te.class_id = c.id
                WHERE te.event_date = ? AND te.is_cancelled = 0
                ORDER BY ts.start_time
            ");
            $stmt->execute([$today]);
            $data['today_events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get upcoming events (next 7 days)
            $next_week = date('Y-m-d', strtotime('+7 days'));
            $stmt = $this->db->prepare("
                SELECT te.*, s.subject_name, r.room_name, ts.slot_name, ts.start_time, ts.end_time,
                       u.first_name as teacher_first_name, u.last_name as teacher_last_name,
                       c.class_name
                FROM timetable_events te
                LEFT JOIN subjects s ON te.subject_id = s.id
                LEFT JOIN rooms r ON te.room_id = r.id
                LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                LEFT JOIN users u ON te.teacher_id = u.id
                LEFT JOIN classes c ON te.class_id = c.id
                WHERE te.event_date BETWEEN ? AND ? AND te.is_cancelled = 0
                ORDER BY te.event_date, ts.start_time
            ");
            $stmt->execute([$today, $next_week]);
            $data['upcoming_events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get statistics
            $stats = [];
            
            if (isAdmin()) {
                // Admin statistics
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users WHERE is_active = 1");
                $stmt->execute();
                $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM timetable_events WHERE is_cancelled = 0");
                $stmt->execute();
                $stats['total_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rooms WHERE is_active = 1");
                $stmt->execute();
                $stats['total_rooms'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            } elseif (isTeacher()) {
                // Teacher statistics
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM timetable_events 
                    WHERE teacher_id = ? AND is_cancelled = 0
                ");
                $stmt->execute([$current_user['id']]);
                $stats['my_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM timetable_events 
                    WHERE teacher_id = ? AND event_date = ? AND is_cancelled = 0
                ");
                $stmt->execute([$current_user['id'], $today]);
                $stats['today_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Get teacher's classes
                $stmt = $this->db->prepare("
                    SELECT DISTINCT c.class_name 
                    FROM timetable_events te
                    JOIN classes c ON te.class_id = c.id
                    WHERE te.teacher_id = ? AND te.is_cancelled = 0
                ");
                $stmt->execute([$current_user['id']]);
                $stats['my_classes'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } else {
                // Student statistics
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM timetable_events te
                    JOIN student_enrollments se ON te.class_id = se.class_id
                    WHERE se.student_id = ? AND te.is_cancelled = 0
                ");
                $stmt->execute([$current_user['id']]);
                $stats['my_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as total FROM timetable_events te
                    JOIN student_enrollments se ON te.class_id = se.class_id
                    WHERE se.student_id = ? AND te.event_date = ? AND te.is_cancelled = 0
                ");
                $stmt->execute([$current_user['id'], $today]);
                $stats['today_events'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Get student's enrolled classes
                $stmt = $this->db->prepare("
                    SELECT c.class_name 
                    FROM student_enrollments se
                    JOIN classes c ON se.class_id = c.id
                    WHERE se.student_id = ?
                ");
                $stmt->execute([$current_user['id']]);
                $stats['my_classes'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            $data['stats'] = $stats;
            
        } catch (Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
        }

        $this->render('admin/dashboard', $data);
    }
}
?>
