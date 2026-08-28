<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$error = '';
$reportData = [];

try {
    $res = $pdo->query("SHOW TABLES LIKE 'events'");
    if ($res && $res->rowCount() > 0) {
        if ($from && $to) {
            $stmt = $pdo->prepare('SELECT e.event_id, e.title, e.event_date, e.status, v.name AS venue_name FROM events e LEFT JOIN venues v ON v.venue_id = e.venue_id WHERE e.event_date BETWEEN :from AND :to ORDER BY e.event_date');
            $stmt->execute(['from' => $from, 'to' => $to]);
        } else {
            $stmt = $pdo->query('SELECT e.event_id, e.title, e.event_date, e.status, v.name AS venue_name FROM events e LEFT JOIN venues v ON v.venue_id = e.venue_id ORDER BY e.event_date');
        }
        $reportData = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/report.html';
