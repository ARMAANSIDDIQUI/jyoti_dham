<?php
require 'config/db_connect.php';
$conn = DB::getInstance()->getConnection();

echo "Users table structure:\n";
$stmt = $conn->query('DESCRIBE users');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\nEvents table structure:\n";
$stmt = $conn->query('DESCRIBE events');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}
?>
