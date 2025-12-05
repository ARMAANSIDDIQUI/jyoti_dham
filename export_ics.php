<?php
require_once 'config/db_connect.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo "Invalid event ID.";
    exit;
}

$eventId = $_GET['id'];

$db = DB::getInstance();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
$stmt->bindParam(':id', $eventId, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    http_response_code(404);
    echo "Event not found.";
    exit;
}

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="event.ics"');

$dtstart = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['event_time']));
$dtend = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['event_end_time']));

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//JyotiDham//NONSGML v1.0//EN\r\n";
echo "BEGIN:VEVENT\r\n";
echo "UID:" . $event['id'] . "@jyotidham.ca\r\n";
echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
echo "DTSTART:" . $dtstart . "\r\n";
echo "DTEND:" . $dtend . "\r\n";
echo "SUMMARY:" . $event['event_name'] . "\r\n";
echo "DESCRIPTION:" . str_replace("\n", "\\n", $event['event_description']) . "\r\n";
echo "LOCATION:" . $event['event_venue'] . "\r\n";
echo "END:VEVENT\r\n";
echo "END:VCALENDAR\r\n";
?>