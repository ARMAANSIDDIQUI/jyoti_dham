<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

class DB {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Load environment variables from .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];

        $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conn = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            error_log("Database connection error: " . $e->getMessage());
            // Display a user-friendly message
            die("Database connection failed. Please try again later.");
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Include the database initialization script, but only when a connection is first established.
// This is now handled within the singleton logic, so we can remove it from here.
// require_once __DIR__ . '/db_init.php';

?>