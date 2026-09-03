<?php
require_once __DIR__ . '/bootstrap.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['venue_id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM venues WHERE venue_id = :venue_id');
            $stmt->execute(['venue_id' => $id]);
            header('Location: admin_venue.php');
            exit;
        } catch (Exception $e) {
            $error = 'This venue is used by an event and cannot be deleted.';
        }
    }
}

$search = trim($_GET['search'] ?? '');
$stmt = $pdo->prepare(
    'SELECT venue_id, venue_name, max_capacity, venue_price, location
     FROM venues WHERE venue_name LIKE :name OR location LIKE :location ORDER BY venue_id ASC'
);
$stmt->execute(['name' => '%' . $search . '%', 'location' => '%' . $search . '%']);
$venues = $stmt->fetchAll();

include __DIR__ . '/views/venue_list.html';
