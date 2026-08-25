<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$pageTitle = 'Edit Event';
$actionUrl = 'edit_event.php?id=' . $id;
$error = '';

$stmt = $pdo->prepare('SELECT id, name, date, venue, status FROM events WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

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
            $stmt = $pdo->prepare('UPDATE events SET name = :name, date = :date, venue = :venue, status = :status WHERE id = :id');
            $stmt->execute([
                'name' => $name,
                'date' => $event['date'],
                'venue' => $event['venue'],
                'status' => $event['status'],
                'id' => $id,
            ]);
            header('Location: admin_events.php'); exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

include __DIR__ . '/views/event_form.html';
