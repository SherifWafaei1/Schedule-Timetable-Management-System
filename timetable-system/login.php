<?php
/**
 * Login Entry Point
 */
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'controllers/AuthController.php';

$db = Database::getInstance()->getConnection();

$controller = new AuthController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
?>
