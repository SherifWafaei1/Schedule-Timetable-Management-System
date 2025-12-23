<?php
/**
 * Home Controller Class
 */
require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController {
    public function __construct($db = null) {
        parent::__construct($db);
    }

    /**
     * Show landing page
     */
    public function index() {
        if ($this->session->isLoggedIn()) {
            $this->redirect('admin/dashboard.php');
        }

        $flash = $this->flash->get();
        // Landing page shouldn't use the standard app layout
        $this->render('home/index', [
            'page_title' => 'Welcome to ' . APP_NAME,
            'flash' => $flash,
            'extra_css' => ['index.css'],
            'body_class' => 'landing'
        ], false); // No standard layout
    }
}
?>
