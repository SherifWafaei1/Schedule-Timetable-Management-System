<?php
/**
 * Room Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Room.php';

class RoomController extends BaseController {
    private $roomModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->roomModel = new Room($db);
    }

    /**
     * Show rooms list
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }

        $page = (int)($_GET['page'] ?? 1);
        $from_record_num = ($page - 1) * RECORDS_PER_PAGE;

        try {
            $rooms = $this->roomModel->readAll($from_record_num, RECORDS_PER_PAGE);
            $total_rooms = $this->roomModel->countAll();
            $pagination = paginate($total_rooms, $page);
            $flash = $this->flash->get();

            $this->render('admin/rooms/index', [
                'page_title' => 'Room Management',
                'current_user' => $this->session->getUser(),
                'rooms' => $rooms,
                'total_rooms' => $total_rooms,
                'pagination' => $pagination,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("Room index error: " . $e->getMessage());
            $this->flash->set('An error occurred loading rooms.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }
    }

    /**
     * Handle room creation
     */
    public function create() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('rooms.php');
        }

        $this->roomModel->room_code = $this->input->sanitize($_POST['room_code']);
        $this->roomModel->room_name = $this->input->sanitize($_POST['room_name']);
        $this->roomModel->capacity = (int)$_POST['capacity'];
        $this->roomModel->room_type = $this->input->sanitize($_POST['room_type']);
        $this->roomModel->location = $this->input->sanitize($_POST['location']);
        $this->roomModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->roomModel->roomCodeExists()) {
            $this->flash->set('Room code already exists.', 'danger');
        } else {
            if ($this->roomModel->create()) {
                $this->flash->set('Room created successfully.', 'success');
            } else {
                $this->flash->set('Failed to create room.', 'danger');
            }
        }
        $this->redirect('rooms.php');
    }

    /**
     * Handle room update
     */
    public function update() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('rooms.php');
        }

        $this->roomModel->id = (int)$_POST['room_id'];
        $this->roomModel->room_code = $this->input->sanitize($_POST['room_code']);
        $this->roomModel->room_name = $this->input->sanitize($_POST['room_name']);
        $this->roomModel->capacity = (int)$_POST['capacity'];
        $this->roomModel->room_type = $this->input->sanitize($_POST['room_type']);
        $this->roomModel->location = $this->input->sanitize($_POST['location']);
        $this->roomModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->roomModel->update()) {
            $this->flash->set('Room updated successfully.', 'success');
        } else {
            $this->flash->set('Failed to update room.', 'danger');
        }
        $this->redirect('rooms.php');
    }

    /**
     * Handle room deletion
     */
    public function delete() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('rooms.php');
        }

        $this->roomModel->id = (int)$_POST['room_id'];
        if ($this->roomModel->delete()) {
            $this->flash->set('Room deleted successfully.', 'success');
        } else {
            $this->flash->set('Failed to delete room. It might be in use.', 'danger');
        }
        $this->redirect('rooms.php');
    }
}
?>
