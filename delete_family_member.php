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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $conn->beginTransaction(); // Start transaction

    try {
        $member_id = (int)$_POST['member_id'];

        // Verify that the family member belongs to the logged-in user
        $stmt = $conn->prepare("SELECT user_id FROM family_members WHERE id = :member_id");
        $stmt->bindParam(':member_id', $member_id);
        $stmt->execute();
        $member = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$member || $member['user_id'] != $user_id) {
            throw new Exception("Unauthorized attempt to delete family member.");
        }

        // Delete the family member
        $stmt = $conn->prepare("DELETE FROM family_members WHERE id = :member_id AND user_id = :user_id");
        $stmt->bindParam(':member_id', $member_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        // Update family_size count in the users table (including the user)
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM family_members WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        $family_size = $count + 1; // Include the user in the family size
        $update = $conn->prepare("UPDATE users SET family_size = ? WHERE id = ?");
        $update->execute([$family_size, $user_id]);

        $conn->commit(); // Commit transaction
        $_SESSION['message'] = "Family member deleted successfully!";
        $_SESSION['message_type'] = "success";
        header("Location: profile.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack(); // ROLLBACK on failure
        error_log("Delete family member error: " . $e->getMessage());
        $_SESSION['message'] = "Failed to delete family member: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: profile.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request to delete family member.";
    $_SESSION['message_type'] = "error";
    header("Location: profile.php"); // Redirect if accessed directly or without member_id
    exit();
}
?>