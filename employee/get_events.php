<?php

define('AUTH_JSON_RESPONSE', true);
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json');

$start = $_GET['start'] ?? null;
$end = $_GET['end'] ?? null;

try {
    $sql = "SELECT
                event_id AS id,
                CONCAT('Event #', event_id, ' (', event_status, ')') AS title,
                event_date AS start,
                CASE
                    WHEN event_status = 'Confirmed' THEN '#D4AF37'
                    WHEN event_status = 'Pending' THEN '#3B82F6'
                    WHEN event_status = 'Completed' THEN '#10B981'
                    ELSE '#EF4444'
                END AS color
            FROM events";

    $params = [];
    if ($start && $end) {
        $sql .= ' WHERE event_date >= :start AND event_date < :end';
        $params = ['start' => $start, 'end' => $end];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Calendar events could not be loaded.']);
}
