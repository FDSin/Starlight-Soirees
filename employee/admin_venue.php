<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['venue_id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM venues WHERE venue_id = :venue_id');
        $stmt->execute(['venue_id' => $id]);
        header('Location: admin_venue.php'); exit;
    }
}

$venues = [];
$error = '';
$search = trim($_GET['search'] ?? '');
try {
    $res = $pdo->query("SHOW TABLES LIKE 'venues'");
    if ($res && $res->rowCount() > 0) {
        $stmt = $pdo->prepare('SELECT venue_id, name, address, capacity FROM venues WHERE name LIKE :venue_name_search OR address LIKE :venue_address_search ORDER BY name');
        $stmt->execute([
            'venue_name_search' => '%' . $search . '%',
            'venue_address_search' => '%' . $search . '%',
        ]);
        $venues = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/venue_list.html';
