<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/event_functions.php';
require_once __DIR__ . '/payment_functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$pageTitle = 'Edit Event';
$actionUrl = 'edit_event.php?id=' . $id;
$error = '';

$stmt = $pdo->prepare(
    'SELECT event_id, title, description, venue_id, menu_id, event_date, event_time, guest_count, event_status
     FROM events WHERE event_id = :event_id'
);
$stmt->execute(['event_id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = [
        'event_id' => $id,
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'venue_id' => (int)($_POST['venue_id'] ?? 0),
        'menu_id' => (int)($_POST['menu_id'] ?? 0),
        'event_date' => $_POST['event_date'] ?? '',
        'event_time' => $_POST['event_time'] ?? '',
        'guest_count' => (int)($_POST['guest_count'] ?? 0),
        'event_status' => $_POST['event_status'] ?? 'Pending',
    ];

    $error = validateEvent($pdo, $event, $id);
    if ($error === '' && (!$event['venue_id'] || !$event['menu_id'])) {
        $paymentStmt = $pdo->prepare('SELECT COUNT(*) FROM payments WHERE event_id = :event_id');
        $paymentStmt->execute(['event_id' => $id]);
        if ((int)$paymentStmt->fetchColumn() > 0) {
            $error = 'This event has a payment, so its venue and menu cannot be removed.';
        }
    }
    if ($error === '') {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                'UPDATE events SET venue_id = :venue_id, menu_id = :menu_id, title = :title,
                 description = :description, event_date = :event_date, event_time = :event_time,
                 guest_count = :guest_count, event_status = :event_status WHERE event_id = :event_id'
            );
            $stmt->execute([
                'venue_id' => $event['venue_id'] ?: null,
                'menu_id' => $event['menu_id'] ?: null,
                'title' => $event['title'],
                'description' => $event['description'],
                'event_date' => $event['event_date'],
                'event_time' => $event['event_time'] ?: null,
                'guest_count' => $event['guest_count'],
                'event_status' => $event['event_status'],
                'event_id' => $id,
            ]);
            recalculatePaymentForEvent($pdo, $id);
            $pdo->commit();
            header('Location: admin_events.php');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = 'Event could not be updated. Please check the entered information.';
        }
    }
}

[$venues, $menus] = getEventOptions($pdo, $id);
include __DIR__ . '/views/event_form.html';
