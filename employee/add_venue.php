<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/resource_functions.php';

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

    $error = validateVenue($venue);
    if ($error === '') {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO venues (venue_name, max_capacity, venue_price, location)
                 VALUES (:venue_name, :max_capacity, :venue_price, :location)'
            );
            $stmt->execute($venue);
            header('Location: admin_venue.php');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = 'Venue could not be saved. Please try again.';
        }
    }
}

include __DIR__ . '/views/venue_form.html';
