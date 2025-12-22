<?php
/**
 * Report Controller Class
 */
require_once __DIR__ . '/BaseController.php';

class ReportController extends BaseController {
    
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Show report generation page
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied.', 'danger');
            $this->redirect('dashboard.php');
        }

        $startDate = $this->input->sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $endDate = $this->input->sanitize($_GET['end_date'] ?? date('Y-m-t'));
        $eventType = $this->input->sanitize($_GET['event_type'] ?? '');
        $teacherId = (int)($_GET['teacher_id'] ?? 0);
        $classId = (int)($_GET['class_id'] ?? 0);

        try {
            // Fetch dependencies for filters
            $teachers = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role = 'teacher' AND is_active = 1 ORDER BY first_name")->fetchAll();
            $classes = $this->db->query("SELECT id, class_code, class_name FROM classes WHERE is_active = 1 ORDER BY class_name")->fetchAll();

            // Build query
            $query = "
                SELECT te.*, s.subject_name, r.room_name, ts.start_time, ts.end_time,
                       u.first_name as teacher_first_name, u.last_name as teacher_last_name,
                       c.class_name
                FROM timetable_events te
                LEFT JOIN subjects s ON te.subject_id = s.id
                LEFT JOIN rooms r ON te.room_id = r.id
                LEFT JOIN time_slots ts ON te.time_slot_id = ts.id
                LEFT JOIN users u ON te.teacher_id = u.id
                LEFT JOIN classes c ON te.class_id = c.id
                WHERE te.event_date BETWEEN :start_date AND :end_date
            ";
            
            $params = [':start_date' => $startDate, ':end_date' => $endDate];

            if ($eventType) {
                $query .= " AND te.event_type = :event_type";
                $params[':event_type'] = $eventType;
            }
            if ($teacherId) {
                $query .= " AND te.teacher_id = :teacher_id";
                $params[':teacher_id'] = $teacherId;
            }
            if ($classId) {
                $query .= " AND te.class_id = :class_id";
                $params[':class_id'] = $classId;
            }

            $query .= " ORDER BY te.event_date, ts.start_time";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calc stats
            $stats = $this->calculateStats($events);
            
            $flash = $this->flash->get();

            $this->render('admin/reports/index', [
                'page_title' => 'System Reports',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'event_type' => $eventType,
                'teacher_id' => $teacherId,
                'class_id' => $classId,
                'teachers' => $teachers,
                'classes' => $classes,
                'events' => $events,
                'stats' => $stats,
                'flash' => $flash
            ]);

        } catch (Exception $e) {
            error_log("Report controller error: " . $e->getMessage());
            $this->flash->set('Error generating report.', 'danger');
            $this->render('admin/reports/index', ['page_title' => 'System Reports', 'events' => [], 'stats' => []]);
        }
    }

    private function calculateStats($events) {
        $stats = ['total_events' => count($events), 'by_type' => [], 'room_usage' => [], 'total_hours' => 0];
        foreach ($events as $event) {
            $stats['by_type'][$event['event_type']] = ($stats['by_type'][$event['event_type']] ?? 0) + 1;
            if ($event['room_name']) $stats['room_usage'][$event['room_name']] = 1;
            
            $duration = getTimeDifferenceInMinutes($event['start_time'], $event['end_time']);
            $stats['total_hours'] += ($duration / 60);
        }
        $stats['room_count'] = count($stats['room_usage']);
        return $stats;
    }
}
?>
