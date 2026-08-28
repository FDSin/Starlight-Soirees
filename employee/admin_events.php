<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['event_id'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare('DELETE FROM events WHERE event_id = :event_id');
            $stmt->execute(['event_id' => $id]);
            header('Location: admin_events.php'); exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$events = [];
$error = '';
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
try {
    $res = $pdo->query("SHOW TABLES LIKE 'events'");
    if ($res && $res->rowCount() > 0) {
        $sql = 'SELECT e.event_id, e.title, e.event_date, e.status, v.name AS venue_name
                FROM events e LEFT JOIN venues v ON v.venue_id = e.venue_id WHERE 1=1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (e.title LIKE :event_search OR v.name LIKE :venue_search)';
            $params['event_search'] = '%' . $search . '%';
            $params['venue_search'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $sql .= ' AND e.status = :status';
            $params['status'] = $status;
        }
        if ($dateFrom !== '') {
            $sql .= ' AND e.event_date >= :date_from';
            $params['date_from'] = $dateFrom;
        }
        if ($dateTo !== '') {
            $sql .= ' AND e.event_date <= :date_to';
            $params['date_to'] = $dateTo;
        }
        $sql .= ' ORDER BY e.event_date DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

include __DIR__ . '/views/events_list.html';
