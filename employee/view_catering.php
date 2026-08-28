<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_food.php'); exit; }
$stmt = $pdo->prepare('SELECT food_id, event_id, item_name, description, price, quantity FROM food_catering WHERE food_id = :food_id LIMIT 1');
$stmt->execute(['food_id' => $id]);
$c = $stmt->fetch(); if (!$c) { header('Location: admin_food.php'); exit; }
include __DIR__ . '/views/catering_view.html';
