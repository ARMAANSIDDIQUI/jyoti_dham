<?php
// Include your database connection file
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Get the event ID from the URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Prepare and execute the SQL query to fetch event data
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $event_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if event data is found
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        echo "Event not found.";
        exit;
    }
} else {
    echo "No event ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event['event_name']; ?> - Event Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/event.css">
</head>

<body>

    <?php require_once 'includes/header.php'; ?>

    <!-- Event Details Section -->
    <div class="container px-6">
        <div class="container all-events">
            <a href="event_list.php"><< All Events</a>
        </div>
        <div class="container">
            <h1 class="event-heading"><?= $event['event_name']; ?></h1>
            <p><span><?= date('F d, Y', strtotime($event['event_date'])); ?></span> @ 
            <span><?= date('g:i A', strtotime($event['event_time'])); ?> to <?= date('g:i A', strtotime($event['event_end_time'])); ?></span>
            <span><?= $event['time_zone']; ?></span></p>
        </div>
        <div class="container event-description">
            <h3><?= $event['event_description']; ?></h3>
        </div>

        <div class="container">
            <details>
                <summary>Add to Calendar</summary>
                <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?= urlencode($event['event_name']); ?>&dates=<?= date('Ymd\THis\Z', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?>/<?= date('Ymd\THis\Z', strtotime($event['event_date'] . ' ' . $event['event_end_time'])); ?>&details=<?= urlencode($event['event_description']); ?>&location=<?= urlencode($event['event_venue']); ?>" target="_blank">Google Calendar</a><br>
                <a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=<?= urlencode($event['event_name']); ?>&startdt=<?= date('Y-m-d\TH:i:s\Z', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?>&enddt=<?= date('Y-m-d\TH:i:s\Z', strtotime($event['event_date'] . ' ' . $event['event_end_time'])); ?>&body=<?= urlencode($event['event_description']); ?>&location=<?= urlencode($event['event_venue']); ?>" target="_blank">Outlook.com</a><br>
                <a href="export_ics.php?id=<?= $event['id']; ?>" target="_blank">Apple / Outlook App / Samsung</a>
            </details>
        </div>

        <div class="container">
            <div class="row">
                <!-- Organizer Column -->
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h3 class="t-head">Organizer</h3>
                    <p><?= $event['organizer']; ?></p>
                </div>

                <!-- Venue Column -->
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h3 class="t-head">Venue</h3>
                    <p><?= $event['event_venue']; ?></p>
                </div>

                <!-- Map Column -->
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <h3 class="t-head">Map Location</h3>
                    <iframe
                        id="googleMap"
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDP4YgDI3gOakb5Y-kqrCCtCT4M8pj9Mzk&q=<?= $event['latitude']; ?>,<?= $event['longitude']; ?>"
                        width="100%"
                        height="250"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
