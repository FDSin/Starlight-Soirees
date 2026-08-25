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
            $stmt = $pdo->prepare('SELECT id, name, date, venue, status FROM events WHERE date BETWEEN :from AND :to ORDER BY date');
            $stmt->execute(['from' => $from, 'to' => $to]);
        } else {
            $stmt = $pdo->query('SELECT id, name, date, venue, status FROM events ORDER BY date');
        }
        $reportData = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/report.html';
