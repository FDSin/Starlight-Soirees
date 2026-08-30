<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/event_functions.php';

$pageTitle = 'Add Event';
$actionUrl = 'add_event.php';
$error = '';
$event = [
    'title' => '', 'description' => '', 'venue_id' => '', 'menu_id' => '',
    'event_date' => '', 'event_time' => '', 'guest_count' => 1, 'event_status' => 'Pending'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'venue_id' => (int)($_POST['venue_id'] ?? 0),
        'menu_id' => (int)($_POST['menu_id'] ?? 0),
        'event_date' => $_POST['event_date'] ?? '',
        'event_time' => $_POST['event_time'] ?? '',
        'guest_count' => (int)($_POST['guest_count'] ?? 0),
        'event_status' => $_POST['event_status'] ?? 'Pending',
    ];

    $error = validateEvent($pdo, $event);
    if ($error === '') {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO events
                 (user_id, venue_id, menu_id, title, description, event_date, event_time, guest_count, event_status)
                 VALUES
                 (:user_id, :venue_id, :menu_id, :title, :description, :event_date, :event_time, :guest_count, :event_status)'
            );
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'venue_id' => $event['venue_id'] ?: null,
                'menu_id' => $event['menu_id'] ?: null,
                'title' => $event['title'],
                'description' => $event['description'],
                'event_date' => $event['event_date'],
                'event_time' => $event['event_time'] ?: null,
                'guest_count' => $event['guest_count'],
                'event_status' => $event['event_status'],
            ]);
            header('Location: admin_events.php');
            exit;
        } catch (Exception $e) {
            $error = 'Event could not be saved. Please check the entered information.';
        }
    }
}

[$venues, $menus] = getEventOptions($pdo);
include __DIR__ . '/views/event_form.html';
