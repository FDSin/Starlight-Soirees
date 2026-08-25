<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$stmt = $pdo->prepare('SELECT id, name, date, venue, status FROM events WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

include __DIR__ . '/views/event_view.html';
