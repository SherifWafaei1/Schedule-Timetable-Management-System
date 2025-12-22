<?php
/**
 * Application Constants
 * Schedule Time Table Management System
 */

// Application Settings
define('APP_NAME', 'Schedule Time Table Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/timetable-system');
define('BASE_PATH', dirname(__DIR__));

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'csv', 'xlsx']);

// Pagination
define('RECORDS_PER_PAGE', 20);

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_STUDENT', 'student');

// Event Types
define('EVENT_CLASS', 'class');
define('EVENT_EXAM', 'exam');
define('EVENT_MEETING', 'meeting');
define('EVENT_BREAK', 'break');
define('EVENT_OTHER', 'other');

// Room Types
define('ROOM_CLASSROOM', 'classroom');
define('ROOM_LAB', 'lab');
define('ROOM_AUDITORIUM', 'auditorium');
define('ROOM_MEETING_ROOM', 'meeting_room');

// Recurrence Patterns
define('RECURRENCE_DAILY', 'daily');
define('RECURRENCE_WEEKLY', 'weekly');
define('RECURRENCE_MONTHLY', 'monthly');

// Time Formats
define('TIME_FORMAT', 'H:i:s');
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_TIME_FORMAT', 'g:i A');
define('DISPLAY_DATE_FORMAT', 'M j, Y');

// Error Messages
define('ERROR_INVALID_CREDENTIALS', 'Invalid username or password');
define('ERROR_ACCOUNT_LOCKED', 'Account temporarily locked due to multiple failed login attempts');
define('ERROR_SESSION_EXPIRED', 'Your session has expired. Please login again.');
define('ERROR_ACCESS_DENIED', 'Access denied. You do not have permission to perform this action.');
define('ERROR_VALIDATION_FAILED', 'Validation failed. Please check your input.');
define('ERROR_DATABASE_ERROR', 'A database error occurred. Please try again later.');

// Success Messages
define('SUCCESS_LOGIN', 'Login successful');
define('SUCCESS_LOGOUT', 'Logout successful');
define('SUCCESS_REGISTRATION', 'Registration successful');
define('SUCCESS_UPDATE', 'Update successful');
define('SUCCESS_DELETE', 'Delete successful');
define('SUCCESS_PASSWORD_RESET', 'Password reset successful');

// Email Settings (for password reset)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('FROM_EMAIL', 'noreply@timetable-system.com');
define('FROM_NAME', APP_NAME);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
ini_set('session.cookie_lifetime', SESSION_TIMEOUT);
?>
