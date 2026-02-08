<?php
// Load .env if present (for local dev or single config file)
$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\"'");
        if ($name !== '') {
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

// Use environment variables for AWS RDS / deployment; fallback to local defaults
$host   = getenv('DB_HOST')   ?: '127.0.0.1';
$port   = (int) (getenv('DB_PORT') ?: 3306);
$dbuser = getenv('DB_USER')   ?: 'root';
$dbpwd  = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME')   ?: 'levelup';

// Use port so PHP connects via TCP (127.0.0.1:3306) instead of Unix socket (localhost)
$conn = new mysqli($host, $dbuser, $dbpwd, $dbname, $port);

if ($conn->connect_error) {
    die('Oops! Database connection failed: ' . $conn->connect_error);
}

// echo "<pre>";
// print_r($conn); // developer checking dataz
// echo "</pre>";