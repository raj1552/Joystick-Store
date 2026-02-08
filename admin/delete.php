<?php session_start();
include "../config/connection.php";

$id = $_GET['user_id'];
$sql = "DELETE FROM customers where id='$id';";
$res = mysqli_query($conn, $sql);


if (isset($_SESSION['msg'])) {
    echo "<div class='alert'>".$_SESSION['msg']."</div>";
    unset($_SESSION['msg']); 
}

header("location: ./index.php");