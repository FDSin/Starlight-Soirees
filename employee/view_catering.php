<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_food.php'); exit; }
$stmt = $pdo->prepare('SELECT menu_id, package_name, price_per_person, description FROM menus WHERE menu_id = :menu_id');
$stmt->execute(['menu_id' => $id]);
$menu = $stmt->fetch();
if (!$menu) { header('Location: admin_food.php'); exit; }

include __DIR__ . '/views/catering_view.html';
