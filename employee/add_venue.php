<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$pageTitle = 'Add Venue';
$actionUrl = 'add_venue.php';
$error = '';
$venue = ['venue_name' => '', 'location' => '', 'max_capacity' => '', 'venue_price' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $venue = [
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
            'INSERT INTO venues (venue_name, max_capacity, venue_price, location)
             VALUES (:venue_name, :max_capacity, :venue_price, :location)'
        );
        $stmt->execute($venue);
        header('Location: admin_venue.php');
        exit;
    }
}

include __DIR__ . '/views/venue_form.html';
