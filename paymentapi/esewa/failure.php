<?php
// eSewa Failure Callback - paymentapi/esewa/failure.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include "../../config/connection.php";

// Get parameters from eSewa callback
$order_id = $_GET['order_id'] ?? '';
$transaction_code = $_GET['transaction_code'] ?? '';
$status = $_GET['status'] ?? '';
$total_amount = $_GET['total_amount'] ?? '';

// Log failure callback
error_log("eSewa Payment Failed - Order ID: $order_id, Status: $status, Parameters: " . print_r($_GET, true));

if (empty($order_id)) {
    error_log("eSewa Failure - No order_id provided");
    header("Location: ../../thankyou.php?status=failed&method=esewa&error=no_order_id");
    exit();
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Check if payment record exists
    $check_sql = "SELECT id FROM payments WHERE order_id = ? AND payment_method = 'esewa' LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing payment record
        $update_sql = "UPDATE payments SET 
                      payment_status = 'failed',
                      updated_at = NOW()
                      WHERE order_id = ? AND payment_method = 'esewa'";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment: " . $stmt->error);
        }
    } else {
        // This shouldn't happen if process-payment.php works correctly
        error_log("eSewa Failure - No payment record found for order_id: $order_id");
        
        // Insert failed payment record
        $insert_sql = "INSERT INTO payments 
                      (order_id, amount, payment_method, payment_status, payment_date, created_at, updated_at)
                      VALUES (?, ?, 'esewa', 'failed', NOW(), NOW(), NOW())";
        $stmt = $conn->prepare($insert_sql);
        $amount = floatval($total_amount);
        $stmt->bind_param("id", $order_id, $amount);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert failed payment: " . $stmt->error);
        }
    }
    
    // Update order status to cancelled
    $order_sql = "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("i", $order_id);
    
    if (!$order_stmt->execute()) {
        throw new Exception("Failed to update order: " . $order_stmt->error);
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Clear session data
    unset($_SESSION['pending_order_id'], $_SESSION['order_total'], $_SESSION['payment_id']);
    unset($_SESSION['pending_payment_order_id'], $_SESSION['pending_payment_amount']);
    
    error_log("eSewa Failure - Payment marked as failed for order_id: $order_id");
    
} catch (Exception $e) {
    // Rollback transaction
    mysqli_rollback($conn);
    error_log("eSewa Failure - Database error: " . $e->getMessage());
}

// Redirect to failure page
header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=esewa");
exit();
?>