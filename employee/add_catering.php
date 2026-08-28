<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$pageTitle = 'Add Catering';
$actionUrl = 'add_catering.php';
$error = '';
$c = ['item_name' => '', 'description' => '', 'price' => '', 'quantity' => 1, 'event_id' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = trim($_POST['item_name'] ?? '');
    $c['item_name'] = $itemName; $c['description'] = trim($_POST['description'] ?? ''); $c['price'] = trim($_POST['price'] ?? ''); $c['quantity'] = (int)($_POST['quantity'] ?? 1); $c['event_id'] = (int)($_POST['event_id'] ?? 0);
    if ($itemName === '') { $error = 'Name is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO food_catering (event_id, item_name, description, price, quantity) VALUES (:event_id, :item_name, :description, :price, :quantity)');
        $stmt->execute(['event_id'=>$c['event_id'] ?: null, 'item_name'=>$itemName, 'description'=>$c['description'], 'price'=>$c['price'], 'quantity'=>$c['quantity']]);
        header('Location: admin_food.php'); exit;
    }
}
$events = $pdo->query('SELECT event_id, title FROM events ORDER BY event_date DESC')->fetchAll();
include __DIR__ . '/views/catering_form.html';
