<?php
/**
 * Run from project root: php check-db.php
 * Verifies .env is loaded and DB connection works (then exits; does not start a server).
 */
$envFile = __DIR__ . '/.env';
if (!is_file($envFile)) {
    echo ".env not found.\n";
    exit(1);
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
    list($name, $value) = explode('=', $line, 2);
    $name = trim($name);
    $value = trim($value, " \t\"'");
    if ($name !== '') {
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
$host   = getenv('DB_HOST') ?: '127.0.0.1';
$port   = (int) (getenv('DB_PORT') ?: 3306);
$dbuser = getenv('DB_USER') ?: 'root';
$dbname = getenv('DB_NAME') ?: 'levelup';
$dbpwd  = getenv('DB_PASSWORD') ?: '';

echo "Connecting: host=$host port=$port user=$dbuser db=$dbname\n";

$conn = @new mysqli($host, $dbuser, $dbpwd, $dbname, $port);
if ($conn->connect_error) {
    echo "FAILED: " . $conn->connect_error . "\n";
    echo "\nCreate DB user for BOTH localhost and 127.0.0.1 (copy and run):\n";
    echo "sudo mariadb -e \"CREATE USER IF NOT EXISTS 'levelup'@'localhost' IDENTIFIED BY 'localdev'; CREATE USER IF NOT EXISTS 'levelup'@'127.0.0.1' IDENTIFIED BY 'localdev'; GRANT ALL ON levelup.* TO 'levelup'@'localhost'; GRANT ALL ON levelup.* TO 'levelup'@'127.0.0.1'; FLUSH PRIVILEGES;\"\n";
    exit(1);
}
echo "OK: Connected.\n";
$conn->close();
exit(0);
