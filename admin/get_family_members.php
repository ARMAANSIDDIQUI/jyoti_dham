<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

$conn = DB::getInstance()->getConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name, gender, age FROM family_members WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'members' => $members]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}
?>
