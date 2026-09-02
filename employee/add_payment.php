<?php
require_once __DIR__ . '/bootstrap.php';
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

    $error = validatePayment($p);
    if ($error === '') {
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
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'Payment could not be saved. Please try again.';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$events = getPayableEvents($pdo);
include __DIR__ . '/views/payment_form.html';
