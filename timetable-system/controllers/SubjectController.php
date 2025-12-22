<?php
/**
 * Subject Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Subject.php';

class SubjectController extends BaseController {
    private $subjectModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->subjectModel = new Subject($db);
    }

    /**
     * Show subjects list
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied. Only administrators can manage subjects.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }

        $page = (int)($_GET['page'] ?? 1);
        $from_record_num = ($page - 1) * RECORDS_PER_PAGE;

        try {
            $subjects = $this->subjectModel->readAll($from_record_num, RECORDS_PER_PAGE);
            $total_subjects = $this->subjectModel->countAll();
            $pagination = paginate($total_subjects, $page);
            $flash = $this->flash->get();

            $this->render('admin/subjects/index', [
                'page_title' => 'Subject Management',
                'current_user' => $this->session->getUser(),
                'subjects' => $subjects,
                'total_subjects' => $total_subjects,
                'pagination' => $pagination,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("Subject index error: " . $e->getMessage());
            $this->flash->set('An error occurred loading subjects.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }
    }

    /**
     * Handle subject creation
     */
    public function create() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('subjects.php');
        }

        $this->subjectModel->subject_code = $this->input->sanitize($_POST['subject_code']);
        $this->subjectModel->subject_name = $this->input->sanitize($_POST['subject_name']);
        $this->subjectModel->description = $this->input->sanitize($_POST['description']);
        $this->subjectModel->credits = (int)$_POST['credits'];
        $this->subjectModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->subjectModel->subjectCodeExists()) {
            $this->flash->set('Subject code already exists.', 'danger');
        } else {
            if ($this->subjectModel->create()) {
                $this->flash->set('Subject created successfully.', 'success');
            } else {
                $this->flash->set('Failed to create subject.', 'danger');
            }
        }
        $this->redirect('subjects.php');
    }

    /**
     * Handle subject update
     */
    public function update() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('subjects.php');
        }

        $this->subjectModel->id = (int)$_POST['subject_id'];
        $this->subjectModel->subject_code = $this->input->sanitize($_POST['subject_code']);
        $this->subjectModel->subject_name = $this->input->sanitize($_POST['subject_name']);
        $this->subjectModel->description = $this->input->sanitize($_POST['description']);
        $this->subjectModel->credits = (int)$_POST['credits'];
        $this->subjectModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->subjectModel->update()) {
            $this->flash->set('Subject updated successfully.', 'success');
        } else {
            $this->flash->set('Failed to update subject.', 'danger');
        }
        $this->redirect('subjects.php');
    }

    /**
     * Handle subject deletion
     */
    public function delete() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('subjects.php');
        }

        $this->subjectModel->id = (int)$_POST['subject_id'];
        if ($this->subjectModel->delete()) {
            $this->flash->set('Subject deleted successfully.', 'success');
        } else {
            $this->flash->set('Failed to delete subject. It might be in use.', 'danger');
        }
        $this->redirect('subjects.php');
    }
}
?>
