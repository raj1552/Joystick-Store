<?php
session_start();
include "./config/connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ./customer-login.php");
    exit();
}

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM cart WHERE id = $cart_id AND customer_id = $user_id";
    mysqli_query($conn, $sql);
}

header("Location: ./cart.php");
exit();
?>
