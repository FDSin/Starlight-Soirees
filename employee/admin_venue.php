<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM venues WHERE id = :id');
        $stmt->execute(['id' => $id]);
        header('Location: admin_venue.php'); exit;
    }
}

$venues = [];
$error = '';
try {
    $res = $pdo->query("SHOW TABLES LIKE 'venues'");
    if ($res && $res->rowCount() > 0) {
        $stmt = $pdo->query('SELECT id, name, address, capacity FROM venues ORDER BY name');
        $venues = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/venue_list.html';
