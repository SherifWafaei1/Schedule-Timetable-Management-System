<?php
require_once 'tests/bootstrap.php';
try {
    $sm = SessionManager::getInstance();
    echo "SessionManager loaded successfully\n";
    $sm->set('test', 'value');
    if ($sm->get('test') === 'value') {
        echo "Set/Get working\n";
    }
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
