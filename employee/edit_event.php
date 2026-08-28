<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$pageTitle = 'Edit Event';
$actionUrl = 'edit_event.php?id=' . $id;
$error = '';

$stmt = $pdo->prepare('SELECT event_id, title, description, venue_id, event_date, event_time, status FROM events WHERE event_id = :event_id LIMIT 1');
$stmt->execute(['event_id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

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
            $stmt = $pdo->prepare('UPDATE events SET title = :title, description = :description, venue_id = :venue_id, event_date = :event_date, event_time = :event_time, status = :status WHERE event_id = :event_id');
            $stmt->execute([
                'title' => $title,
                'description' => $event['description'],
                'venue_id' => $event['venue_id'] ?: null,
                'event_date' => $event['event_date'] ?: null,
                'event_time' => $event['event_time'] ?: null,
                'status' => $event['status'],
                'event_id' => $id,
            ]);
            header('Location: admin_events.php'); exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$venueStmt = $pdo->prepare(
    'SELECT v.venue_id, v.name
     FROM venues v
     WHERE NOT EXISTS (
         SELECT 1 FROM events e
         WHERE e.venue_id = v.venue_id AND e.event_id <> :event_id
     )
     ORDER BY v.name'
);
$venueStmt->execute(['event_id' => $id]);
$venues = $venueStmt->fetchAll();
include __DIR__ . '/views/event_form.html';
