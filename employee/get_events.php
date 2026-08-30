<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../check_auth.php';
    require_once __DIR__ . '/../db.php';

    header('Content-Type: application/json');

    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;

    try {
        $sql = "SELECT 
                    event_id AS id,
                    CONCAT('Event #', event_id, ' (', event_status, ')') AS title,
                    event_date AS start,
                    CASE
                        WHEN event_status = 'Confirmed' THEN '#D4AF37'
                        WHEN event_status = 'Pending' THEN '#3B82F6'
                        WHEN event_status = 'Completed' THEN '#10B981'
                        ELSE '#EF4444'
                    END AS color
                FROM events";
        
        if ($start && $end) {
            $sql .= " WHERE event_date >= :start AND event_date <= :end";
        }

        $stmt = $pdo->prepare($sql);

        $params = [];
        if($start && $end) {
            $params['start'] = $start;
            $params['end'] = $end;
        }

        $stmt->execute($params);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($events);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error'=> $e->getMessage()]);
        exit();
    }

?>
