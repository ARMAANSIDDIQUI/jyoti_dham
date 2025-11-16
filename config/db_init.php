<?php

// This script assumes $conn is available from db_connect.php

// SQL for creating the users table
$createUsersTableSQL = "
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
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// SQL for creating the family_members table
$createFamilyMembersTableSQL = "
CREATE TABLE IF NOT EXISTS family_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    age INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
";

try {
    // Execute SQL to create users table if it doesn't exist
    $conn->exec($createUsersTableSQL);
    // Execute SQL to create family_members table if it doesn't exist
    $conn->exec($createFamilyMembersTableSQL);
} catch (PDOException $e) {
    error_log("Database initialization error: " . $e->getMessage());
    die("Database initialization failed. Please try again later.");
}

?>