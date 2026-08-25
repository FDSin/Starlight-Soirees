<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$pageTitle = 'Add Catering';
$actionUrl = 'add_catering.php';
$error = '';
$c = ['name' => '', 'details' => '', 'price' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $c['name'] = $name; $c['details'] = trim($_POST['details'] ?? ''); $c['price'] = trim($_POST['price'] ?? '');
    if ($name === '') { $error = 'Name is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO catering (name, details, price) VALUES (:name, :details, :price)');
        $stmt->execute(['name'=>$name, 'details'=>$c['details'], 'price'=>$c['price']]);
        header('Location: admin_food.php'); exit;
    }
}
include __DIR__ . '/views/catering_form.html';
