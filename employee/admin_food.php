<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['menu_id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM menus WHERE menu_id = :menu_id');
            $stmt->execute(['menu_id' => $id]);
            header('Location: admin_food.php');
            exit;
        } catch (Exception $e) {
            $error = 'This menu package is used by an event and cannot be deleted.';
        }
    }
}

$search = trim($_GET['search'] ?? '');
$stmt = $pdo->prepare(
    'SELECT menu_id, package_name, price_per_person, description FROM menus
     WHERE package_name LIKE :name OR description LIKE :description ORDER BY price_per_person'
);
$stmt->execute(['name' => '%' . $search . '%', 'description' => '%' . $search . '%']);
$menus = $stmt->fetchAll();

include __DIR__ . '/views/catering_list.html';
