<?php
// Admin check
require_once __DIR__ . '/auth_check.php';

// Include database connection
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$conn = DB::getInstance()->getConnection();

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM satsang WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    header("Location: manage_satsangs.php");
    exit();
}

// Handle update action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $video_url = $_POST['video_url'];

    $sql = "UPDATE satsang SET title = ?, description = ?, start_time = ?, end_time = ?, video_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $start_datetime, $end_datetime, $video_url, $id]);
    header("Location: manage_satsangs.php");
    exit();
}
?>
