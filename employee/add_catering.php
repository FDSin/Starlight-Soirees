<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$pageTitle = 'Add Menu Package';
$actionUrl = 'add_catering.php';
$error = '';
$menu = ['package_name' => '', 'description' => '', 'price_per_person' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu = [
        'package_name' => trim($_POST['package_name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price_per_person' => trim($_POST['price_per_person'] ?? ''),
    ];
    if ($menu['package_name'] === '') $error = 'Package name is required.';
    elseif (!is_numeric($menu['price_per_person']) || (float)$menu['price_per_person'] < 0) $error = 'Price per person must be a valid positive amount.';
    else {
        $stmt = $pdo->prepare(
            'INSERT INTO menus (package_name, price_per_person, description)
             VALUES (:package_name, :price_per_person, :description)'
        );
        $stmt->execute($menu);
        header('Location: admin_food.php');
        exit;
    }
}

include __DIR__ . '/views/catering_form.html';
