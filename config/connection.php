<?php
$host = "127.0.0.1"; // localhost
$dbuser = "root"; // default of mysql
$dbpwd = ""; // default empty on xampp mysql
$dbname = "levelup"; // create DB on mysql

//mysqli_connect() method, mysql object instance
//$conn = mysqli_connect();

$conn = new mysqli($host, $dbuser, $dbpwd, $dbname);

if(!$conn) die("Oops! Database connection failed.");

// echo "<pre>";
// print_r($conn); // developer checking data
// echo "</pre>";