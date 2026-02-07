<?php
session_start();
include "./config/connection.php";

// This file should be called after successful payment from eSewa or Khalti
// You can call this from your payment gateway success callbacks

function updatePaymentStatus($order_id, $status, $transaction_id = null) {
    global $conn;
    
    try {
        // Start transaction
        mysqli_autocommit($conn, false);
        
        // Update payment status
        $update_sql = "UPDATE payments SET 
                       payment_status = ?, 
                       updated_at = NOW()";
        
        $params = [$status];
        $types = "s";
        
        // If transaction ID is provided, add it to the update
        if ($transaction_id) {
            $update_sql .= ", transaction_id = ?";
            $params[] = $transaction_id;
            $types .= "s";
        }
        
        $update_sql .= " WHERE order_id = ?";
        $params[] = $order_id;
        $types .= "s";
        
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update payment status');
        }
        
        // If payment is successful, update order status
        if ($status === 'successful' || $status === 'completed') {
            $order_update_sql = "UPDATE orders SET status = 'confirmed' WHERE id = ?";
            $order_stmt = mysqli_prepare($conn, $order_update_sql);
            mysqli_stmt_bind_param($order_stmt, "s", $order_id);
            mysqli_stmt_execute($order_stmt);
        }

        // Commit transaction
        mysqli_commit($conn);

        // Decouple: notify SQS for async processing on AWS
        if (($status === 'successful' || $status === 'completed') && file_exists(__DIR__ . '/config/sqs_helper.php')) {
            require_once __DIR__ . '/config/sqs_helper.php';
            sqs_send_message('payment_completed', [
                'order_id'        => (int) $order_id,
                'status'          => $status,
                'transaction_id'  => $transaction_id,
            ]);
        }
        
        return ['success' => true, 'message' => 'Payment status updated successfully'];
        
    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        return ['success' => false, 'message' => $e->getMessage()];
    } finally {
        // Reset autocommit
        mysqli_autocommit($conn, true);
    }
}

// Handle AJAX requests for payment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $status = $_POST['status'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? null;
    
    if (empty($order_id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    $result = updatePaymentStatus($order_id, $status, $transaction_id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

// Handle GET requests (for redirect callbacks from payment gateways)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $order_id = $_GET['order_id'] ?? '';
    $status = $_GET['status'] ?? '';
    $transaction_id = $_GET['transaction_id'] ?? null;
    
    if (!empty($order_id) && !empty($status)) {
        $result = updatePaymentStatus($order_id, $status, $transaction_id);
        
        if ($result['success']) {
            // Clear session data
            unset($_SESSION['pending_order_id']);
            unset($_SESSION['order_total']);
            unset($_SESSION['payment_id']);
            
            // Redirect to success page
            if ($status === 'successful' || $status === 'completed') {
                header("Location: ./thankyou.php?order_id=$order_id&status=success");
            } else {
                header("Location: ./thankyou.php?order_id=$order_id&status=failed");
            }
        } else {
            // Redirect to error page
            header("Location: ./thankyou.php?error=payment_update_failed");
        }
    } else {
        header("Location: ./cart.php");
    }
    exit();
}
?>