<?php
/**
 * Export Entry Point
 */
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'controllers/ExportController.php';
require_once 'includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new ExportController($db);
$controller->export();
?>
