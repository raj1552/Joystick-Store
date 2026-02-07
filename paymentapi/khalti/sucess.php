<?php
// Khalti Success Callback
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../../config/connection.php";

$order_id = (int) ($_GET['order_id'] ?? 0);
$transaction_id = $_GET['pidx'] ?? '';
$status = $_GET['status'] ?? '';

if ($order_id > 0 && $status === 'Completed' && $transaction_id !== '') {
    // Update payment and order in DB
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'completed', transaction_id = ?, payment_date = NOW(), updated_at = NOW() WHERE order_id = ? AND payment_method = 'khalti'");
    if ($stmt) {
        $stmt->bind_param("si", $transaction_id, $order_id);
        $stmt->execute();
        $stmt2 = $conn->prepare("UPDATE orders SET status = 'confirmed', updated_at = NOW() WHERE id = ?");
        if ($stmt2) {
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();
        }
    }
    unset($_SESSION['pending_order_id'], $_SESSION['order_total'], $_SESSION['payment_id'], $_SESSION['pending_payment_order_id'], $_SESSION['pending_payment_amount']);

    // Decouple: send to SQS for async processing on AWS
    if (file_exists(__DIR__ . '/../../config/sqs_helper.php')) {
        require_once __DIR__ . '/../../config/sqs_helper.php';
        sqs_send_message('payment_completed', [
            'order_id'        => $order_id,
            'method'          => 'khalti',
            'status'          => 'completed',
            'transaction_id'  => $transaction_id,
        ]);
    }

    header("Location: ../../thankyou.php?status=success&order_id=$order_id&method=khalti&txn_id=$transaction_id");
    exit();
}

header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=khalti");
exit();