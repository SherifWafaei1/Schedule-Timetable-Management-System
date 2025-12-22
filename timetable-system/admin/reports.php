<?php
/**
 * Reports Management Entry Point
 */
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/ReportController.php';
require_once '../includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new ReportController($db);
$controller->index();
?>