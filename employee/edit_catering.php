<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_food.php'); exit; }
$pageTitle = 'Edit Catering';
$actionUrl = 'edit_catering.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT food_id, event_id, item_name, description, price, quantity FROM food_catering WHERE food_id = :food_id LIMIT 1');
$stmt->execute(['food_id'=>$id]);
$c = $stmt->fetch(); if (!$c) { header('Location: admin_food.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = trim($_POST['item_name'] ?? '');
    $c['item_name'] = $itemName; $c['description'] = trim($_POST['description'] ?? ''); $c['price'] = trim($_POST['price'] ?? ''); $c['quantity'] = (int)($_POST['quantity'] ?? 1); $c['event_id'] = (int)($_POST['event_id'] ?? 0);
    if ($itemName === '') { $error = 'Name is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE food_catering SET event_id=:event_id, item_name=:item_name, description=:description, price=:price, quantity=:quantity WHERE food_id=:food_id');
        $stmt->execute(['event_id'=>$c['event_id'] ?: null, 'item_name'=>$c['item_name'], 'description'=>$c['description'], 'price'=>$c['price'], 'quantity'=>$c['quantity'], 'food_id'=>$id]);
        header('Location: admin_food.php'); exit;
    }
}
$events = $pdo->query('SELECT event_id, title FROM events ORDER BY event_date DESC')->fetchAll();
include __DIR__ . '/views/catering_form.html';
