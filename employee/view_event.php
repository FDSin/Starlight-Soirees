<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$stmt = $pdo->prepare('SELECT e.event_id, e.title, e.description, e.event_date, e.event_time, e.status, v.name AS venue_name FROM events e LEFT JOIN venues v ON v.venue_id = e.venue_id WHERE e.event_id = :event_id LIMIT 1');
$stmt->execute(['event_id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

include __DIR__ . '/views/event_view.html';
