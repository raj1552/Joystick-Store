<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "./config/connection.php";

$status = $_GET['status'] ?? '';
$order_id = (int)($_GET['order_id'] ?? 0);
$method = $_GET['method'] ?? '';

// Handle successful online payment completion
if ($status === 'success' && in_array($method, ['esewa', 'khalti'])) {
    // Update payment status to completed (works for initiated or processing payments)
    $sql = "UPDATE payments 
            SET payment_status = 'completed', updated_at = NOW() 
            WHERE order_id = ? AND payment_method = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $order_id, $method);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        // Update order status to confirmed
        $orderSql = "UPDATE orders SET status = 'confirmed', updated_at = NOW() WHERE id = ?";
        $stmt2 = $conn->prepare($orderSql);
        $stmt2->bind_param("i", $order_id);
        $stmt2->execute();

        // Clear session data
        unset($_SESSION['pending_order_id'], $_SESSION['order_total'], $_SESSION['payment_id']);
    }
}

// Handle failed payment
if ($status === 'failed' && $order_id > 0) {
    $sql = "UPDATE payments SET payment_status = 'failed', updated_at = NOW() WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    $orderSql = "UPDATE orders SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
    $stmt2 = $conn->prepare($orderSql);
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();

    unset($_SESSION['pending_order_id'], $_SESSION['order_total'], $_SESSION['payment_id']);
}

include "./header.php";
?>

<section class="thankyou-page">
    <div class="container">
        <div class="thankyou-content">
            <?php if ($status === 'success'): ?>
                <div class="success-icon">✅</div>
                <h1>Thank You!</h1>
                <p class="main-message">
                    <?php 
                    if ($method === 'cod') {
                        echo "Your order has been successfully placed and will be delivered with Cash on Delivery.";
                    } else {
                        echo "Your payment via " . ucfirst($method) . " has been processed successfully and your order is confirmed.";
                    }
                    ?>
                </p>

                <?php if ($order_id > 0): ?>
                    <div class="order-info">
                        <p>Order ID: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
                        <p>Payment Method: <strong><?= ($method === 'cod') ? 'Cash on Delivery' : ucfirst(htmlspecialchars($method)) ?></strong></p>
                        <?php if ($method === 'cod'): ?>
                            <p class="cod-note">💡 Please keep the exact amount ready for delivery.</p>
                        <?php else: ?>
                            <p class="payment-note">✅ Payment completed successfully.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <p class="sub-message">We'll send you a confirmation email with your order details and tracking information.</p>

            <?php elseif ($status === 'failed'): ?>
                <div class="error-icon">❌</div>
                <h1>Payment Failed</h1>
                <p class="main-message">Unfortunately, your payment could not be processed.</p>
                
                <?php if ($order_id > 0): ?>
                    <div class="order-info">
                        <p>Order ID: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
                        <p class="error-note">Please try again or contact our support team.</p>
                    </div>
                <?php endif; ?>
                
                <p class="sub-message">You can retry the payment or choose a different payment method.</p>
                
            <?php else: ?>
                <div class="success-icon">✅</div>
                <h1>Thank You!</h1>
                <p class="main-message">Your order has been successfully placed.</p>
                
                <?php if ($order_id > 0): ?>
                    <div class="order-info">
                        <p>Order ID: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
                    </div>
                <?php endif; ?>
                
                <p class="sub-message">We'll send you a confirmation email with your order details.</p>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="./myorders.php" class="btn btn-primary">View My Orders</a>
                <a href="./shop.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="./index.php" class="btn btn-outline">Back to Home</a>
                <?php if ($status === 'failed'): ?>
                    <a href="./cart.php" class="btn btn-warning">Retry Payment</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
