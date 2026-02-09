<?php
// config/connection.php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

try {
    // Load .env
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $awsRegion = $_ENV['AWS_REGION'] ?? '';
    $secretName = $_ENV['AWS_SECRET_NAME'] ?? '';

    if (!$awsRegion || !$secretName) {
        throw new Exception("AWS_REGION or AWS_SECRET_NAME is not set in .env");
    }

    // AWS Secrets Manager client
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region'  => $awsRegion
    ]);

    $result = $client->getSecretValue(['SecretId' => $secretName]);
    $secret = json_decode($result['SecretString'], true);

    $dbHost = $secret['host'];
    $dbName = $secret['dbname'] ?? $secret['dbInstanceIdentifier'] ?? 'levelup';
    $dbUser = $secret['username'];
    $dbPass = $secret['password'];
    $dbPort = $secret['port'] ?? 3306;

    // mysqli connection
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Set charset
    $conn->set_charset("utf8mb4");

    // Now $conn is available for mysqli_query in your code

} catch (\Dotenv\Exception\InvalidPathException $e) {
    die("Error: .env file not found. " . $e->getMessage());
} catch (AwsException $e) {
    die("Error fetching secret: " . $e->getMessage());
} catch (Exception $e) {
    die("Unexpected error: " . $e->getMessage());
}
