<?php
// Khalti Success Callback - Add this to your paymentapi/khalti/success.php
session_start();

// Get order_id from URL parameter
$order_id = $_GET['order_id'] ?? '';

// For Khalti success callback  
if (isset($_GET['pidx']) && isset($_GET['status']) && $_GET['status'] === 'Completed') {
    // Khalti payment successful
    $transaction_id = $_GET['pidx'];
    
    // Log the callback for debugging
    error_log("Khalti Success Callback - Order ID: $order_id, Transaction ID: $transaction_id");
    
    // Redirect to thank you page with success status
    header("Location: ../../thankyou.php?status=success&order_id=$order_id&method=khalti&txn_id=$transaction_id");
    exit();
}

// For payment failure
header("Location: ../../thankyou.php?status=failed&order_id=$order_id&method=khalti");
exit();
?>