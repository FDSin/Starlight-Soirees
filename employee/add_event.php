<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$pageTitle = 'Add Event';
$event = ['name' => '', 'date' => '', 'venue' => '', 'status' => 'Pending'];
$actionUrl = 'add_event.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $event['name'] = $name;
    $event['date'] = $_POST['date'] ?? '';
    $event['venue'] = trim($_POST['venue'] ?? '');
    $event['status'] = $_POST['status'] ?? 'Pending';

    if ($name === '') {
        $error = 'Event name is required.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO events (name, date, venue, status) VALUES (:name, :date, :venue, :status)');
            $stmt->execute([
                'name' => $name,
                'date' => $event['date'],
                'venue' => $event['venue'],
                'status' => $event['status'],
            ]);
            header('Location: admin_events.php'); exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

include __DIR__ . '/views/event_form.html';
