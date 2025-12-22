<?php
/**
 * Class Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Class.php';

class ClassController extends BaseController {
    private $classModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->classModel = new SchoolClass($db);
    }

    /**
     * Show classes list
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }

        $page = (int)($_GET['page'] ?? 1);
        $from_record_num = ($page - 1) * RECORDS_PER_PAGE;

        try {
            $classes = $this->classModel->readAll($from_record_num, RECORDS_PER_PAGE);
            $total_classes = $this->classModel->countAll();
            $pagination = paginate($total_classes, $page);
            $flash = $this->flash->get();

            $this->render('admin/classes/index', [
                'page_title' => 'Class Management',
                'current_user' => $this->session->getUser(),
                'classes' => $classes,
                'total_classes' => $total_classes,
                'pagination' => $pagination,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("Class index error: " . $e->getMessage());
            $this->flash->set('An error occurred loading classes.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }
    }

    /**
     * Handle class creation
     */
    public function create() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('classes.php');
        }

        $this->classModel->class_code = $this->input->sanitize($_POST['class_code']);
        $this->classModel->class_name = $this->input->sanitize($_POST['class_name']);
        $this->classModel->description = $this->input->sanitize($_POST['description']);
        $this->classModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->classModel->classCodeExists()) {
            $this->flash->set('Class code already exists.', 'danger');
        } else {
            if ($this->classModel->create()) {
                $this->flash->set('Class created successfully.', 'success');
            } else {
                $this->flash->set('Failed to create class.', 'danger');
            }
        }
        $this->redirect('classes.php');
    }

    /**
     * Handle class update
     */
    public function update() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('classes.php');
        }

        $this->classModel->id = (int)$_POST['class_id'];
        $this->classModel->class_code = $this->input->sanitize($_POST['class_code']);
        $this->classModel->class_name = $this->input->sanitize($_POST['class_name']);
        $this->classModel->description = $this->input->sanitize($_POST['description']);
        $this->classModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->classModel->update()) {
            $this->flash->set('Class updated successfully.', 'success');
        } else {
            $this->flash->set('Failed to update class.', 'danger');
        }
        $this->redirect('classes.php');
    }

    /**
     * Handle class deletion
     */
    public function delete() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('classes.php');
        }

        $this->classModel->id = (int)$_POST['class_id'];
        if ($this->classModel->delete()) {
            $this->flash->set('Class deleted successfully.', 'success');
        } else {
            $this->flash->set('Failed to delete class. It might be in use.', 'danger');
        }
        $this->redirect('classes.php');
    }
}
?>
