<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$pageTitle = 'Add Event';
$event = ['title' => '', 'description' => '', 'venue_id' => '', 'event_date' => '', 'event_time' => '', 'status' => 'planned'];
$actionUrl = 'add_event.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $event['title'] = $title;
    $event['description'] = trim($_POST['description'] ?? '');
    $event['venue_id'] = (int)($_POST['venue_id'] ?? 0);
    $event['event_date'] = $_POST['event_date'] ?? '';
    $event['event_time'] = $_POST['event_time'] ?? '';
    $event['status'] = $_POST['status'] ?? 'planned';

    if ($title === '') {
        $error = 'Event name is required.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO events (title, description, venue_id, event_date, event_time, status) VALUES (:title, :description, :venue_id, :event_date, :event_time, :status)');
            $stmt->execute([
                'title' => $title,
                'description' => $event['description'],
                'venue_id' => $event['venue_id'] ?: null,
                'event_date' => $event['event_date'] ?: null,
                'event_time' => $event['event_time'] ?: null,
                'status' => $event['status'],
            ]);
            header('Location: admin_events.php'); exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$venues = $pdo->query(
    'SELECT v.venue_id, v.name
     FROM venues v
     WHERE NOT EXISTS (SELECT 1 FROM events e WHERE e.venue_id = v.venue_id)
     ORDER BY v.name'
)->fetchAll();
include __DIR__ . '/views/event_form.html';
