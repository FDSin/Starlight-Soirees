<?php
session_start();
require_once __DIR__ . '/../check_auth.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/payment_functions.php';

$pageTitle = 'Add Payment';
$actionUrl = 'add_payment.php';
$error = '';
$p = [
    'event_id' => '', 'total_amount' => '', 'payment_date' => date('Y-m-d\TH:i'),
    'payment_method' => '', 'payment_status' => 'Unpaid', 'receipt_file' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p['event_id'] = (int)($_POST['event_id'] ?? 0);
    $p['payment_method'] = $_POST['payment_method'] ?? '';
    $p['payment_status'] = $_POST['payment_status'] ?? 'Unpaid';
    $p['payment_date'] = $_POST['payment_date'] ?? '';
    $p['total_amount'] = calculateEventTotal($pdo, $p['event_id']);

    $allowedMethods = ['Bank Transfer', 'Credit Card'];
    $allowedStatuses = ['Unpaid', 'Pending Approval', 'Paid'];
    if (!$p['event_id']) $error = 'Please select an event.';
    elseif ($p['total_amount'] === null) $error = 'Choose a venue and menu for this event before creating its payment.';
    elseif (!in_array($p['payment_method'], $allowedMethods, true)) $error = 'Please select a payment method.';
    elseif (!in_array($p['payment_status'], $allowedStatuses, true)) $error = 'Please select a valid payment status.';
    else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM payments WHERE event_id = :event_id');
        $stmt->execute(['event_id' => $p['event_id']]);
        if ((int)$stmt->fetchColumn() > 0) {
            $error = 'This event already has a payment record. Please edit that payment instead.';
        } else {
            try {
                $p['receipt_file'] = saveReceipt($_FILES['receipt_file'] ?? []);
                $stmt = $pdo->prepare(
                    'INSERT INTO payments
                     (event_id, total_amount, payment_method, payment_status, receipt_file, payment_date)
                     VALUES (:event_id, :total_amount, :payment_method, :payment_status, :receipt_file, :payment_date)'
                );
                $stmt->execute([
                    'event_id' => $p['event_id'],
                    'total_amount' => $p['total_amount'],
                    'payment_method' => $p['payment_method'],
                    'payment_status' => $p['payment_status'],
                    'receipt_file' => $p['receipt_file'] ?: null,
                    'payment_date' => $p['payment_date'] ? str_replace('T', ' ', $p['payment_date']) : null,
                ]);
                header('Location: admin_payment.php');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$events = getPayableEvents($pdo);
include __DIR__ . '/views/payment_form.html';
