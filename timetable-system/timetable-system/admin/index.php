<?php
/**
 * Admin Console Entry Point
 */
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/AdminController.php';
require_once '../includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new AdminController($db);
$controller->index();
?>