<?php
include "../config/connection.php";

$id = $_GET['product_id']; 

$cat_query = "SELECT category_id FROM products WHERE id = '$id' LIMIT 1";
$cat_res = mysqli_query($conn, $cat_query);

if ($cat_res && mysqli_num_rows($cat_res) > 0) {
    $product = mysqli_fetch_assoc($cat_res);
    $category_id = $product['category_id'];

    $del_details = "DELETE FROM category_details WHERE category_id = $category_id";
    mysqli_query($conn, $del_details);

    $del_product = "DELETE FROM products WHERE id = $id";
    mysqli_query($conn, $del_product);

    $del_category = "DELETE FROM categories WHERE id = $category_id";
    mysqli_query($conn, $del_category);

    header("Location: ./index.php?msg=Product deleted successfully");
    exit;
} else {
    echo "No product ID provided.";
}
?>