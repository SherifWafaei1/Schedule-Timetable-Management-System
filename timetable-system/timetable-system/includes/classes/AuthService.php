<?php
/**
 * Auth Service Class
 */
require_once __DIR__ . '/SessionManager.php';
require_once __DIR__ . '/../../models/User.php';

class AuthService {
    private $db;
    private $user;
    private $sessionManager;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
        $this->sessionManager = SessionManager::getInstance();
    }

    public function login($username, $password) {
        if ($this->user->authenticate($username, $password)) {
            $this->sessionManager->setUserSession($this->user);
            return true;
        }
        return false;
    }

    public function logout() {
        $this->sessionManager->destroy();
    }

    public function getCurrentUser() {
        return $this->sessionManager->getUser();
    }
    
    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }

    public function isAdmin() {
        return $this->hasRole('admin');
    }

    public function isTeacher() {
        return $this->hasRole('teacher');
    }
    
    public function isStudent() {
        return $this->hasRole('student');
    }
}
?>
