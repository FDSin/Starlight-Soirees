<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/resource_functions.php';
require_once __DIR__ . '/payment_functions.php';

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

    $error = validateVenue($venue);
    if ($error === '') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                'UPDATE venues SET venue_name = :venue_name, max_capacity = :max_capacity,
                 venue_price = :venue_price, location = :location WHERE venue_id = :venue_id'
            );
            $stmt->execute($venue);
            recalculatePaymentsForVenue($pdo, $id, (float)$venue['venue_price']);
            $pdo->commit();
            header('Location: admin_venue.php');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Venue could not be updated. Please try again.';
        }
    }
}

include __DIR__ . '/views/venue_form.html';
