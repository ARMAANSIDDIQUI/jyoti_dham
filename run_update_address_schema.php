<?php
require_once __DIR__ . '/vendor/autoload.php';

$dbHost = $_SERVER['DB_HOST'];
$dbName = $_SERVER['DB_NAME'];
$dbUser = $_SERVER['DB_USER'];
$dbPass = $_SERVER['DB_PASS'];

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $dbUser, $dbPass, $options);
    $sql = file_get_contents(__DIR__ . '/update_address_schema.sql');
    $conn->exec($sql);
    echo "Database schema updated successfully.";
} catch (PDOException $e) {
    echo "Error updating database schema: " . $e->getMessage();
}
?>