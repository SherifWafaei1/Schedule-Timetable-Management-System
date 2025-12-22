<?php
/**
 * Dashboard Entry Point
 */
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/DashboardController.php';
require_once '../includes/functions.php'; // For global helpers still in use

$db = Database::getInstance()->getConnection();

$controller = new DashboardController($db);
$controller->index();
?>
