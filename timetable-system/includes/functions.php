<?php
/**
 * Utility Functions
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/classes/SessionManager.php';
require_once __DIR__ . '/classes/InputValidator.php';
require_once __DIR__ . '/classes/FlashMessage.php';
require_once __DIR__ . '/classes/AuthService.php';

// Initialize instances for global helper functions
$sessionManager = SessionManager::getInstance();
$inputValidator = new InputValidator();
$flashMessage = new FlashMessage($sessionManager);

/**
 * Start secure session
 */
function startSecureSession() {
    global $sessionManager;
    $sessionManager->startSecureSession();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    global $sessionManager;
    return $sessionManager->isLoggedIn();
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    if (!isLoggedIn()) return false;
    $user = getUserFromSession();
    return $user['role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole(ROLE_ADMIN);
}

/**
 * Check if user is teacher
 */
function isTeacher() {
    return hasRole(ROLE_TEACHER);
}

/**
 * Check if user is student
 */
function isStudent() {
    return hasRole(ROLE_STUDENT);
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    header('Location: ' . APP_URL . '/login.php');
    exit();
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'info') {
    global $flashMessage;
    $flashMessage->set($message, $type, $url);
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    global $flashMessage;
    return $flashMessage->get();
}

/**
 * Get current user info from session.
 *
 * @return array|null User data array or null if not logged in.
 */
function getUserFromSession() {
    global $sessionManager;
    return $sessionManager->getUser();
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    global $inputValidator;
    return $inputValidator->sanitize($data);
}

/**
 * Validate email
 */
function isValidEmail($email) {
    global $inputValidator;
    return $inputValidator->isValidEmail($email);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date)) return '';
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj ? $dateObj->format($format) : $date;
}

/**
 * Format time for display
 */
function formatTime($time, $format = DISPLAY_TIME_FORMAT) {
    if (empty($time)) return '';
    $timeObj = DateTime::createFromFormat('H:i:s', $time);
    return $timeObj ? $timeObj->format($format) : $time;
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = 'M j, Y g:i A') {
    if (empty($datetime)) return '';
    $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    return $dateObj ? $dateObj->format($format) : $date;
}

/**
 * Get time difference in minutes
 */
function getTimeDifferenceInMinutes($startTime, $endTime) {
    $start = DateTime::createFromFormat('H:i:s', $startTime);
    $end = DateTime::createFromFormat('H:i:s', $endTime);
    
    if (!$start || !$end) return 0;
    
    $diff = $end->diff($start);
    return ($diff->h * 60) + $diff->i;
}

/**
 * Check if two time ranges overlap
 */
function timeRangesOverlap($start1, $end1, $start2, $end2) {
    $start1Time = strtotime($start1);
    $end1Time = strtotime($end1);
    $start2Time = strtotime($start2);
    $end2Time = strtotime($end2);
    
    return $start1Time < $end2Time && $start2Time < $end1Time;
}

/**
 * Log audit trail
 */
function logAuditTrail($action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
    global $db;
    
    // Create new connection if global db is not available
    if (!isset($db) || $db === null) {
        $db = Database::getInstance()->getConnection();
    }
    
    try {
        $stmt = $db->prepare("
            INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $action,
            $tableName,
            $recordId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
    }
}

/**
 * Validate required fields
 */
function validateRequired($data, $requiredFields) {
    global $inputValidator;
    return $inputValidator->validateRequired($data, $requiredFields);
}

/**
 * Paginate results
 */
function paginate($totalRecords, $currentPage = 1, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_records' => $totalRecords,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'records_per_page' => $recordsPerPage,
        'offset' => $offset,
        'has_next' => $currentPage < $totalPages,
        'has_prev' => $currentPage > 1
    ];
}

/**
 * Get user IP address
 */
function getUserIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Clean old sessions
 */
function cleanOldSessions() {
    global $db;
    
     // Create new connection if global db is not available
     if (!isset($db) || $db === null) {
        $db = Database::getInstance()->getConnection();
    }

    try {
        $stmt = $db->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Session cleanup error: " . $e->getMessage());
    }
}

/**
 * Escape output for HTML
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Debug function (remove in production)
 */
function debug($data) {
    if (defined('DEBUG') && DEBUG) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
?>
