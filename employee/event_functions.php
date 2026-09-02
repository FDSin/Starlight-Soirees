<?php

require_once __DIR__ . '/../config.php';

function validateEvent(PDO $pdo, array $event, int $ignoreEventId = 0): string
{
    if ($event['title'] === '') return 'Event name is required.';
    if ($event['event_date'] === '') return 'Event date is required.';
    if ($event['guest_count'] < 1) return 'Guest count must be at least 1.';
    if (!in_array($event['event_status'], EVENT_STATUSES, true)) return 'Please choose a valid event status.';

    if ($event['venue_id']) {
        $stmt = $pdo->prepare('SELECT max_capacity FROM venues WHERE venue_id = :venue_id');
        $stmt->execute(['venue_id' => $event['venue_id']]);
        $venue = $stmt->fetch();
        if (!$venue) return 'The selected venue does not exist.';
        if ($event['guest_count'] > (int)$venue['max_capacity']) {
            return 'Guest count is higher than the selected venue capacity.';
        }

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM events
             WHERE venue_id = :venue_id AND event_id <> :event_id'
        );
        $stmt->execute([
            'venue_id' => $event['venue_id'],
            'event_id' => $ignoreEventId,
        ]);
        if ((int)$stmt->fetchColumn() > 0) return 'This venue is already assigned to another event.';
    }

    if ($event['menu_id']) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM menus WHERE menu_id = :menu_id');
        $stmt->execute(['menu_id' => $event['menu_id']]);
        if ((int)$stmt->fetchColumn() === 0) return 'The selected menu does not exist.';

        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM events
             WHERE menu_id = :menu_id AND event_id <> :event_id'
        );
        $stmt->execute([
            'menu_id' => $event['menu_id'],
            'event_id' => $ignoreEventId,
        ]);
        if ((int)$stmt->fetchColumn() > 0) return 'This menu package is already assigned to another event.';
    }

    return '';
}

function getEventOptions(PDO $pdo, int $currentEventId = 0): array
{
    $venueStmt = $pdo->prepare(
        'SELECT v.venue_id, v.venue_name, v.max_capacity, v.venue_price
         FROM venues v
         WHERE NOT EXISTS (
             SELECT 1 FROM events e
             WHERE e.venue_id = v.venue_id AND e.event_id <> :current_event_id
         )
         ORDER BY v.venue_name'
    );
    $venueStmt->execute(['current_event_id' => $currentEventId]);

    $menuStmt = $pdo->prepare(
        'SELECT m.menu_id, m.package_name, m.price_per_person
         FROM menus m
         WHERE NOT EXISTS (
             SELECT 1 FROM events e
             WHERE e.menu_id = m.menu_id AND e.event_id <> :current_event_id
         )
         ORDER BY m.price_per_person'
    );
    $menuStmt->execute(['current_event_id' => $currentEventId]);

    return [$venueStmt->fetchAll(), $menuStmt->fetchAll()];
}
