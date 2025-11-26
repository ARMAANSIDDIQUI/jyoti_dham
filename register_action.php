<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';

// This will load the .env file and get the connection
$conn = DB::getInstance()->getConnection();

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Configure Cloudinary
if (isset($_ENV['CLOUDINARY_URL'])) {
    Configuration::instance($_ENV['CLOUDINARY_URL']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store form data in session in case of failure
    $_SESSION['form_data'] = $_POST;

    $conn->beginTransaction(); // Start transaction

    try {
        // 1. Handle Cloudinary upload (if profile_image is provided)
        $profile_image_url = null;
        $profile_image_public_id = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = (new UploadApi())->upload($_FILES['profile_image']['tmp_name']);
            $profile_image_url = $uploadResult['secure_url'];
            $profile_image_public_id = $uploadResult['public_id'];
        } else {
            // Use default image if no profile image is uploaded
            $profile_image_url = $_ENV['DEFAULT_PROFILE_IMAGE_URL'] ?? null;
            // For default images, public_id might not be relevant or could be a placeholder
            // For now, we'll leave it null or set a specific default public_id if needed.
            // Assuming the default image doesn't need to be deleted later.
            $profile_image_public_id = null;
        }

        // Sanitize and validate user input
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = $_POST['password']; // Will be hashed
        $confirm_password = $_POST['confirm_password'];
        $gender = htmlspecialchars(trim($_POST['gender']));
        $dob = htmlspecialchars(trim($_POST['dob']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));
        $vehicle_number = htmlspecialchars(trim($_POST['vehicle_number']));
        $additional_family_members = (int)$_POST['family_size'];
        $family_size = $additional_family_members + 1;

        // Basic validation
        if (empty($name)) {
            throw new Exception("Full Name is required.");
        }
        if (empty($email)) {
            throw new Exception("Email is required.");
        }
        if (empty($password)) {
            throw new Exception("Password is required.");
        }
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match. Please try again.");
        }
        if (empty($gender)) {
            throw new Exception("Gender is required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format. Please enter a valid email address.");
        }
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("Password must contain at least one uppercase letter.");
        }
        if (!preg_match('/[!@#$%^&*]/', $password)) {
            throw new Exception("Password must contain at least one special character.");
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            throw new Exception("Email already registered. Please use a different email or login.");
        }

        // 2. Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // 3. INSERT into the users table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, gender, dob, phone, address, family_size, vehicle_number, profile_image_url, profile_image_public_id)
                                VALUES (:name, :email, :password_hash, :gender, :dob, :phone, :address, :family_size, :vehicle_number, :profile_image_url, :profile_image_public_id)");

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':family_size', $family_size);
        $stmt->bindParam(':vehicle_number', $vehicle_number);
        $stmt->bindParam(':profile_image_url', $profile_image_url);
        $stmt->bindParam(':profile_image_public_id', $profile_image_public_id);

        $stmt->execute();

        // 4. Get the LAST_INSERT_ID() for the new user_id
        $user_id = $conn->lastInsertId();

        // 5. Loop through family member arrays and INSERT each one
        if ($additional_family_members > 0 && isset($_POST['family_name']) && is_array($_POST['family_name'])) {
            $family_names = $_POST['family_name'];
            $family_ages = $_POST['family_age'];
            $family_genders = $_POST['family_gender'];

            if (count($family_names) !== $additional_family_members || count($family_ages) !== $additional_family_members || count($family_genders) !== $additional_family_members) {
                throw new Exception("Mismatch in family member data provided. Please ensure all family member fields are filled correctly.");
            }

            $stmt = $conn->prepare("INSERT INTO family_members (user_id, name, age, gender) VALUES (:user_id, :name, :age, :gender)");

            for ($i = 0; $i < $additional_family_members; $i++) {
                $member_name = htmlspecialchars(trim($family_names[$i]));
                $member_age = (int)$family_ages[$i];
                $member_gender = htmlspecialchars(trim($family_genders[$i]));

                if (empty($member_name)) {
                    throw new Exception("Family member name cannot be empty.");
                }
                if (empty($member_gender)) {
                    throw new Exception("Family member gender cannot be empty.");
                }
                if ($member_age < 0) {
                    throw new Exception("Family member age cannot be negative.");
                }

                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':name', $member_name);
                $stmt->bindParam(':age', $member_age);
                $stmt->bindParam(':gender', $member_gender);
                $stmt->execute();
            }
        }

        // 6. COMMIT the transaction
        $conn->commit();
        unset($_SESSION['form_data']); // Clear form data on successful registration
        $_SESSION['message'] = "Registration successful! You can now login.";
        $_SESSION['message_type'] = "success";
        header("Location: login.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack(); // ROLLBACK on failure
        $_SESSION['message'] = "Registration failed: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php"); // Redirect if accessed directly
    exit();
}
?>