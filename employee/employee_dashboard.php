<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); exit;
}

require_once __DIR__ . '/../db.php';
$activeEvents = 0;
$pendingTasks = 0;
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'events'");
    if ($stmt && $stmt->rowCount() > 0) {
        $eventStmt = $pdo->query("SELECT COUNT(*) AS total FROM events WHERE event_status IN ('Pending', 'Confirmed')");
        $eventRow = $eventStmt->fetch();
        $activeEvents = (int)($eventRow['total'] ?? 0);

        $pendingStmt = $pdo->query("SELECT COUNT(*) AS total FROM events WHERE event_status = 'Pending'");
        $pendingRow = $pendingStmt->fetch();
        $pendingTasks = (int)($pendingRow['total'] ?? 0);
    }
} catch (Exception $e) {
    $activeEvents = 0;
    $pendingTasks = 0;
}
include __DIR__ . '/views/dashboard.html';
