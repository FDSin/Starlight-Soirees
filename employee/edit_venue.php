<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$id = (int)($_GET['id'] ?? 0); if ($id <= 0) { header('Location: admin_venue.php'); exit; }
$pageTitle = 'Edit Venue';
$actionUrl = 'edit_venue.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT venue_id, name, address, capacity FROM venues WHERE venue_id = :venue_id LIMIT 1'); $stmt->execute(['venue_id'=>$id]); $venue = $stmt->fetch(); if (!$venue) { header('Location: admin_venue.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $venue['name'] = $name;
    $venue['address'] = trim($_POST['address'] ?? '');
    $venue['capacity'] = trim($_POST['capacity'] ?? '');
    if ($name === '') { $error = 'Venue name is required.'; }
    else {
        $stmt = $pdo->prepare('UPDATE venues SET name=:name, address=:address, capacity=:capacity WHERE venue_id=:venue_id');
        $stmt->execute(['name'=>$venue['name'], 'address'=>$venue['address'], 'capacity'=>$venue['capacity'], 'venue_id'=>$id]);
        header('Location: admin_venue.php'); exit;
    }
}
include __DIR__ . '/views/venue_form.html';
