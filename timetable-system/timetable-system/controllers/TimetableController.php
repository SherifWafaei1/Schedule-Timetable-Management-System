<?php
/**
 * Timetable Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/TimetableEvent.php';

class TimetableController extends BaseController {
    private $timetableModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->timetableModel = new TimetableEvent($db);
    }

    /**
     * Show timetable list
     */
    public function index() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('login.php');
        }

        if (!$this->isAdmin() && !$this->isTeacher()) {
            $this->flash->set('Access denied. Only administrators and teachers can manage timetables.', 'danger');
            $this->redirect('admin/dashboard.php');
        }

        $current_user = $this->session->getUser();
        $page = (int)($_GET['page'] ?? 1);
        $search = $this->input->sanitize($_GET['search'] ?? '');
        $date_filter = $this->input->sanitize($_GET['date'] ?? '');
        $from_record_num = ($page - 1) * RECORDS_PER_PAGE;

        $filters = [];
        if (!empty($search)) $filters['search'] = "%" . $search . "%";
        if (!empty($date_filter)) {
            $filters['start_date'] = $date_filter;
            $filters['end_date'] = $date_filter;
        }
        if (!isAdmin()) $filters['teacher_id'] = $current_user['id'];

        try {
            $events = $this->timetableModel->readAll($filters, $from_record_num, RECORDS_PER_PAGE);
            $total_events = $this->timetableModel->countAll($filters);
            $pagination = paginate($total_events, $page);

            // Get dropdown data
            $subjects = $this->db->query("SELECT id, subject_code, subject_name FROM subjects WHERE is_active = 1 ORDER BY subject_name")->fetchAll();
            $rooms = $this->db->query("SELECT id, room_code, room_name FROM rooms WHERE is_active = 1 ORDER BY room_name")->fetchAll();
            $timeSlots = $this->db->query("SELECT id, slot_name, start_time, end_time FROM time_slots WHERE is_active = 1 ORDER BY start_time")->fetchAll();
            $classes = $this->db->query("SELECT id, class_code, class_name FROM classes WHERE is_active = 1 ORDER BY class_name")->fetchAll();
            $teachers = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role = 'teacher' AND is_active = 1 ORDER BY first_name")->fetchAll();

            $flash = $this->flash->get();

            $this->render('timetable/index', [
                'page_title' => 'Timetable Management',
                'current_user' => $current_user,
                'events' => $events,
                'pagination' => $pagination,
                'subjects' => $subjects,
                'rooms' => $rooms,
                'timeSlots' => $timeSlots,
                'classes' => $classes,
                'teachers' => $teachers,
                'flash' => $flash,
                'search' => $search,
                'date_filter' => $date_filter
            ]);
        } catch (Exception $e) {
            error_log("Timetable index error: " . $e->getMessage());
            $this->flash->set('An error occurred loading the timetable.', 'danger');
            $this->redirect('admin/dashboard.php');
        }
    }

    /**
     * Handle event creation
     */
    public function create() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request. Please try again.', 'danger');
            $this->redirect('timetable.php');
        }

        $current_user = $this->session->getUser();
        $this->timetableModel->title = $this->input->sanitize($_POST['title']);
        $this->timetableModel->description = $this->input->sanitize($_POST['description']);
        $this->timetableModel->event_type = $this->input->sanitize($_POST['event_type']);
        $this->timetableModel->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
        $this->timetableModel->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
        
        $postedTeacher = $_POST['teacher_id'] ?? '';
        $this->timetableModel->teacher_id = empty($postedTeacher) ? $current_user['id'] : (int)$postedTeacher;
        
        $this->timetableModel->room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
        $this->timetableModel->time_slot_id = (int)$_POST['time_slot_id'];
        $this->timetableModel->event_date = $this->input->sanitize($_POST['event_date']);
        $this->timetableModel->created_by = $current_user['id'];
        
        $result = $this->timetableModel->create();
        $this->flash->set($result['success'] ? 'Event created successfully.' : 'Failed to create event.', $result['success'] ? 'success' : 'danger');
        $this->redirect('timetable.php');
    }

    /**
     * Handle event update
     */
    public function update() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request. Please try again.', 'danger');
            $this->redirect('timetable.php');
        }

        $current_user = $this->session->getUser();
        $this->timetableModel->id = (int)$_POST['event_id'];
        $this->timetableModel->title = $this->input->sanitize($_POST['title']);
        $this->timetableModel->description = $this->input->sanitize($_POST['description']);
        $this->timetableModel->event_type = $this->input->sanitize($_POST['event_type']);
        $this->timetableModel->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
        $this->timetableModel->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
        
        if ($this->isTeacher()) {
            $this->timetableModel->teacher_id = $current_user['id'];
        } else {
            $this->timetableModel->teacher_id = (int)$_POST['teacher_id'];
        }
        
        $this->timetableModel->room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
        $this->timetableModel->time_slot_id = (int)$_POST['time_slot_id'];
        $this->timetableModel->event_date = $this->input->sanitize($_POST['event_date']);
        
        $errors = $this->input->validateRequired($_POST, ['title', 'event_type', 'event_date', 'time_slot_id']);
        if (!empty($errors)) {
            $this->flash->set('Please fill in all required fields.', 'danger');
            $this->redirect('timetable.php');
        }

        $result = $this->timetableModel->update();
        $this->flash->set($result['success'] ? 'Event updated successfully.' : 'Failed to update event.', $result['success'] ? 'success' : 'danger');
        $this->redirect('timetable.php');
    }

    /**
     * Handle event deletion
     */
    public function delete() {
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request. Please try again.', 'danger');
            $this->redirect('timetable.php');
        }

        $this->timetableModel->id = (int)$_POST['event_id'];
        $result = $this->timetableModel->delete();
        $this->flash->set($result['success'] ? 'Event deleted successfully.' : 'Failed to delete event.', $result['success'] ? 'success' : 'danger');
        $this->redirect('timetable.php');
    }
}
?>
