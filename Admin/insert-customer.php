<?php
include "../config/connection.php";

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        echo json_encode(array(
            'status' => 'error',
            'message' => 'All fields are required'
        ));
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO customers (fullname, email, phone, address, password) 
            VALUES ('$fullname', '$email', '$phone', '$address', '$hashedPassword')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ./index.php?msg=Customer added successfully");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>