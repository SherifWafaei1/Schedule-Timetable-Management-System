<?php
/**
 * Subject Management Entry Point
 */
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../controllers/SubjectController.php';
require_once '../includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new SubjectController($db);

$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    $controller->index();
}
?>