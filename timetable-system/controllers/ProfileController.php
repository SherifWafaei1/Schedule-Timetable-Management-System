<?php
/**
 * Profile Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController extends BaseController {
    private $userModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    /**
     * Show profile page
     */
    public function index() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('login.php');
        }

        $userId = $this->session->getUser()['id'];
        try {
            $this->userModel->id = $userId;
            $this->userModel->readOne();
            
            $flash = $this->flash->get();

            $this->render('profile/index', [
                'page_title' => 'My Profile',
                'user' => $this->userModel,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("Profile error: " . $e->getMessage());
            $this->flash->set('An error occurred loading your profile.', 'danger');
            $this->redirect('admin/dashboard.php');
        }
    }

    /**
     * Update profile information
     */
    public function update() {
        if (!$this->session->isLoggedIn()) $this->redirect('login.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('profile.php');
        }

        $userId = $this->session->getUser()['id'];
        $this->userModel->id = $userId;
        $this->userModel->username = $this->input->sanitize($_POST['username']);
        $this->userModel->email = $this->input->sanitize($_POST['email']);
        $this->userModel->first_name = $this->input->sanitize($_POST['first_name']);
        $this->userModel->last_name = $this->input->sanitize($_POST['last_name']);

        if ($this->userModel->updateProfile()) {
            // Update session
            $_SESSION['username'] = $this->userModel->username;
            $_SESSION['email'] = $this->userModel->email;
            $_SESSION['first_name'] = $this->userModel->first_name;
            $_SESSION['last_name'] = $this->userModel->last_name;
            
            $this->flash->set('Profile updated successfully.', 'success');
        } else {
            $this->flash->set('Failed to update profile.', 'danger');
        }
        $this->redirect('profile.php');
    }

    /**
     * Change password
     */
    public function changePassword() {
        if (!$this->session->isLoggedIn()) $this->redirect('login.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('profile.php');
        }

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $this->flash->set('New passwords do not match.', 'danger');
            $this->redirect('profile.php');
        }

        $userId = $this->session->getUser()['id'];
        $this->userModel->id = $userId;
        $this->userModel->readOne();

        if (verifyPassword($current_password, $this->userModel->password_hash)) {
            if ($this->userModel->updatePassword($new_password)) {
                $this->flash->set('Password updated successfully.', 'success');
            } else {
                $this->flash->set('Failed to update password.', 'danger');
            }
        } else {
            $this->flash->set('Current password is incorrect.', 'danger');
        }
        $this->redirect('profile.php');
    }
}
?>
