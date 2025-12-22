<?php
/**
 * Flash Message Class
 */
class FlashMessage {
    private $sessionManager;

    public function __construct($sessionManager) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Set a flash message and optionally redirect
     */
    public function set($message, $type = 'info', $redirectUrl = null) {
        $this->sessionManager->set('message', $message);
        $this->sessionManager->set('message_type', $type);
        
        if ($redirectUrl) {
            header('Location: ' . $redirectUrl);
            exit();
        }
    }

    /**
     * Check if a flash message exists
     */
    public function hasFlash() {
        return $this->sessionManager->has('message');
    }

    /**
     * Get and clear flash message
     */
    public function get() {
        $message = $this->sessionManager->get('message');
        if ($message) {
            $type = $this->sessionManager->get('message_type') ?? 'info';
            $this->sessionManager->remove('message');
            $this->sessionManager->remove('message_type');
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }
}
?>
