<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['payment_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM payments WHERE payment_id = :payment_id');
        $stmt->execute(['payment_id' => $id]);
        header('Location: admin_payment.php');
        exit;
    }
}

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$sql = 'SELECT p.payment_id, p.event_id, p.total_amount, p.payment_date, p.payment_status, e.title
        FROM payments p JOIN events e ON e.event_id = p.event_id WHERE 1=1';
$params = [];
if ($search !== '') {
    $sql .= ' AND (e.title LIKE :title OR CAST(p.event_id AS CHAR) LIKE :event_id)';
    $params['title'] = '%' . $search . '%';
    $params['event_id'] = '%' . $search . '%';
}
if ($status !== '') { $sql .= ' AND p.payment_status = :status'; $params['status'] = $status; }
$sql .= ' ORDER BY p.payment_id ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

include __DIR__ . '/views/payment_list.html';
