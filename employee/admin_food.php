<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM catering WHERE id = :id');
        $stmt->execute(['id' => $id]);
        header('Location: admin_food.php'); exit;
    }
}

$catering = [];
$error = '';
try {
    $res = $pdo->query("SHOW TABLES LIKE 'catering'");
    if ($res && $res->rowCount() > 0) {
        $stmt = $pdo->query('SELECT id, name, details, price FROM catering ORDER BY name');
        $catering = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/catering_list.html';
