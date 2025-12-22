<?php
/**
 * Export Strategy Interface
 * Part of the Strategy Design Pattern
 */
interface ExportStrategy {
    /**
     * Export data to a specific format
     * 
     * @param array $events Data to export
     * @param string $filename Suggested filename
     * @param array $options Additional options (dates, etc.)
     */
    public function export($events, $filename, $options = []);
}
?>
