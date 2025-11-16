<?php
require_once 'vendor/autoload.php';
require_once 'config/db_connect.php';

$conn = DB::getInstance()->getConnection();
$stmt = $conn->query('SELECT id FROM users');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $user_id = $user['id'];
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM family_members WHERE user_id = ?');
    $stmt->execute([$user_id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $family_size = $count + 1; // Include the user in the family size
    $update = $conn->prepare('UPDATE users SET family_size = ? WHERE id = ?');
    $update->execute([$family_size, $user_id]);
    echo "Updated user $user_id to family_size $family_size\n";
}

echo 'Family sizes updated for all users.\n';
?>
