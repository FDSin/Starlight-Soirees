<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['payment_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM payments WHERE payment_id = :payment_id');
        $stmt->execute(['payment_id' => $id]);
        header('Location: admin_payment.php'); exit;
    }
}

$payments = [];
$error = '';
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
try {
    $res = $pdo->query("SHOW TABLES LIKE 'payments'");
    if ($res && $res->rowCount() > 0) {
        $sql = 'SELECT payment_id, event_id, amount, payment_date, status FROM payments WHERE 1=1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (CAST(event_id AS CHAR) LIKE :payment_event_search OR status LIKE :payment_status_search)';
            $params['payment_event_search'] = '%' . $search . '%';
            $params['payment_status_search'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY payment_date DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $payments = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/payment_list.html';
