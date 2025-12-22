<?php
/**
 * Admin Dashboard Controller
 */
require_once __DIR__ . '/BaseController.php';

class AdminController extends BaseController {
    public function __construct($db) {
        parent::__construct($db);
    }

    /**
     * Show admin dashboard / index
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied.', 'danger');
            $this->redirect('../index.php');
        }

        try {
            // Aggregated system statistics
            $stats = [
                'users' => $this->db->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
                'events' => $this->db->query("SELECT COUNT(*) FROM timetable_events WHERE is_cancelled = 0")->fetchColumn(),
                'rooms' => $this->db->query("SELECT COUNT(*) FROM rooms WHERE is_active = 1")->fetchColumn(),
                'subjects' => $this->db->query("SELECT COUNT(*) FROM subjects WHERE is_active = 1")->fetchColumn(),
                'classes' => $this->db->query("SELECT COUNT(*) FROM classes WHERE is_active = 1")->fetchColumn()
            ];

            $flash = $this->flash->get();

            $this->render('admin/index', [
                'page_title' => 'Admin Console',
                'stats' => $stats,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("Admin index error: " . $e->getMessage());
            $this->flash->set('Error loading admin statistics.', 'danger');
            $this->redirect('dashboard.php');
        }
    }
}
?>
