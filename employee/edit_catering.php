<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_food.php'); exit; }
$pageTitle = 'Edit Catering';
$actionUrl = 'edit_catering.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT id, name, details, price FROM catering WHERE id = :id LIMIT 1');
$stmt->execute(['id'=>$id]);
$c = $stmt->fetch(); if (!$c) { header('Location: admin_food.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $c['name'] = $name; $c['details'] = trim($_POST['details'] ?? ''); $c['price'] = trim($_POST['price'] ?? '');
    if ($name === '') { $error = 'Name is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE catering SET name=:name, details=:details, price=:price WHERE id=:id');
        $stmt->execute(['name'=>$c['name'], 'details'=>$c['details'], 'price'=>$c['price'], 'id'=>$id]);
        header('Location: admin_food.php'); exit;
    }
}
include __DIR__ . '/views/catering_form.html';
