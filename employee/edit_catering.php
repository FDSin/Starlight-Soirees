<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/resource_functions.php';
require_once __DIR__ . '/payment_functions.php';

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
    $error = validateMenuPackage($menu);
    if ($error === '') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                'UPDATE menus SET package_name = :package_name, price_per_person = :price_per_person,
                 description = :description WHERE menu_id = :menu_id'
            );
            $stmt->execute($menu);
            recalculatePaymentsForMenu($pdo, $id, (float)$menu['price_per_person']);
            $pdo->commit();
            header('Location: admin_food.php');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Menu package could not be updated. Please try again.';
        }
    }
}

include __DIR__ . '/views/catering_form.html';
