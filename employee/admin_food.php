<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['food_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM food_catering WHERE food_id = :food_id');
        $stmt->execute(['food_id' => $id]);
        header('Location: admin_food.php'); exit;
    }
}

$catering = [];
$error = '';
$search = trim($_GET['search'] ?? '');
try {
    $res = $pdo->query("SHOW TABLES LIKE 'food_catering'");
    if ($res && $res->rowCount() > 0) {
        $stmt = $pdo->prepare('SELECT food_id, item_name, description, price FROM food_catering WHERE item_name LIKE :food_name_search OR description LIKE :food_description_search ORDER BY item_name');
        $stmt->execute([
            'food_name_search' => '%' . $search . '%',
            'food_description_search' => '%' . $search . '%',
        ]);
        $catering = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/catering_list.html';
