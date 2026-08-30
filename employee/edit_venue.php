<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_venue.php'); exit; }

$pageTitle = 'Edit Venue';
$actionUrl = 'edit_venue.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare('SELECT venue_id, venue_name, max_capacity, venue_price, location FROM venues WHERE venue_id = :venue_id');
$stmt->execute(['venue_id' => $id]);
$venue = $stmt->fetch();
if (!$venue) { header('Location: admin_venue.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venue = [
        'venue_id' => $id,
        'venue_name' => trim($_POST['venue_name'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'max_capacity' => (int)($_POST['max_capacity'] ?? 0),
        'venue_price' => trim($_POST['venue_price'] ?? ''),
    ];

    if ($venue['venue_name'] === '') $error = 'Venue name is required.';
    elseif ($venue['max_capacity'] < 1) $error = 'Maximum capacity must be at least 1.';
    elseif (!is_numeric($venue['venue_price']) || (float)$venue['venue_price'] < 0) $error = 'Venue price must be a valid positive amount.';
    else {
        $stmt = $pdo->prepare(
            'UPDATE venues SET venue_name = :venue_name, max_capacity = :max_capacity,
             venue_price = :venue_price, location = :location WHERE venue_id = :venue_id'
        );
        $stmt->execute($venue);
        $stmt = $pdo->prepare(
            'UPDATE payments p
             JOIN events e ON e.event_id = p.event_id
             JOIN menus m ON m.menu_id = e.menu_id
             SET p.total_amount = :venue_price + (m.price_per_person * e.guest_count)
             WHERE e.venue_id = :venue_id'
        );
        $stmt->execute(['venue_price' => $venue['venue_price'], 'venue_id' => $id]);
        header('Location: admin_venue.php');
        exit;
    }
}

include __DIR__ . '/views/venue_form.html';
