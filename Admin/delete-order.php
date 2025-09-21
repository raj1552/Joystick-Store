<?php
include "../config/connection.php";

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Start transaction to ensure all deletions happen together
    $conn->begin_transaction();

    try {
        // 1. Delete related cart entries
        $stmt_cart = $conn->prepare("DELETE FROM cart WHERE order_id = ?");
        $stmt_cart->bind_param("i", $order_id);
        $stmt_cart->execute();

        // 2. Delete related order_details
        $stmt_details = $conn->prepare("DELETE FROM order_details WHERE order_id = ?");
        $stmt_details->bind_param("i", $order_id);
        $stmt_details->execute();

        // 3. Delete related payments
        $stmt_payment = $conn->prepare("DELETE FROM payments WHERE order_id = ?");
        $stmt_payment->bind_param("i", $order_id);
        $stmt_payment->execute();

        // 4. Delete the order itself
        $stmt_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt_order->bind_param("i", $order_id);
        $stmt_order->execute();

        // Commit transaction
        $conn->commit();

        header("Location: index.php?msg=Order+and+related+entries+deleted+successfully");
        exit();

    } catch (Exception $e) {
        // Rollback if any deletion fails
        $conn->rollback();
        header("Location: index.php?msg=Error+deleting+order");
        exit();
    }
} else {
    header("Location: index.php?msg=Invalid+request");
    exit();
}
?>
