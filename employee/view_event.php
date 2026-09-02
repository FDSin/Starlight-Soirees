<?php
require_once __DIR__ . '/bootstrap.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_events.php'); exit; }

$stmt = $pdo->prepare(
    'SELECT e.event_id, e.title, e.description, e.event_date, e.event_time,
            e.guest_count, e.event_status, v.venue_name, v.venue_price,
            m.package_name, m.price_per_person,
            (COALESCE(v.venue_price, 0) + (COALESCE(m.price_per_person, 0) * e.guest_count)) AS estimated_total
     FROM events e
     LEFT JOIN venues v ON v.venue_id = e.venue_id
     LEFT JOIN menus m ON m.menu_id = e.menu_id
     WHERE e.event_id = :event_id'
);
$stmt->execute(['event_id' => $id]);
$event = $stmt->fetch();
if (!$event) { header('Location: admin_events.php'); exit; }

include __DIR__ . '/views/event_view.html';
