<?php 
if(isset($_POST['submit'])){
    $id = $_POST['user_id'];
    $fname= $_POST['fullname'];
    $email= $_POST['email'];
    $phone= $_POST['phone'];
    $addr= $_POST['address'];

$sql = "UPDATE users SET fullname='$fname', email='$email', phone='$phone', address='$addr' WHERE id=$id";
include "../config/connection.php";
$res = mysqli_query($conn, $sql);

if($res) $_SESSION['msg'] = "Admin Updated Sucessfully";
else $_SESSION['msg'] = "Admin Update Failed";

}

header("location: ./index.php");
