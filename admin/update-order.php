<?php
include "../config/connection.php";

if (isset($_POST['submit'])) {
    $order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    if ($order_id <= 0) {
        echo "Invalid order ID.";
        exit;
    }

    if ($quantity <= 0) {
        echo "Quantity must be greater than 0.";
        exit;
    }

    // Validate status
    $allowed_status = ['Pending', 'Completed'];
    if (!in_array($status, $allowed_status)) {
        $status = 'Pending';
    }

    // Get the product price from DB
    $price_query = "SELECT p.price 
                    FROM order_details od
                    JOIN products p ON od.product_id = p.id
                    WHERE od.order_id = '$order_id'
                    LIMIT 1";
    $price_result = mysqli_query($conn, $price_query);
    $price_row = mysqli_fetch_assoc($price_result);
    $price = isset($price_row['price']) ? (float)$price_row['price'] : 0;

    // Calculate total amount
    $total_amount = $price * $quantity;

    // Update order_details
    $update_od = "UPDATE order_details 
                  SET quantity = '$quantity' 
                  WHERE order_id = '$order_id'";
    $res_od = mysqli_query($conn, $update_od);

    // Update orders table with total amount + status
    $update_order = "UPDATE orders 
                     SET total_amount = '$total_amount', status = '$status' 
                     WHERE id = '$order_id'";
    $res_order = mysqli_query($conn, $update_order);

    // Update cart (assuming one product per order_id)
    $update_cart = "UPDATE cart 
                    SET quantity = '$quantity' 
                    WHERE order_id = '$order_id'";
    $res_cart = mysqli_query($conn, $update_cart);

    if ($res_od && $res_order && $res_cart) {
        header("Location: ./index.php?msg=Order updated successfully");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
