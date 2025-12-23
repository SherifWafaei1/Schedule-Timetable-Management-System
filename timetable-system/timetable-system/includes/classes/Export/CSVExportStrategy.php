<?php
/**
 * CSV Export Strategy
 * Concrete Implementation of Strategy Pattern
 */
require_once __DIR__ . '/ExportStrategy.php';

class CSVExportStrategy implements ExportStrategy {
    public function export($events, $filename, $options = []) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Start Time', 'End Time', 'Title', 'Type', 'Subject', 'Teacher', 'Class', 'Room', 'Description']);
        
        foreach ($events as $event) {
            fputcsv($output, [
                formatDate($event['event_date']),
                formatTime($event['start_time']),
                formatTime($event['end_time']),
                $event['title'],
                ucfirst($event['event_type']),
                $event['subject_name'] ?? '',
                $event['teacher_first_name'] . ' ' . $event['teacher_last_name'],
                $event['class_name'] ?? '',
                $event['room_name'] ?? '',
                $event['description'] ?? ''
            ]);
        }
        fclose($output);
        exit;
    }
}
?>
