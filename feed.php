<?php
// 1. Start output buffering immediately to catch any stray whitespace
ob_start();

require_once 'config/db_connect.php';

$db = DB::getInstance();
$conn = $db->getConnection();

// Fetch all future events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date, event_time");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. HELPER FUNCTION: Escape text for .ics (Strict Requirement)
// Commas, semicolons, backslashes, and newlines must be escaped
function escapeIcalText($text) {
    $text = str_replace("\\", "\\\\", $text); // Escape backslashes first
    $text = str_replace(",", "\,", $text);    // Escape commas
    $text = str_replace(";", "\;", $text);    // Escape semicolons
    $text = str_replace("\n", "\\n", $text);  // Escape newlines
    $text = str_replace("\r", "", $text);     // Remove carriage returns
    return $text;
}

// 3. Clear the buffer to remove any whitespace included from db_connect.php
ob_end_clean();

// 4. Send Headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="calendar_feed.ics"');

// 5. Define Line Ending
$eol = "\r\n";

// 6. Generate Content
echo "BEGIN:VCALENDAR" . $eol;
echo "VERSION:2.0" . $eol;
echo "PRODID:-//JyotiDham//NONSGML v1.0//EN" . $eol;
echo "X-WR-CALNAME:Jyoti Dham Events" . $eol;
echo "CALSCALE:GREGORIAN" . $eol;
echo "METHOD:PUBLISH" . $eol; // Helpful for subscriptions

foreach ($events as $event) {
    // Combine date and time
    $startTimestamp = strtotime($event['event_date'] . ' ' . $event['event_time']);
    $endTimestamp = strtotime($event['event_date'] . ' ' . $event['event_end_time']);

    // 7. USE UTC TIME (Z suffix)
    // Calendars prefer UTC to handle timezones correctly on the user's device
    $dtstart = gmdate('Ymd\THis\Z', $startTimestamp);
    $dtend   = gmdate('Ymd\THis\Z', $endTimestamp);
    $dtstamp = gmdate('Ymd\THis\Z');

    echo "BEGIN:VEVENT" . $eol;
    echo "UID:" . $event['id'] . "@" . ($_SERVER['HTTP_HOST'] ?? 'jyotidham.org') . $eol;
    echo "DTSTAMP:" . $dtstamp . $eol;
    echo "DTSTART:" . $dtstart . $eol;
    echo "DTEND:" . $dtend . $eol;
    
    // Use the helper function here
    echo "SUMMARY:" . escapeIcalText($event['event_name']) . $eol;
    echo "DESCRIPTION:" . escapeIcalText($event['event_description']) . $eol;
    echo "LOCATION:" . escapeIcalText($event['event_venue']) . $eol;
    
    echo "END:VEVENT" . $eol;
}

echo "END:VCALENDAR" . $eol;
exit; // Stop script immediately to prevent trailing whitespace
?>