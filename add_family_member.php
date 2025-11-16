<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/db_connect.php';
$conn = DB::getInstance()->getConnection();

// Protection: Redirect to login.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store form data in session in case of failure
    $_SESSION['add_family_member_form_data'] = $_POST;

    $conn->beginTransaction(); // Start transaction

    try {
        $name = htmlspecialchars(trim($_POST['name']));
        $age = (int)$_POST['age'];
        $gender = htmlspecialchars(trim($_POST['gender']));

        // Basic validation
        if (empty($name)) {
            throw new Exception("Family member name is required.");
        }
        if (empty($gender)) {
            throw new Exception("Family member gender is required.");
        }
        if ($age < 0) {
            throw new Exception("Family member age cannot be negative.");
        }

        // Insert new family member
        $stmt = $conn->prepare("INSERT INTO family_members (user_id, name, age, gender) VALUES (:user_id, :name, :age, :gender)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':gender', $gender);
        $stmt->execute();

        // Update family_size count in the users table (including the user)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM family_members WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $family_size = $count + 1; // Include the user in the family size
        $update = $conn->prepare("UPDATE users SET family_size = ? WHERE id = ?");
        $update->execute([$family_size, $user_id]);

        $conn->commit(); // Commit transaction
        unset($_SESSION['add_family_member_form_data']); // Clear form data on successful addition
        $_SESSION['message'] = "Family member added successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: profile.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack(); // ROLLBACK on failure
        error_log("Add family member error: " . $e->getMessage());
        $_SESSION['message'] = "Failed to add family member: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: profile.php");
        exit();
    }
} else {
    header("Location: profile.php"); // Redirect if accessed directly
    exit();
}
?>