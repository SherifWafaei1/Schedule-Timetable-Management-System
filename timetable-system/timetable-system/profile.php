<?php
/**
 * Profile Entry Point
 */
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'controllers/ProfileController.php';
require_once 'includes/functions.php';

$db = Database::getInstance()->getConnection();

$controller = new ProfileController($db);

$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'update_profile':
            $controller->update();
            break;
        case 'change_password':
            $controller->changePassword();
            break;
        default:
            $controller->index();
            break;
    }
} else {
    $controller->index();
}
?>
