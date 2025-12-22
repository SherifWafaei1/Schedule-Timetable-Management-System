<?php
/**
 * Base Controller Class
 */
require_once __DIR__ . '/../includes/classes/SessionManager.php';
require_once __DIR__ . '/../includes/classes/InputValidator.php';
require_once __DIR__ . '/../includes/classes/FlashMessage.php';

class BaseController {
    protected $session;
    protected $input;
    protected $flash;
    protected $db;

    public function __construct($db = null) {
        $this->session = SessionManager::getInstance();
        $this->input = new InputValidator();
        $this->flash = new FlashMessage($this->session);
        $this->db = $db;
        
        $this->session->startSecureSession();
    }

    /**
     * Render a view file with layout
     */
    protected function render($viewName, $data = [], $layout = true) {
        // Automatically inject common data
        if (!isset($data['current_user'])) {
            $data['current_user'] = $this->session->getUser();
        }
        if (!isset($data['flash'])) {
            $data['flash'] = $this->flash->get();
        }

        // Extract data for use in the view
        extract($data);
        
        $viewPath = __DIR__ . '/../views/' . $viewName . '.php';
        $headerPath = __DIR__ . '/../views/layout/header.php';
        $footerPath = __DIR__ . '/../views/layout/footer.php';
        
        if (!file_exists($viewPath)) {
            error_log("View error: View $viewName not found at $viewPath");
            die("View $viewName not found at $viewPath");
        }

        if ($layout) {
            require $headerPath;
        }
        
        require $viewPath;
        
        if ($layout) {
            require $footerPath;
        }
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header('Location: ' . $url);
        exit();
    }

    /**
     * Send JSON response
     */
    protected function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    /**
     * Helper to check if current user is admin
     */
    protected function isAdmin() {
        return $this->session->get('role') === ROLE_ADMIN;
    }

    /**
     * Helper to check if current user is teacher
     */
    protected function isTeacher() {
        return $this->session->get('role') === ROLE_TEACHER;
    }
}
?>
