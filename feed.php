<?php
require_once 'config/db_connect.php';

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="calendar_feed.ics"');

$db = DB::getInstance();
$conn = $db->getConnection();

// Fetch all future events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date, event_time");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:-//JyotiDham//NONSGML v1.0//EN\r\n";
echo "X-WR-CALNAME:Jyoti Dham Events\r\n";
echo "CALSCALE:GREGORIAN\r\n";

foreach ($events as $event) {
    $dtstart = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['event_time']));
    $dtend = date('Ymd\THis', strtotime($event['event_date'] . ' ' . $event['event_end_time']));

    echo "BEGIN:VEVENT\r\n";
    echo "UID:" . $event['id'] . "@" . $_SERVER['HTTP_HOST'] . "\r\n";
    echo "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
    echo "DTSTART:" . $dtstart . "\r\n";
    echo "DTEND:" . $dtend . "\r\n";
    echo "SUMMARY:" . $event['event_name'] . "\r\n";
    echo "DESCRIPTION:" . str_replace("\n", "\\n", $event['event_description']) . "\r\n";
    echo "LOCATION:" . $event['event_venue'] . "\r\n";
    echo "END:VEVENT\r\n";
}

echo "END:VCALENDAR\r\n";
?>