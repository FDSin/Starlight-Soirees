<?php

function calculateEventTotal(PDO $pdo, int $eventId): ?float
{
    $stmt = $pdo->prepare(
        'SELECT v.venue_price, m.price_per_person, e.guest_count
         FROM events e
         JOIN venues v ON v.venue_id = e.venue_id
         JOIN menus m ON m.menu_id = e.menu_id
         WHERE e.event_id = :event_id'
    );
    $stmt->execute(['event_id' => $eventId]);
    $event = $stmt->fetch();

    if (!$event) return null;

    return round((float)$event['venue_price'] + ((float)$event['price_per_person'] * (int)$event['guest_count']), 2);
}

function saveReceipt(array $file, string $currentFile = ''): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return $currentFile;
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('The receipt could not be uploaded. Please try again.');
    if ($file['size'] > 5 * 1024 * 1024) throw new Exception('The receipt must be smaller than 5 MB.');

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];
    $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowedTypes[$mimeType])) throw new Exception('Receipt must be a JPG, PNG, or PDF file.');

    $folder = __DIR__ . '/../uploads/receipts';
    if (!is_dir($folder) && !mkdir($folder, 0775, true)) throw new Exception('The receipt folder could not be created.');

    $filename = uniqid('receipt_', true) . '.' . $allowedTypes[$mimeType];
    if (!move_uploaded_file($file['tmp_name'], $folder . '/' . $filename)) {
        throw new Exception('The receipt could not be saved.');
    }

    return 'uploads/receipts/' . $filename;
}

function getPayableEvents(PDO $pdo, int $currentPaymentId = 0): array
{
    $stmt = $pdo->prepare(
        'SELECT e.event_id, e.title,
                (v.venue_price + (m.price_per_person * e.guest_count)) AS calculated_total
         FROM events e
         JOIN venues v ON v.venue_id = e.venue_id
         JOIN menus m ON m.menu_id = e.menu_id
         WHERE NOT EXISTS (
             SELECT 1 FROM payments p
             WHERE p.event_id = e.event_id AND p.payment_id <> :current_payment_id
         )
         ORDER BY e.event_date DESC, e.event_id DESC'
    );
    $stmt->execute(['current_payment_id' => $currentPaymentId]);
    return $stmt->fetchAll();
}
