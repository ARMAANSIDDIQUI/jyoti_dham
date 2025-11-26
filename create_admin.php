<?php
require_once __DIR__ . '/config/db_connect.php';

// This script is intended to be run from the command line.
// Example usage: php create_admin.php "Admin Name" "admin@example.com" "adminpassword"

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from the command line.");
}

if ($argc < 4) {
    die("Usage: php create_admin.php \"Admin Name\" \"admin@example.com\" \"adminpassword\"\n");
}

$name = $argv[1];
$email = $argv[2];
$password = $argv[3];
$role = 'admin'; // Set role to admin

// Basic validation
if (empty($name) || empty($email) || empty($password)) {
    die("Error: All fields are required.\n");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Error: Invalid email format.\n");
}
if (strlen($password) < 6) {
    die("Error: Password must be at least 6 characters long.\n");
}

// Hash the password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        die("Error: Email already registered.\n");
    }

    // Insert new admin user into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        echo "Admin user '{$name}' with email '{$email}' created successfully!\n";
    } else {
        echo "Error: Failed to create admin user.\n";
    }
} catch (PDOException $e) {
    error_log("Admin creation error: " . $e->getMessage());
    echo "An error occurred: " . $e->getMessage() . "\n";
}
?>
