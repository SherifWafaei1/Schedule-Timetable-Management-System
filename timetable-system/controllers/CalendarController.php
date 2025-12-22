<?php
/**
 * Calendar Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/TimetableEvent.php';

class CalendarController extends BaseController {
    private $timetableModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->timetableModel = new TimetableEvent($db);
    }

    /**
     * Show calendar view
     */
    public function index() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('login.php');
        }

        $view = $this->input->sanitize($_GET['view'] ?? 'month');
        $year = (int)($_GET['year'] ?? date('Y'));
        $month = (int)($_GET['month'] ?? date('n'));
        $date = $this->input->sanitize($_GET['date'] ?? date('Y-m-d'));

        if (!in_array($view, ['day', 'week', 'month'])) {
            $view = 'month';
        }

        $startDate = '';
        $endDate = '';

        switch ($view) {
            case 'day':
                $startDate = $date;
                $endDate = $date;
                break;
            case 'week':
                $startDate = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $endDate = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
                break;
            case 'month':
            default:
                $startDate = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
                $endDate = date('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
                break;
        }

        try {
            $currentUser = $this->session->getUser();
            $events = $this->timetableModel->getCalendarEvents($startDate, $endDate, $currentUser['id'], $currentUser['role']);
            $flash = $this->flash->get();

            $this->render('calendar/index', [
                'page_title' => 'Calendar View',
                'view' => $view,
                'year' => $year,
                'month' => $month,
                'date' => $date,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'events' => $events,
                'flash' => $flash,
                'current_user' => $currentUser
            ]);
        } catch (Exception $e) {
            error_log("Calendar error: " . $e->getMessage());
            $this->flash->set('An error occurred loading the calendar.', 'danger');
            $this->redirect('admin/dashboard.php');
        }
    }
}
?>
