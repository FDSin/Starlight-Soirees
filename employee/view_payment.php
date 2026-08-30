<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_payment.php'); exit; }
$stmt = $pdo->prepare(
    'SELECT p.payment_id, p.event_id, p.total_amount, p.payment_method, p.payment_status,
            p.receipt_file, p.payment_date, e.title
     FROM payments p JOIN events e ON e.event_id = p.event_id
     WHERE p.payment_id = :payment_id'
);
$stmt->execute(['payment_id' => $id]);
$p = $stmt->fetch();
if (!$p) { header('Location: admin_payment.php'); exit; }

include __DIR__ . '/views/payment_view.html';
