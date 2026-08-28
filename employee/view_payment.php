<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_payment.php'); exit; }
$stmt = $pdo->prepare('SELECT payment_id, event_id, amount, payment_method, payment_date AS date, status FROM payments WHERE payment_id = :payment_id LIMIT 1');
$stmt->execute(['payment_id'=>$id]);
$p = $stmt->fetch(); if (!$p) { header('Location: admin_payment.php'); exit; }
include __DIR__ . '/views/payment_view.html';
