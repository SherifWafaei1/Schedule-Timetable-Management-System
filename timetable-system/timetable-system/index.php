<?php
/**
 * Home Entry Point
 */
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'controllers/HomeController.php';
require_once 'includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new HomeController($db);
$controller->index();
?>
