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
    $member_id = $_POST['member_id'];
    $name = htmlspecialchars(trim($_POST['name']));
    $age = filter_var(trim($_POST['age']), FILTER_VALIDATE_INT);
    $gender = htmlspecialchars(trim($_POST['gender']));
    $user_id = $_SESSION['user_id'];

    try {
        // Validation
        if (empty($name) || $age === false || $age < 0 || empty($gender)) {
            throw new Exception("Invalid data provided.");
        }

        // Check if the family member belongs to the logged-in user
        $stmt = $conn->prepare("SELECT id FROM family_members WHERE id = :member_id AND user_id = :user_id");
        $stmt->bindParam(':member_id', $member_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        if ($stmt->fetch() === false) {
            throw new Exception("You are not authorized to edit this family member.");
        }

        // Update the family member
        $stmt = $conn->prepare("UPDATE family_members SET name = :name, age = :age, gender = :gender WHERE id = :id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':id', $member_id);
        $stmt->execute();

        $_SESSION['message'] = "Family member updated successfully!";
        $_SESSION['message_type'] = "success";

    } catch (Exception $e) {
        $_SESSION['message'] = "Failed to update family member: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }

    header("Location: profile.php#family");
    exit();

} else {
    // Redirect if not a POST request
    header("Location: profile.php");
    exit();
}
?>