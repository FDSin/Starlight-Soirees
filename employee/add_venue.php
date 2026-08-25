<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
$pageTitle = 'Add Venue';
$actionUrl = 'add_venue.php';
$error = '';
$venue = ['name' => '', 'address' => '', 'capacity' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $venue['name'] = $name;
    $venue['address'] = trim($_POST['address'] ?? '');
    $venue['capacity'] = trim($_POST['capacity'] ?? '');
    if ($name === '') { $error = 'Venue name is required.'; }
    else {
        $stmt = $pdo->prepare('INSERT INTO venues (name, address, capacity) VALUES (:name, :address, :capacity)');
        $stmt->execute(['name'=>$name, 'address'=>$venue['address'], 'capacity'=>$venue['capacity']]);
        header('Location: admin_venue.php'); exit;
    }
}
include __DIR__ . '/views/venue_form.html';
