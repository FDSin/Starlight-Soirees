<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['event_id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = :event_id');
            $stmt->execute(['event_id' => $id]);
            header('Location: admin_events.php');
            exit;
        } catch (Exception $e) {
            $error = 'This event cannot be deleted while it has a payment record.';
        }
    }
}

$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$sql = 'SELECT e.event_id, e.title, e.event_date, e.guest_count, e.event_status,
               v.venue_name, m.package_name
        FROM events e
        LEFT JOIN venues v ON v.venue_id = e.venue_id
        LEFT JOIN menus m ON m.menu_id = e.menu_id
        WHERE 1=1';
$params = [];

if ($search !== '') {
    $sql .= ' AND (e.title LIKE :event_search OR v.venue_name LIKE :venue_search OR m.package_name LIKE :menu_search)';
    $params['event_search'] = '%' . $search . '%';
    $params['venue_search'] = '%' . $search . '%';
    $params['menu_search'] = '%' . $search . '%';
}
if ($status !== '') { $sql .= ' AND e.event_status = :status'; $params['status'] = $status; }
if ($dateFrom !== '') { $sql .= ' AND e.event_date >= :date_from'; $params['date_from'] = $dateFrom; }
if ($dateTo !== '') { $sql .= ' AND e.event_date <= :date_to'; $params['date_to'] = $dateTo; }

$sql .= ' ORDER BY e.event_date DESC, e.event_id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

include __DIR__ . '/views/events_list.html';
