<?php
/**
 * Timetable Management
 * Schedule Time Table Management System
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'models/TimetableEvent.php';
require_once 'includes/functions.php';

startSecureSession();

// Check if user is logged in
if (!isLoggedIn()) {
    redirectToLogin();
}

// Check permissions (only admin and teachers can manage timetables)
if (!isAdmin() && !isTeacher()) {
    redirectWithMessage('admin/dashboard.php', 'Access denied. Only administrators and teachers can manage timetables.', 'danger');
}

// Get current user info
$current_user = [
    'id' => $_SESSION['user_id'],
    'role' => $_SESSION['role'],
    'first_name' => $_SESSION['first_name'],
    'last_name' => $_SESSION['last_name']
];

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    // Verify CSRF token
    if (!verifyCSRFToken($csrf_token)) {
        $message = 'Invalid request. Please try again.';
        $message_type = 'danger';
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            $timetableEvent = new TimetableEvent($db);
            
            switch ($action) {
                case 'create':
                    $timetableEvent->title = sanitizeInput($_POST['title']);
                    $timetableEvent->description = sanitizeInput($_POST['description']);
                    $timetableEvent->event_type = sanitizeInput($_POST['event_type']);
                    $timetableEvent->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
                    $timetableEvent->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;
                    // Ensure teacher_id is set. If form omitted it (e.g., teacher role), default to current user.
                    $postedTeacher = $_POST['teacher_id'] ?? '';
                    if (empty($postedTeacher)) {
                        $timetableEvent->teacher_id = $current_user['id'];
                    } else {
                        $timetableEvent->teacher_id = (int)$postedTeacher;
                    }
                    $timetableEvent->room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
                    $timetableEvent->time_slot_id = (int)$_POST['time_slot_id'];
                    $timetableEvent->event_date = sanitizeInput($_POST['event_date']);
                    $timetableEvent->created_by = $current_user['id'];
                    
                    $result = $timetableEvent->create();
                    if ($result['success']) {
                        $message = 'Event created successfully.';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to create event.';
                        if (isset($result['conflicts'])) {
                            $message .= ' Conflicts detected: ' . implode(', ', array_column($result['conflicts'], 'message'));
                        }
                        $message_type = 'danger';
                    }
                    break;
                    
                case 'update':
                    $timetableEvent->id = (int)$_POST['event_id'];

                    // Populate the object with new data from the form
                    $timetableEvent->title = sanitizeInput($_POST['title']);
                    $timetableEvent->description = sanitizeInput($_POST['description']);
                    $timetableEvent->event_type = sanitizeInput($_POST['event_type']);
                    $timetableEvent->subject_id = !empty($_POST['subject_id']) ? (int)$_POST['subject_id'] : null;
                    $timetableEvent->class_id = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null;                    
                    $timetableEvent->teacher_id = (int)$_POST['teacher_id'];
                    $timetableEvent->room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
                    $timetableEvent->time_slot_id = (int)$_POST['time_slot_id'];
                    $timetableEvent->event_date = sanitizeInput($_POST['event_date']);
                    
                    // Validate required fields similar to profile update
                    $errors = validateRequired($_POST, ['title', 'event_type', 'event_date', 'time_slot_id', 'teacher_id']);
                    if (!empty($errors)) {
                        $message = 'Please fill in all required fields.';
                        $message_type = 'danger';
                        break;
                    }

                    // Basic date validation
                    $dateObj = DateTime::createFromFormat('Y-m-d', $timetableEvent->event_date);
                    if (!$dateObj || $dateObj->format('Y-m-d') !== $timetableEvent->event_date) {
                        $message = 'Invalid event date format.';
                        $message_type = 'danger';
                        break;
                    }

                    // Call the update method
                    $result = $timetableEvent->update();
                    // Log the full result for debugging (will appear in PHP/Apache error log)
                    error_log('Timetable update result: ' . json_encode($result));
                    if (!empty($result['success']) && $result['success']) {
                        $message = 'Event updated successfully.';
                        $message_type = 'success';
                    } else {
                        // Prefer any explicit error returned by the model
                        if (!empty($result['error'])) {
                            $message = 'Failed to update event: ' . $result['error'];
                        } else {
                            $message = 'Failed to update event.';
                        }

                        if (isset($result['conflicts'])) {
                            $message .= ' Conflicts detected: ' . implode(', ', array_column($result['conflicts'], 'message'));
                        }

                        $message_type = 'danger';
                    }
                    break;
                    
                case 'delete':
                    $timetableEvent->id = (int)$_POST['event_id'];
                    $result = $timetableEvent->delete();
                    if ($result['success']) {
                        $message = 'Event deleted successfully.';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to delete event.';
                        $message_type = 'danger';
                    }
                    break;
                    
                case 'cancel':
                    $timetableEvent->id = (int)$_POST['event_id'];
                    $result = $timetableEvent->cancel();
                    if ($result['success']) {
                        $message = 'Event cancelled successfully.';
                        $message_type = 'success';
                    } else {
                        $message = 'Failed to cancel event.';
                        $message_type = 'danger';
                    }
                    break;
            }
        } catch (Exception $e) {
            error_log("Timetable error: " . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $message_type = 'danger';
        }
    }
}

// Get pagination parameters
$page = (int)($_GET['page'] ?? 1);
$search = sanitizeInput($_GET['search'] ?? '');
$date_filter = sanitizeInput($_GET['date'] ?? '');

$from_record_num = ($page - 1) * RECORDS_PER_PAGE;

// Get events data
$events = [];
$total_events = 0;
$pagination = [];

try {
    $db = Database::getInstance()->getConnection();
    $timetableEvent = new TimetableEvent($db);
    
    $filters = [];
    if (!empty($search)) {
        // Format for SQL LIKE query
        $filters['search'] = "%" . $search . "%";
    }
    if (!empty($date_filter)) {
        $filters['start_date'] = $date_filter;
        $filters['end_date'] = $date_filter;
    }
    
    // Filter by teacher if not admin
    if (!isAdmin()) {
        $filters['teacher_id'] = $current_user['id'];
    }
    
    $events = $timetableEvent->readAll($filters, $from_record_num, RECORDS_PER_PAGE);
    $total_events = $timetableEvent->countAll($filters);
    $pagination = paginate($total_events, $page);
    
    // Get dropdown data
    $subjects = $db->query("SELECT id, subject_code, subject_name FROM subjects WHERE is_active = 1 ORDER BY subject_name")->fetchAll();
    $rooms = $db->query("SELECT id, room_code, room_name FROM rooms WHERE is_active = 1 ORDER BY room_name")->fetchAll();
    $timeSlots = $db->query("SELECT id, slot_name, start_time, end_time FROM time_slots WHERE is_active = 1 ORDER BY start_time")->fetchAll();
    $classes = $db->query("SELECT id, class_code, class_name FROM classes WHERE is_active = 1 ORDER BY class_name")->fetchAll();
    $teachers = $db->query("SELECT id, first_name, last_name FROM users WHERE role = 'teacher' AND is_active = 1 ORDER BY first_name")->fetchAll();
    
} catch (Exception $e) {
    error_log("Timetable listing error: " . $e->getMessage());
    $events = [];
    $subjects = [];
    $rooms = [];
    $timeSlots = [];
    $classes = [];
    $teachers = [];
}

// Get flash message
$flash = getFlashMessage();
if ($flash) {
    $message = $flash['message'];
    $message_type = $flash['type'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timetable Management - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/main_nav.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo escape($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-clock me-2"></i>Timetable Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                        <i class="fas fa-plus me-2"></i>Create Event
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?php echo escape($search); ?>" placeholder="Search events by title">
                            </div>
                            <div class="col-md-4">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" 
                                       value="<?php echo escape($date_filter); ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <a href="timetable.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Clear
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" id="events-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Events (<?php echo $total_events; ?> total)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (empty($events)): ?>
                                <p class="text-muted text-center mt-3">No events found.</p>
                            <?php else: ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Room</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="events-table-body">
                                        <?php foreach ($events as $event): ?>
                                            <tr>
                                                <td><?php echo formatDate($event['event_date']); ?></td>
                                                <td><?php echo formatTime($event['start_time']) . ' - ' . formatTime($event['end_time']); ?></td>
                                                <td><?php echo escape($event['title']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($event['event_type']) {
                                                            'class' => 'primary',
                                                            'exam' => 'danger',
                                                            'meeting' => 'warning',
                                                            'break' => 'success',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst(escape($event['event_type'])); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo escape($event['subject_name'] ?? ''); ?></td>
                                                <td><?php echo escape($event['teacher_first_name'] . ' ' . $event['teacher_last_name']); ?></td>
                                                <td><?php echo escape($event['room_name'] ?? ''); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="editEvent(<?php echo htmlspecialchars(json_encode($event)); ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <?php if (!$event['is_cancelled']): ?>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="cancelEvent(<?php echo $event['id']; ?>, '<?php echo escape($event['title']); ?>')">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteEvent(<?php echo $event['id']; ?>, '<?php echo escape($event['title']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Pagination -->
                        <nav aria-label="Events pagination" id="pagination-container">
                            <?php include 'includes/pagination.php'; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Event Modal -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="create_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_event_type" class="form-label">Event Type *</label>
                                    <select class="form-select" id="create_event_type" name="event_type" required>
                                        <option value="">Select Type</option>
                                        <option value="class">Class</option>
                                        <option value="exam">Exam</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="break">Break</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_event_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="create_event_date" name="event_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_time_slot_id" class="form-label">Time Slot *</label>
                                    <select class="form-select" id="create_time_slot_id" name="time_slot_id" required>
                                        <option value="">Select Time Slot</option>
                                        <?php foreach ($timeSlots as $slot): ?>
                                            <option value="<?php echo $slot['id']; ?>">
                                                <?php echo escape($slot['slot_name']) . ' (' . formatTime($slot['start_time']) . ' - ' . formatTime($slot['end_time']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <?php if (isTeacher()): ?>
                                        <label for="teacher_id" class="form-label">Teacher</label>
                                        <input type="text" class="form-control" value="<?php echo escape($current_user['first_name'] . ' ' . $current_user['last_name']); ?>" readonly>
                                        <input type="hidden" name="teacher_id" value="<?php echo $current_user['id']; ?>">
                                    <?php else: // For Admins ?>
                                        <label for="create_teacher_id" class="form-label">Teacher *</label>
                                        <select class="form-select" id="create_teacher_id" name="teacher_id" required>
                                            <option value="">Select Teacher</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?php echo $teacher['id']; ?>">
                                                    <?php echo escape($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_subject_id" class="form-label">Subject</label>
                                    <select class="form-select" id="create_subject_id" name="subject_id">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>">
                                                <?php echo escape($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_class_id" class="form-label">Class</label>
                                    <select class="form-select" id="create_class_id" name="class_id">
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['id']; ?>">
                                                <?php echo escape($class['class_code'] . ' - ' . $class['class_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="create_room_id" class="form-label">Room</label>
                                    <select class="form-select" id="create_room_id" name="room_id">
                                        <option value="">Select Room</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <option value="<?php echo $room['id']; ?>">
                                                <?php echo escape($room['room_code'] . ' - ' . $room['room_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="create_description" class="form-label">Description</label>
                            <textarea class="form-control" id="create_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="event_id" id="edit_event_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_event_type" class="form-label">Event Type *</label>
                                    <select class="form-select" id="edit_event_type" name="event_type" required>
                                        <option value="">Select Type</option>
                                        <option value="class">Class</option>
                                        <option value="exam">Exam</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="break">Break</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_event_date" class="form-label">Date *</label>
                                    <input type="date" class="form-control" id="edit_event_date" name="event_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_time_slot_id" class="form-label">Time Slot *</label>
                                    <select class="form-select" id="edit_time_slot_id" name="time_slot_id" required>
                                        <option value="">Select Time Slot</option>
                                        <?php foreach ($timeSlots as $slot): ?>
                                            <option value="<?php echo $slot['id']; ?>">
                                                <?php echo escape($slot['slot_name']) . ' (' . formatTime($slot['start_time']) . ' - ' . formatTime($slot['end_time']) . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_teacher_id_select" class="form-label">Teacher *</label>
                                    <select class="form-select" id="edit_teacher_id_select" name="teacher_id" required>
                                        <option value="">Select Teacher</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?php echo $teacher['id']; ?>"><?php echo escape($teacher['first_name'] . ' ' . $teacher['last_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_subject_id" class="form-label">Subject</label>
                                    <select class="form-select" id="edit_subject_id" name="subject_id">
                                        <option value="">Select Subject</option>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['id']; ?>"><?php echo escape($subject['subject_code'] . ' - ' . $subject['subject_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_class_id" class="form-label">Class</label>
                                    <select class="form-select" id="edit_class_id" name="class_id">
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['id']; ?>">
                                                <?php echo escape($class['class_code'] . ' - ' . $class['class_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_room_id" class="form-label">Room</label>
                                    <select class="form-select" id="edit_room_id" name="room_id">
                                        <option value="">Select Room</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <option value="<?php echo $room['id']; ?>">
                                                <?php echo escape($room['room_code'] . ' - ' . $room['room_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/debounce@2.1.0/dist/debounce.min.js"></script>
    <script>
        const APP_URL = '<?php echo rtrim(APP_URL, "/"); ?>';
        function editEvent(eventData) {
            document.getElementById('edit_event_id').value = eventData.id;
            document.getElementById('edit_title').value = eventData.title;
            document.getElementById('edit_description').value = eventData.description;
            document.getElementById('edit_event_type').value = eventData.event_type;
            document.getElementById('edit_subject_id').value = eventData.subject_id;
            document.getElementById('edit_class_id').value = eventData.class_id;
            
            // Handle teacher field based on user role
            document.getElementById('edit_teacher_id_select').value = eventData.teacher_id;
            document.getElementById('edit_room_id').value = eventData.room_id;
            document.getElementById('edit_time_slot_id').value = eventData.time_slot_id;
            document.getElementById('edit_event_date').value = eventData.event_date;
            
            new bootstrap.Modal(document.getElementById('editEventModal')).show();
        }
        
        function cancelEvent(eventId, eventTitle) {
            if (confirm('Are you sure you want to cancel "' + eventTitle + '"?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="event_id" value="${eventId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteEvent(eventId, eventTitle) {
            if (confirm('Are you sure you want to delete "' + eventTitle + '"? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="event_id" value="${eventId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Auto-dismiss alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Set today's date as default for create modal
        if(document.getElementById('create_event_date')) {
            document.getElementById('create_event_date').value = new Date().toISOString().split('T')[0];
        }

        // --- Instant Search Implementation ---
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const dateInput = document.getElementById('date');
            const tableBody = document.getElementById('events-table-body');
            const headerCount = document.querySelector('#events-header h5');
            const paginationContainer = document.getElementById('pagination-container');
            const tableResponsive = document.querySelector('.table-responsive');
            let currentPage = 1;

            const fetchEvents = () => {
                const searchQuery = searchInput.value;
                const dateQuery = dateInput.value;
                
                tableResponsive.classList.add('loading');

                const url = `${APP_URL}/api/search_events.php?search=${encodeURIComponent(searchQuery)}&date=${encodeURIComponent(dateQuery)}&page=${currentPage}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.error}</td></tr>`;
                            return;
                        }
                        
                        tableBody.innerHTML = data.html;
                        headerCount.innerHTML = `<i class="fas fa-list me-2"></i>Events (${data.count} total)`;
                        paginationContainer.innerHTML = data.pagination;
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                        tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">An error occurred while searching.</td></tr>`;
                    })
                    .finally(() => {
                        tableResponsive.classList.remove('loading');
                    });
            };

            // Use debounce to avoid sending too many requests while typing
            const debouncedFetch = debounce(fetchEvents, 300);

            searchInput.addEventListener('keyup', (e) => {
                currentPage = 1; // Reset to first page on new search
                debouncedFetch();
            });
            dateInput.addEventListener('change', () => {
                currentPage = 1; // Reset to first page on date change
                fetchEvents();
            });
            paginationContainer.addEventListener('click', function(e) {
                if (e.target.matches('a.page-link')) {
                    e.preventDefault();
                    currentPage = e.target.dataset.page;
                    fetchEvents();
                }
            });
        });
    </script>
</body>
</html>
