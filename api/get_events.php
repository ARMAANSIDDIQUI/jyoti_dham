<?php
require_once '../config/db_connect.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json');

$conn = DB::getInstance()->getConnection();

$stmt = $conn->query("SELECT title, start_time, end_time FROM satsang WHERE is_active = 1 ORDER BY start_time ASC");

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $events[] = [
        'title' => $row['title'],
        'start' => $row['start_time'], // MySQL DATETIME is already in the right format
        'end'   => $row['end_time']
    ];
}

echo json_encode($events);
?>
