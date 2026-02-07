<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "../../config/connection.php";

// eSewa v2 API sends these parameters on success
$order_id = $_GET['order_id'] ?? '';
$transaction_code = $_GET['transaction_code'] ?? '';
$status = $_GET['status'] ?? '';
$total_amount = $_GET['total_amount'] ?? '';
$transaction_uuid = $_GET['transaction_uuid'] ?? '';
$product_code = $_GET['product_code'] ?? '';
$signed_field_names = $_GET['signed_field_names'] ?? '';
$signature = $_GET['signature'] ?? '';

error_log("eSewa Success Callback - Parameters: " . print_r($_GET, true));

// Validate required parameters
if (empty($order_id) || empty($transaction_code) || $status !== 'COMPLETE') {
    error_log("eSewa Success - Missing required parameters or status not COMPLETE");
    header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=esewa&error=invalid_callback");
    exit();
}

// Verify signature (optional but recommended)
$secret = "8gBm/:&EnhH.1/q"; // Same secret from index.php
$message = "transaction_code=$transaction_code,status=$status,total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code,signed_field_names=$signed_field_names";
$expected_signature = base64_encode(hash_hmac('sha256', $message, $secret, true));

if ($signature !== $expected_signature) {
    error_log("eSewa Success - Signature verification failed. Expected: $expected_signature, Got: $signature");
    // You can choose to continue or fail here - for now, we'll log and continue
}

// Verify this order exists and is in pending status
$check_sql = "SELECT id, amount FROM payments WHERE order_id = ? AND payment_method = 'esewa' AND payment_status = 'pending' LIMIT 1";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $order_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    error_log("eSewa Success - No pending payment found for order_id: $order_id");
    header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=esewa&error=no_pending_payment");
    exit();
}

$payment_row = $result->fetch_assoc();

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update payment record with transaction details
    $update_payment_sql = "UPDATE payments SET 
                          payment_status = 'completed',
                          transaction_id = ?,
                          payment_date = NOW(),
                          updated_at = NOW()
                          WHERE order_id = ? AND payment_method = 'esewa'";
    
    $stmt = $conn->prepare($update_payment_sql);
    $stmt->bind_param("si", $transaction_code, $order_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update payment: " . $stmt->error);
    }
    
    // Update order status to confirmed
    $update_order_sql = "UPDATE orders SET status = 'confirmed', updated_at = NOW() WHERE id = ?";
    $stmt2 = $conn->prepare($update_order_sql);
    $stmt2->bind_param("i", $order_id);
    
    if (!$stmt2->execute()) {
        throw new Exception("Failed to update order: " . $stmt2->error);
    }
    
    // Commit transaction
    mysqli_commit($conn);

    // Decouple: send to SQS for async processing (emails, analytics, etc.) on AWS
    if (file_exists(__DIR__ . '/../../config/sqs_helper.php')) {
        require_once __DIR__ . '/../../config/sqs_helper.php';
        sqs_send_message('payment_completed', [
            'order_id'        => (int) $order_id,
            'amount'          => (float) ($payment_row['amount'] ?? 0),
            'method'          => 'esewa',
            'status'          => 'completed',
            'transaction_id'  => $transaction_code,
        ]);
    }

    // Clear session data
    unset($_SESSION['pending_order_id'], $_SESSION['order_total'], $_SESSION['payment_id']);
    unset($_SESSION['pending_payment_order_id'], $_SESSION['pending_payment_amount']);
    
    error_log("eSewa Success - Payment completed for order_id: $order_id, transaction_code: $transaction_code");
    
    // Redirect to success page
    header("Location: ../../thankyou.php?status=success&order_id=$order_id&method=esewa&txn_id=$transaction_code");
    exit();
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    error_log("eSewa Success - Database error: " . $e->getMessage());
    header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=esewa&error=database_error");
    exit();
}
?>