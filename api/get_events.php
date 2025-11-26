<?php
require_once '../config/db_connect.php';
require_once '../vendor/autoload.php';

header('Content-Type: application/json');

$conn = DB::getInstance()->getConnection();

$stmt = $conn->query("SELECT id, event_name, event_date, event_time, event_end_time, event_description, event_venue FROM events ORDER BY event_date ASC");

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $start = $row['event_date'] . ' ' . $row['event_time'];
    $end = $row['event_date'] . ' ' . $row['event_end_time'];
    $events[] = [
        'id' => $row['id'],
        'title' => $row['event_name'],
        'start' => $start,
        'end' => $end,
        'description' => $row['event_description'],
        'venue' => $row['event_venue']
    ];
}

echo json_encode($events);
?>
