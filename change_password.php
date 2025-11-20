<?php
session_start();
require_once __DIR__ . '/config/db_connect.php';
$conn = DB::getInstance()->getConnection();

// Protection: Redirect to login.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // Fetch current password hash
        $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User not found.");
        }

        // Verify current password
        if (!password_verify($current_password, $user['password_hash'])) {
            throw new Exception("Incorrect current password.");
        }

        // Check if new passwords match
        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords do not match.");
        }

        // Check new password length and complexity
        if (strlen($new_password) < 6) {
            throw new Exception("New password must be at least 6 characters long.");
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            throw new Exception("Password must contain at least one uppercase letter.");
        }
        if (!preg_match('/[!@#$%^&*]/', $new_password)) {
            throw new Exception("Password must contain at least one special character.");
        }

        // Hash new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $stmt = $conn->prepare("UPDATE users SET password_hash = :password WHERE id = :id");
        $stmt->bindParam(':password', $new_password_hash);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        $_SESSION['message'] = "Password updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['message'] = "Failed to update password: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: profile.php#password");
    exit();

} else {
    // Redirect if not a POST request
    header("Location: profile.php");
    exit();
}
?>