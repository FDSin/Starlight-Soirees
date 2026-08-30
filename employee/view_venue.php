<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_venue.php'); exit; }
$stmt = $pdo->prepare('SELECT venue_id, venue_name, max_capacity, venue_price, location FROM venues WHERE venue_id = :venue_id');
$stmt->execute(['venue_id' => $id]);
$venue = $stmt->fetch();
if (!$venue) { header('Location: admin_venue.php'); exit; }

include __DIR__ . '/views/venue_view.html';
