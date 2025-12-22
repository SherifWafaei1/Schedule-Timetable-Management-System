<?php
/**
 * PDF Export Strategy
 * Concrete Implementation of Strategy Pattern
 */
require_once __DIR__ . '/ExportStrategy.php';

class PDFExportStrategy implements ExportStrategy {
    private $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function export($events, $filename, $options = []) {
        $data = [
            'events' => $events,
            'start_date' => $options['start_date'] ?? '',
            'end_date' => $options['end_date'] ?? '',
            'page_title' => 'Timetable Export'
        ];
        
        // Use the controller's render method
        $this->controller->render('export/pdf_view', $data, false);
    }
}
?>
