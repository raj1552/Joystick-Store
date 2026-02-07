<?php
// Use environment variables for AWS RDS / deployment; fallback to local defaults
$host   = getenv('DB_HOST')   ?: '127.0.0.1';
$dbuser = getenv('DB_USER')   ?: 'root';
$dbpwd  = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME')   ?: 'levelup';

$conn = new mysqli($host, $dbuser, $dbpwd, $dbname);

if ($conn->connect_error) {
    die('Oops! Database connection failed: ' . $conn->connect_error);
}

// echo "<pre>";
// print_r($conn); // developer checking data
// echo "</pre>";