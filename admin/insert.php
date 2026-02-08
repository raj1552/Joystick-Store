<?php 
if(isset($_POST['submit'])){
    $fname= $_POST['fullname'];
    $email= $_POST['email'];
    $addr= $_POST['address'];
    $pwd= $_POST['password'];

        include "./connection.php";
        $sql ="INSERT INTO users (fullname, email, password, address)
                VALUES ('$fname', '$email', '$pwd','$addr')";
        $res = mysqli_query($conn, $sql);
        if($res) $_SESSION ['msg']= "Hey! user registered sucessfully.";
        else $_SESSION ['msg']= "Oops! User registration failed.";
}
header("location: ./index.php");