<?php
require_once 'vendor/autoload.php';
require_once 'config/db_connect.php';

$conn = DB::getInstance()->getConnection();

try {
    $stmt = $conn->prepare('SELECT COUNT(*) as count FROM family_members WHERE user_id = ?');
    $stmt->execute([1]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo 'Count: ' . $count . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
