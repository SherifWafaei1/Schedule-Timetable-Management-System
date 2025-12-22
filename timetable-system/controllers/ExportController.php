<?php
/**
 * Export Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/TimetableEvent.php';
require_once __DIR__ . '/../includes/classes/Export/CSVExportStrategy.php';
require_once __DIR__ . '/../includes/classes/Export/PDFExportStrategy.php';

class ExportController extends BaseController {
    private $timetableModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->timetableModel = new TimetableEvent($db);
    }

    /**
     * Handle export request
     */
    public function export() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('login.php');
        }

        $format = $this->input->sanitize($_GET['format'] ?? '');
        $start_date = $this->input->sanitize($_GET['start_date'] ?? date('Y-m-01'));
        $end_date = $this->input->sanitize($_GET['end_date'] ?? date('Y-m-t'));

        try {
            $filters = [
                'start_date' => $start_date,
                'end_date' => $end_date
            ];
            
            $currentUser = $this->session->getUser();
            if ($currentUser['role'] === 'teacher') {
                $filters['teacher_id'] = $currentUser['id'];
            } elseif ($currentUser['role'] === 'student') {
                $filters['student_id'] = $currentUser['id'];
            }
            
            $events = $this->timetableModel->readAll($filters, 0, 10000);
            $filename = 'timetable_export_' . date('Y-m-d') . '_' . $start_date . '_to_' . $end_date;

            // Strategy selection
            $strategy = null;
            switch ($format) {
                case 'csv':
                    $strategy = new CSVExportStrategy();
                    break;
                case 'pdf':
                    $strategy = new PDFExportStrategy($this);
                    break;
                default:
                    $this->flash->set('Invalid export format.', 'danger');
                    $this->redirect('admin/dashboard.php');
                    return;
            }

            $strategy->export($events, $filename, [
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);

        } catch (Exception $e) {
            error_log("Export error: " . $e->getMessage());
            $this->flash->set('Error generating export.', 'danger');
            $this->redirect('admin/dashboard.php');
        }
    }

}
?>
