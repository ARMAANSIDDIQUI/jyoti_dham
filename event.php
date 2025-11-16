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
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if event data is found
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
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
            <a href="calender.php"><< All Events</a>
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
            <div class="row">
                <!-- Details Column -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h2 class="t-head">Details</h2>
                    <h3 class="t-head">Date:</h3>
                    <p><?= date('F d, Y', strtotime($event['event_date'])); ?></p>
                    <h3 class="t-head">Time:</h3>
                    <p><span><?= date('g:i A', strtotime($event['event_time'])); ?> to <?= date('g:i A', strtotime($event['event_end_time'])); ?></span> <span><?= $event['time_zone']; ?></span></p>
                </div>

                <!-- Organizer Column -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h3 class="t-head">Organizer</h3>
                    <p><?= $event['organizer']; ?></p>
                </div>

                <!-- Venue Column -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h3 class="t-head">Venue</h3>
                    <p><?= $event['event_venue']; ?></p>
                </div>

                <!-- Map Column -->
                <div class="col-lg-3 col-md-6 col-sm-12">
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
