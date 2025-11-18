<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';
$conn = DB::getInstance()->getConnection();

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Protection: Redirect to login.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configure Cloudinary
Configuration::instance($_ENV['CLOUDINARY_URL']);

$user_id = $_SESSION['user_id'];

try {
    // Fetch current user data to get existing image public_id
    $stmt = $conn->prepare("SELECT profile_image_public_id FROM users WHERE id = :id");
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['profile_image_public_id'])) {
        $public_id = $user['profile_image_public_id'];

        // Delete image from Cloudinary
        (new UploadApi())->destroy($public_id);

        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_image_url = NULL, profile_image_public_id = NULL WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        $_SESSION['message'] = "Profile photo removed successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        throw new Exception("No profile photo to remove.");
    }

} catch (Exception $e) {
    error_log("Remove photo error: " . $e->getMessage());
    $_SESSION['message'] = "Failed to remove profile photo: " . $e->getMessage();
    $_SESSION['message_type'] = "error";
}

header("Location: profile.php");
exit();
?>