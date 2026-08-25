<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM payments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        header('Location: admin_payment.php'); exit;
    }
}

$payments = [];
$error = '';
try {
    $res = $pdo->query("SHOW TABLES LIKE 'payments'");
    if ($res && $res->rowCount() > 0) {
        $stmt = $pdo->query('SELECT id, event_id, amount, date, status FROM payments ORDER BY date DESC');
        $payments = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/payment_list.html';
