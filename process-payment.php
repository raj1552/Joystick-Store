<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "./config/connection.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: ./customer-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Always take POST first, fallback to session
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : (int)($_SESSION['pending_order_id'] ?? 0);
$total_amount = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : (float)($_SESSION['order_total'] ?? 0);
$payment_method = $_POST['paymentMethod'] ?? '';

// Debug logging
error_log("=== PAYMENT PROCESSING START ===");
error_log("Order ID: " . $order_id);
error_log("Total Amount: " . $total_amount);
error_log("Payment Method: " . $payment_method);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate order info
    if ($order_id <= 0 || $total_amount <= 0) {
        error_log("ERROR: Invalid order info - order_id: $order_id, total_amount: $total_amount");
        echo "<script>alert('Invalid order info.'); window.location.href='./cart.php';</script>";
        exit();
    }
    
    // Allowed payment methods
    $allowed_methods = ['esewa', 'khalti', 'cod'];
    if (!in_array($payment_method, $allowed_methods)) {
        error_log("ERROR: Invalid payment method: $payment_method");
        echo "<script>alert('Invalid payment method!'); window.history.back();</script>";
        exit();
    }
    
    // Set payment status based on method
    if ($payment_method === 'cod') {
        $payment_status = 'completed'; // COD is completed immediately
        $order_status = 'confirmed';
    } else {
        $payment_status = 'pending'; // Online payments start as pending
        $order_status = 'pending'; // Keep order pending until payment confirms
    }
    
    error_log("Payment status will be: $payment_status");
    error_log("Order status will be: $order_status");
    
    // Check if payment already exists for this order and method
    $check_sql = "SELECT id FROM payments WHERE order_id = ? AND payment_method = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        error_log("ERROR: Failed to prepare check statement: " . $conn->error);
        echo "<script>alert('Database error occurred.'); window.location.href='./cart.php';</script>";
        exit();
    }
    
    $check_stmt->bind_param("is", $order_id, $payment_method);
    $check_stmt->execute();
    $existing_payment = $check_stmt->get_result();
    
    if ($existing_payment->num_rows > 0) {
        error_log("WARNING: Payment already exists for order_id: $order_id, method: $payment_method");
        // Continue with existing payment instead of creating new one
        if ($payment_method !== 'cod') {
            header("Location: ./paymentapi/$payment_method/index.php?total=$total_amount&order_id=$order_id");
            exit();
        }
    }
    
    // Insert payment record without transaction (simpler approach)
    error_log("Attempting to insert payment record...");
    
    $stmt = $conn->prepare("
        INSERT INTO payments 
        (order_id, amount, payment_method, payment_status, payment_date, created_at, updated_at)
        VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())
    ");
    
    if (!$stmt) {
        error_log("ERROR: Failed to prepare insert statement: " . $conn->error);
        echo "<script>alert('Database error occurred.'); window.location.href='./cart.php';</script>";
        exit();
    }
    
    $stmt->bind_param("idss", $order_id, $total_amount, $payment_method, $payment_status);
    
    if (!$stmt->execute()) {
        error_log("ERROR: Payment insert failed: " . $stmt->error);
        echo "<script>alert('Payment processing failed. Error: " . $stmt->error . "'); window.location.href='./cart.php';</script>";
        exit();
    }
    
    $payment_id = $conn->insert_id;
    $_SESSION['payment_id'] = $payment_id;
    
    error_log("SUCCESS: Payment inserted with ID: $payment_id");
    
    // Update order status
    $stmt2 = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?");
    if (!$stmt2) {
        error_log("ERROR: Failed to prepare order update statement: " . $conn->error);
    } else {
        $stmt2->bind_param("si", $order_status, $order_id);
        if (!$stmt2->execute()) {
            error_log("ERROR: Order update failed: " . $stmt2->error);
        } else {
            error_log("SUCCESS: Order status updated to: $order_status");
        }
    }
    
    // Handle different payment methods
    if ($payment_method === 'cod') {
        // Decouple: notify SQS for async processing (e.g. confirmation email)
        if (file_exists(__DIR__ . '/config/sqs_helper.php')) {
            require_once __DIR__ . '/config/sqs_helper.php';
            sqs_send_message('payment_completed', [
                'order_id' => (int) $order_id,
                'amount'   => (float) $total_amount,
                'method'   => 'cod',
                'status'   => 'completed',
            ]);
        }
        unset($_SESSION['pending_order_id'], $_SESSION['order_total']);
        error_log("COD: Redirecting to success page");
        header("Location: ./thankyou.php?status=success&order_id=$order_id&method=cod");
        exit();
    }
    
    if ($payment_method === 'esewa') {
        // Store order info in session for callback verification
        $_SESSION['pending_payment_order_id'] = $order_id;
        $_SESSION['pending_payment_amount'] = $total_amount;
        error_log("eSewa: Redirecting to payment gateway");
        
        // Add a small delay to ensure database write is complete
        usleep(500000); // 0.5 second delay
        
        header("Location: ./paymentapi/esewa/index.php?total=$total_amount&order_id=$order_id");
        exit();
    }
    
    if ($payment_method === 'khalti') {
        // Store order info in session for callback verification
        $_SESSION['pending_payment_order_id'] = $order_id;
        $_SESSION['pending_payment_amount'] = $total_amount;
        error_log("Khalti: Redirecting to payment gateway");
        
        // Add a small delay to ensure database write is complete
        usleep(500000); // 0.5 second delay
        
        header("Location: ./paymentapi/khalti/index.php?total=$total_amount&order_id=$order_id");
        exit();
    }
    
} else {
    // Invalid access
    error_log("ERROR: Invalid request method or missing POST data");
    header("Location: ./cart.php");
    exit();
}
?>