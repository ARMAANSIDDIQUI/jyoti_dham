<?php
require 'includes/db_connect.php';

header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename="satsang_feed.ics"');

$conn = DB::getInstance()->getConnection();

$stmt = $conn->prepare("SELECT * FROM satsang WHERE start_time > NOW() AND is_active = 1 ORDER BY start_time ASC");
$stmt->execute();
$satsangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ics = "BEGIN:VCALENDAR\r\n";
$ics .= "VERSION:2.0\r\n";
$ics .= "PRODID:-//Jyoti Dham//Satsang Calendar//EN\r\n";

foreach ($satsangs as $satsang) {
    $start = date('Ymd\THis', strtotime($satsang['start_time']));
    $end = date('Ymd\THis', strtotime($satsang['end_time']));

    $ics .= "BEGIN:VEVENT\r\n";
    $ics .= "UID:" . uniqid() . "@jyotidham.com\r\n";
    $ics .= "DTSTAMP:" . gmdate('Ymd\THis') . "\r\n";
    $ics .= "DTSTART:" . $start . "\r\n";
    $ics .= "DTEND:" . $end . "\r\n";
    $ics .= "SUMMARY:" . $satsang['title'] . "\r\n";
    $ics .= "DESCRIPTION:" . $satsang['description'] . "\r\n";
    $ics .= "LOCATION:" . $satsang['video_url'] . "\r\n";
    $ics .= "END:VEVENT\r\n";
}

$ics .= "END:VCALENDAR\r\n";

echo $ics;
?>
