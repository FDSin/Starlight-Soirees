<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_payment.php'); exit; }
$pageTitle = 'Edit Payment';
$actionUrl = 'edit_payment.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT payment_id, event_id, amount, payment_method, payment_date AS date, status FROM payments WHERE payment_id = :payment_id LIMIT 1');
$stmt->execute(['payment_id'=>$id]);
$p = $stmt->fetch(); if (!$p) { header('Location: admin_payment.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $p['event_id'] = $event_id; $p['amount'] = trim($_POST['amount'] ?? ''); $p['date'] = $_POST['date'] ?? ''; $p['payment_method'] = trim($_POST['payment_method'] ?? ''); $p['status'] = $_POST['status'] ?? 'pending';
    if ($event_id === '') { $error = 'Event ID is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE payments SET event_id=:event_id, amount=:amount, payment_method=:payment_method, payment_date=:payment_date, status=:status WHERE payment_id=:payment_id');
        $stmt->execute(['event_id'=>$p['event_id'], 'amount'=>$p['amount'], 'payment_method'=>$p['payment_method'], 'payment_date'=>$p['date'] ?: null, 'status'=>$p['status'], 'payment_id'=>$id]);
        header('Location: admin_payment.php'); exit;
    }
}
$events = $pdo->query('SELECT event_id, title FROM events ORDER BY event_date DESC, event_id DESC')->fetchAll();
include __DIR__ . '/views/payment_form.html';
