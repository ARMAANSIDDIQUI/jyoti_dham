<?php
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

if (!isset($_GET['id'])) {
    die('Event ID required');
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM events WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $id, PDO::PARAM_INT);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die('Event not found');
}

$title = $event['event_name'];
$description = $event['event_description'];
$start = new DateTime($event['event_date'] . ' ' . $event['event_time']);
$end = new DateTime($event['event_date'] . ' ' . $event['event_end_time']);
$venue = $event['event_venue'];

$ics = "BEGIN:VCALENDAR\r\n";
$ics .= "VERSION:2.0\r\n";
$ics .= "PRODID:-//Jyoti Dham//Event Calendar//EN\r\n";
$ics .= "BEGIN:VEVENT\r\n";
$ics .= "UID:" . uniqid() . "@jyotidham.com\r\n";
$ics .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
$ics .= "DTSTART:" . $start->format('Ymd\THis\Z') . "\r\n";
$ics .= "DTEND:" . $end->format('Ymd\THis\Z') . "\r\n";
$ics .= "SUMMARY:" . $title . "\r\n";
$ics .= "DESCRIPTION:" . $description . "\r\n";
$ics .= "LOCATION:" . $venue . "\r\n";
$ics .= "END:VEVENT\r\n";
$ics .= "END:VCALENDAR\r\n";

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="event.ics"');
echo $ics;
?>
