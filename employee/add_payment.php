<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$pageTitle = 'Add Payment';
$actionUrl = 'add_payment.php';
$error = '';
$p = ['event_id' => '', 'amount' => '', 'date' => '', 'payment_method' => '', 'status' => 'pending'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = trim($_POST['event_id'] ?? '');
    $p['event_id'] = $event_id; $p['amount'] = trim($_POST['amount'] ?? ''); $p['date'] = $_POST['date'] ?? ''; $p['payment_method'] = trim($_POST['payment_method'] ?? ''); $p['status'] = $_POST['status'] ?? 'pending';
    if ($event_id === '') { $error = 'Event ID is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO payments (event_id, amount, payment_method, status, payment_date) VALUES (:event_id, :amount, :payment_method, :status, :payment_date)');
        $stmt->execute(['event_id'=>$event_id, 'amount'=>$p['amount'], 'payment_method'=>$p['payment_method'], 'status'=>$p['status'], 'payment_date'=>$p['date'] ?: null]);
        header('Location: admin_payment.php'); exit;
    }
}
$events = $pdo->query('SELECT event_id, title FROM events ORDER BY event_date DESC, event_id DESC')->fetchAll();
include __DIR__ . '/views/payment_form.html';
