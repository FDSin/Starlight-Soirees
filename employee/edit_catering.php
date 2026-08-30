<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_food.php'); exit; }

$pageTitle = 'Edit Menu Package';
$actionUrl = 'edit_catering.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT menu_id, package_name, price_per_person, description FROM menus WHERE menu_id = :menu_id');
$stmt->execute(['menu_id' => $id]);
$menu = $stmt->fetch();
if (!$menu) { header('Location: admin_food.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu = [
        'menu_id' => $id,
        'package_name' => trim($_POST['package_name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price_per_person' => trim($_POST['price_per_person'] ?? ''),
    ];
    if ($menu['package_name'] === '') $error = 'Package name is required.';
    elseif (!is_numeric($menu['price_per_person']) || (float)$menu['price_per_person'] < 0) $error = 'Price per person must be a valid positive amount.';
    else {
        $stmt = $pdo->prepare(
            'UPDATE menus SET package_name = :package_name, price_per_person = :price_per_person,
             description = :description WHERE menu_id = :menu_id'
        );
        $stmt->execute($menu);
        $stmt = $pdo->prepare(
            'UPDATE payments p
             JOIN events e ON e.event_id = p.event_id
             JOIN venues v ON v.venue_id = e.venue_id
             SET p.total_amount = v.venue_price + (:menu_price * e.guest_count)
             WHERE e.menu_id = :menu_id'
        );
        $stmt->execute(['menu_price' => $menu['price_per_person'], 'menu_id' => $id]);
        header('Location: admin_food.php');
        exit;
    }
}

include __DIR__ . '/views/catering_form.html';
