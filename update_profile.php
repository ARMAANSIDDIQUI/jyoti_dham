<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';
$conn = DB::getInstance()->getConnection();

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

// Protection: Redirect to login.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Configure Cloudinary
Configuration::instance($_ENV['CLOUDINARY_URL']);

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store form data in session in case of failure
    $_SESSION['profile_form_data'] = $_POST;

    $conn->beginTransaction(); // Start transaction

    try {
        // Fetch current user data to get existing image public_id and url
        $stmt = $conn->prepare("SELECT profile_image_url, profile_image_public_id FROM users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        $old_profile_image_url = $currentUser['profile_image_url'] ?? null;
        $old_profile_image_public_id = $currentUser['profile_image_public_id'] ?? null;

        // Handle Profile Picture Update
        $profile_image_url = $old_profile_image_url; // Start with current URL
        $profile_image_public_id = $old_profile_image_public_id; // Start with current public_id

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image from Cloudinary if it exists and is not the default image
            if ($old_profile_image_public_id && $old_profile_image_url !== ($_ENV['DEFAULT_PROFILE_IMAGE_URL'] ?? null)) {
                (new UploadApi())->destroy($old_profile_image_public_id);
            }
            // Upload new image
            $uploadResult = (new UploadApi())->upload($_FILES['profile_image']['tmp_name']);
            $profile_image_url = $uploadResult['secure_url'];
            $profile_image_public_id = $uploadResult['public_id'];
        } else {
            // If no new image uploaded, and current image is empty, use default
            if (empty($profile_image_url)) {
                $profile_image_url = $_ENV['DEFAULT_PROFILE_IMAGE_URL'] ?? null;
                $profile_image_public_id = null; // Default images don't have a public_id for deletion
            }
        }

        // Handle User Details Update
        $name = htmlspecialchars(trim($_POST['name']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $dob = htmlspecialchars(trim($_POST['dob']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));

        $vehicle_number = htmlspecialchars(trim($_POST['vehicle_number']));

        // Basic validation
        if (empty($name)) {
            throw new Exception("Full Name is required.");
        }
        if (empty($gender)) {
            throw new Exception("Gender is required.");
        }
        // Add more specific validation for phone if needed
        if (!empty($phone) && !preg_match("/^\+?[0-9\s\-()]{7,20}$/", $phone)) {
            throw new Exception("Invalid phone number format.");
        }


        $stmt = $conn->prepare("UPDATE users SET
                                name = :name,
                                gender = :gender,
                                dob = :dob,
                                phone = :phone,
                                address = :address,
                                vehicle_number = :vehicle_number,
                                profile_image_url = :profile_image_url,
                                profile_image_public_id = :profile_image_public_id
                                WHERE id = :id");

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':vehicle_number', $vehicle_number);
        $stmt->bindParam(':profile_image_url', $profile_image_url);
        $stmt->bindParam(':profile_image_public_id', $profile_image_public_id);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        $conn->commit(); // Commit transaction
        unset($_SESSION['profile_form_data']); // Clear form data on successful update
        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: profile.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack(); // ROLLBACK on failure
        error_log("Profile update error: " . $e->getMessage());
        $_SESSION['message'] = "Profile update failed: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: profile.php");
        exit();
    }
} else {
    header("Location: profile.php"); // Redirect if accessed directly
    exit();
}
?>