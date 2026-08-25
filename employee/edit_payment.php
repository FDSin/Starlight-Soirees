<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_payment.php'); exit; }
$pageTitle = 'Edit Payment';
$actionUrl = 'edit_payment.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT id, event_id, amount, date, status FROM payments WHERE id = :id LIMIT 1');
$stmt->execute(['id'=>$id]);
$p = $stmt->fetch(); if (!$p) { header('Location: admin_payment.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $p['event_id'] = $event_id; $p['amount'] = trim($_POST['amount'] ?? ''); $p['date'] = $_POST['date'] ?? ''; $p['status'] = $_POST['status'] ?? 'Pending';
    if ($event_id === '') { $error = 'Event ID is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE payments SET event_id=:event_id, amount=:amount, date=:date, status=:status WHERE id=:id');
        $stmt->execute(['event_id'=>$p['event_id'], 'amount'=>$p['amount'], 'date'=>$p['date'], 'status'=>$p['status'], 'id'=>$id]);
        header('Location: admin_payment.php'); exit;
    }
}
include __DIR__ . '/views/payment_form.html';
