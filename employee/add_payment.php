<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$pageTitle = 'Add Payment';
$actionUrl = 'add_payment.php';
$error = '';
$p = ['event_id' => '', 'amount' => '', 'date' => '', 'status' => 'Pending'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $p['event_id'] = $event_id; $p['amount'] = trim($_POST['amount'] ?? ''); $p['date'] = $_POST['date'] ?? ''; $p['status'] = $_POST['status'] ?? 'Pending';
    if ($event_id === '') { $error = 'Event ID is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO payments (event_id, amount, date, status) VALUES (:event_id, :amount, :date, :status)');
        $stmt->execute(['event_id'=>$event_id, 'amount'=>$p['amount'], 'date'=>$p['date'], 'status'=>$p['status']]);
        header('Location: admin_payment.php'); exit;
    }
}
include __DIR__ . '/views/payment_form.html';
