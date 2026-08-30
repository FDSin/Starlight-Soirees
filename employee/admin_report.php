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
        $select = 'SELECT e.event_id, e.title, e.event_date, e.guest_count, e.event_status,
                          v.venue_name, m.package_name,
                          (COALESCE(v.venue_price, 0) + (COALESCE(m.price_per_person, 0) * e.guest_count)) AS estimated_total
                   FROM events e
                   LEFT JOIN venues v ON v.venue_id = e.venue_id
                   LEFT JOIN menus m ON m.menu_id = e.menu_id';
        if ($from && $to) {
            $stmt = $pdo->prepare($select . ' WHERE e.event_date BETWEEN :from AND :to ORDER BY e.event_date');
            $stmt->execute(['from' => $from, 'to' => $to]);
        } else {
            $stmt = $pdo->query($select . ' ORDER BY e.event_date');
        }
        $reportData = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/report.html';
