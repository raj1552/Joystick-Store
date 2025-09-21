<?php
include "../config/connection.php";

if (isset($_POST['submit'])) {
    $id = $_POST['product_id'];
    $pname = $_POST['productname'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Validate inputs
    if (empty($pname) || empty($price) || empty($stock)) {
        echo "All fields are required!";
        exit;
    }

    // Update query
    $sql = "UPDATE products 
            SET name = ?, price = ?, stock_quantity = ? 
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sdii", $pname, $price, $stock, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?message=Product updated successfully");
        exit;
    } else {
        echo "Error updating product: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Invalid request!";
}
?>
