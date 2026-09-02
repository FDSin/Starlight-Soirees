<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$expectsJson = defined('AUTH_JSON_RESPONSE') && AUTH_JSON_RESPONSE === true;

if (!isset($_SESSION['user_id'])) {
    if ($expectsJson) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required.']);
    } else {
        header('Location: ../login.php');
    }
    exit;
}

$timeoutDuration = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > $timeoutDuration) {
    session_unset();
    session_destroy();

    if ($expectsJson) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Your session has expired.']);
    } else {
        header('Location: ../login.php?error=timeout');
    }
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();
