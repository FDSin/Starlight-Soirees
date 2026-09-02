<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/payment_functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: admin_payment.php'); exit; }

$pageTitle = 'Edit Payment';
$actionUrl = 'edit_payment.php?id=' . $id;
$error = '';
$stmt = $pdo->prepare(
    "SELECT payment_id, event_id, total_amount, payment_method, payment_status, receipt_file,
            DATE_FORMAT(payment_date, '%Y-%m-%dT%H:%i') AS payment_date
     FROM payments WHERE payment_id = :payment_id"
);
$stmt->execute(['payment_id' => $id]);
$p = $stmt->fetch();
if (!$p) { header('Location: admin_payment.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p['event_id'] = (int)($_POST['event_id'] ?? 0);
    $p['payment_method'] = $_POST['payment_method'] ?? '';
    $p['payment_status'] = $_POST['payment_status'] ?? 'Unpaid';
    $p['payment_date'] = $_POST['payment_date'] ?? '';
    $p['total_amount'] = calculateEventTotal($pdo, $p['event_id']);

    $error = validatePayment($p);
    if ($error === '') {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM payments WHERE event_id = :event_id AND payment_id <> :payment_id');
        $stmt->execute(['event_id' => $p['event_id'], 'payment_id' => $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            $error = 'This event already has another payment record.';
        } else {
            try {
                $p['receipt_file'] = saveReceipt($_FILES['receipt_file'] ?? [], $p['receipt_file'] ?? '');
                $stmt = $pdo->prepare(
                    'UPDATE payments SET event_id = :event_id, total_amount = :total_amount,
                     payment_method = :payment_method, payment_status = :payment_status,
                     receipt_file = :receipt_file, payment_date = :payment_date
                     WHERE payment_id = :payment_id'
                );
                $stmt->execute([
                    'event_id' => $p['event_id'],
                    'total_amount' => $p['total_amount'],
                    'payment_method' => $p['payment_method'],
                    'payment_status' => $p['payment_status'],
                    'receipt_file' => $p['receipt_file'] ?: null,
                    'payment_date' => $p['payment_date'] ? str_replace('T', ' ', $p['payment_date']) : null,
                    'payment_id' => $id,
                ]);
                header('Location: admin_payment.php');
                exit;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = 'Payment could not be updated. Please try again.';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}

$events = getPayableEvents($pdo, $id);
include __DIR__ . '/views/payment_form.html';
