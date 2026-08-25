<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_food.php'); exit; }
$stmt = $pdo->prepare('SELECT id, name, details, price FROM catering WHERE id = :id LIMIT 1');
$stmt->execute(['id'=>$id]);
$c = $stmt->fetch(); if (!$c) { header('Location: admin_food.php'); exit; }
include __DIR__ . '/views/catering_view.html';
