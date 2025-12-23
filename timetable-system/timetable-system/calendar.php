<?php
/**
 * Calendar Entry Point
 */
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'controllers/CalendarController.php';
require_once 'includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new CalendarController($db);
$controller->index();
?>
