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
            // self::$instance->checkAndCreateTables(); 
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    private function checkAndCreateTables() {
        $tables = [
            'users' => "
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password_hash VARCHAR(255) NOT NULL,
                    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
                    dob DATE NULL,
                    phone VARCHAR(20) NULL,
                    address TEXT NULL,
                    family_size INT NULL DEFAULT 0,
                    vehicle_number VARCHAR(50) NULL,
                    profile_image_url VARCHAR(255) NULL,
                    profile_image_public_id VARCHAR(255) NULL,
                    nationality VARCHAR(100) NULL,
                    role VARCHAR(50) NOT NULL DEFAULT 'user',

                    /* Google Calendar OAuth Tokens */
                    google_access_token TEXT NULL,
                    google_refresh_token TEXT NULL,
                    google_token_expires_at TIMESTAMP NULL,

                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            ",
            'family_members' => "
                CREATE TABLE IF NOT EXISTS family_members (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    gender ENUM('male', 'female', 'other') NOT NULL,
                    age INT NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                );
            ",
            'events' => "
                CREATE TABLE IF NOT EXISTS events (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    day VARCHAR(20) NOT NULL,
                    event_date DATE NOT NULL,
                    event_time TIME NOT NULL,
                    event_end_time TIME NOT NULL,
                    time_zone VARCHAR(10) NOT NULL,
                    event_name VARCHAR(255) NOT NULL,
                    event_description TEXT NOT NULL,
                    organizer VARCHAR(255) NOT NULL,
                    event_venue VARCHAR(255) NOT NULL,
                    latitude DECIMAL(10, 8) NOT NULL,
                    longitude DECIMAL(11, 8) NOT NULL,
                    is_featured TINYINT(1) DEFAULT 0,
                    google_event_id VARCHAR(255) NULL,
                    created_by INT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
                );
            ",
            'user_events' => "
                CREATE TABLE IF NOT EXISTS user_events (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    event_id INT NOT NULL,
                    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_user_event (user_id, event_id)
                );
            ",
            'satsang' => "
                CREATE TABLE IF NOT EXISTS satsang (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    satsang_date DATE NOT NULL,
                    start_time TIME NOT NULL,
                    end_time TIME NOT NULL,
                    time_zone VARCHAR(10) NOT NULL DEFAULT 'EST',
                    yt_link VARCHAR(500) NOT NULL,
                    is_active TINYINT(1) DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
            "
        ];

        foreach ($tables as $tableName => $createSQL) {
            try {
                $this->conn->exec($createSQL);
            } catch (PDOException $e) {
                error_log("Error creating table $tableName: " . $e->getMessage());
                // Continue with other tables
            }
        }

        // Alter satsang table to add new fields and modify existing ones
        $alterSQLs = [
            "ALTER TABLE satsang ADD COLUMN IF NOT EXISTS title VARCHAR(255) NOT NULL AFTER id",
            "ALTER TABLE satsang ADD COLUMN IF NOT EXISTS description TEXT AFTER title",
            "ALTER TABLE satsang MODIFY COLUMN start_time DATETIME NOT NULL",
            "ALTER TABLE satsang MODIFY COLUMN end_time DATETIME NOT NULL",
            "ALTER TABLE satsang RENAME COLUMN yt_link TO video_url"
        ];

        foreach ($alterSQLs as $alterSQL) {
            try {
                $this->conn->exec($alterSQL);
            } catch (PDOException $e) {
                error_log("Error altering satsang table: " . $e->getMessage());
            }
        }
    }
}

// Include the database initialization script, but only when a connection is first established.
// This is now handled within the singleton logic, so we can remove it from here.
// require_once __DIR__ . '/db_init.php';

?>