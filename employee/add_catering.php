<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/resource_functions.php';

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
    $error = validateMenuPackage($menu);
    if ($error === '') {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO menus (package_name, price_per_person, description)
                 VALUES (:package_name, :price_per_person, :description)'
            );
            $stmt->execute($menu);
            header('Location: admin_food.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Menu package could not be saved. Please try again.';
        }
    }
}

include __DIR__ . '/views/catering_form.html';
