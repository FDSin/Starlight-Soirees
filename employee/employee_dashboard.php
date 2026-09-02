<?php
require_once __DIR__ . '/bootstrap.php';
$activeEvents = 0;
$pendingTasks = 0;
try {
    $stmt = $pdo->query(
        "SELECT
            SUM(event_status IN ('Pending', 'Confirmed')) AS active_events,
            SUM(event_status = 'Pending') AS pending_tasks
         FROM events"
    );
    $counts = $stmt->fetch();
    $activeEvents = (int)($counts['active_events'] ?? 0);
    $pendingTasks = (int)($counts['pending_tasks'] ?? 0);
} catch (Exception $e) {
    $activeEvents = 0;
    $pendingTasks = 0;
}
include __DIR__ . '/views/dashboard.html';
