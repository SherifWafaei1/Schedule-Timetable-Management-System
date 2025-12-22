<?php
/**
 * Auth Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../includes/classes/AuthService.php';

class AuthController extends BaseController {
    private $authService;

    public function __construct($db) {
        parent::__construct($db);
        $this->authService = new AuthService($db);
    }

    /**
     * Show login page
     */
    public function showLogin() {
        if ($this->session->isLoggedIn()) {
            $this->redirect('admin/dashboard.php');
        }
        
        $flash = $this->flash->get();
        $this->render('auth/login', [
            'page_title' => 'Login',
            'flash' => $flash,
            'no_nav' => true,
            'extra_css' => ['login.css'],
            'body_class' => 'login-page',
            'container_class' => 'login-container'
        ]);
    }

    /**
     * Handle login request
     */
    public function login() {
        $username = $this->input->sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $csrf_token = $_POST['csrf_token'] ?? '';
        
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request. Please try again.', 'danger');
            $this->redirect('login.php');
        }

        $errors = $this->input->validateRequired($_POST, ['username', 'password']);
        
        if (empty($errors)) {
            try {
                if ($this->authService->login($username, $password)) {
                    logAuditTrail('LOGIN_SUCCESS', 'users', $this->authService->getCurrentUser()['id']);
                    $this->flash->set('Login successful!', 'success');
                    $this->redirect('admin/dashboard.php');
                } else {
                    logAuditTrail('LOGIN_FAILED', 'users', null, null, ['username' => $username]);
                    $this->flash->set(ERROR_INVALID_CREDENTIALS, 'danger');
                    $this->redirect('login.php');
                }
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
                $this->flash->set(ERROR_DATABASE_ERROR, 'danger');
                $this->redirect('login.php');
            }
        } else {
            $this->flash->set('Please fill in all required fields.', 'danger');
            $this->redirect('login.php');
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        if ($this->session->isLoggedIn()) {
            try {
                // Log the logout
                logAuditTrail('LOGOUT', 'users', $this->session->getUser()['id']);
            } catch (Exception $e) {
                error_log("Logout audit error: " . $e->getMessage());
            }
        }
        
        $this->authService->logout(); // This handles session destroy and cookie clearing
        $this->flash->set('You have been logged out successfully.', 'info');
        $this->redirect('login.php');
    }
}
?>
