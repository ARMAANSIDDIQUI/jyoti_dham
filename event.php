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

require_once 'includes/header.php';
?>
<link rel="stylesheet" href="css/event.css">

<!-- Event Details Section -->
<div class="container event-page my-5">
    <div class="text-left mb-4">
        <a href="all-events.php" class="btn btn-outline-dark">&larr; View All Events</a>
    </div>

    <div class="card event-details-card">
        <div class="card-body p-0">
            <div class="event-header">
                <h1 class="event-name"><?= htmlspecialchars($event['event_name']); ?></h1>
                <p class="event-time-info">
                    <span><?= date('F d, Y', strtotime($event['event_date'])); ?></span> @
                    <span><?= date('g:i A', strtotime($event['event_time'])); ?> to <?= date('g:i A', strtotime($event['event_end_time'])); ?></span>
                    (<span><?= htmlspecialchars($event['time_zone']); ?></span>)
                </p>
            </div>

            <div class="row no-gutters">
                <div class="col-lg-8">
                    <div class="event-description">
                        <p><?= nl2br(htmlspecialchars($event['event_description'])); ?></p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="event-sidebar">
                        <div class="sidebar-section">
                            <h5 class="sidebar-title">Organizer</h5>
                            <p><?= htmlspecialchars($event['organizer']); ?></p>
                        </div>
                        <div class="sidebar-section">
                            <h5 class="sidebar-title">Venue</h5>
                            <p><?= htmlspecialchars($event['event_venue']); ?></p>
                        </div>
                        <div class="sidebar-section">
                            <details>
                            <summary>Add to Calendar</summary>
                            <ul>
                                <?php
                                    $start_time = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time'])));
                                    $end_time = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_end_time'])));
                                    $title = urlencode($event['event_name']);
                                    $description = urlencode($event['event_description']);
                                ?>
                                <li><a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?php echo $title; ?>&dates=<?php echo $start_time; ?>/<?php echo $end_time; ?>&details=<?php echo $description; ?>" target="_blank">Google</a></li>
                                <li><a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=<?php echo $title; ?>&startdt=<?php echo $start_time; ?>&enddt=<?php echo $end_time; ?>&body=<?php echo $description; ?>" target="_blank">Outlook</a></li>
                                <li><a href="export_ics.php?id=<?php echo $event['id']; ?>">Apple / Samsung / File</a></li>
                            </ul>
                        </details>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
            <div class="map-section mt-4">
                <h3 class="section-title">Map Location</h3>
                <div style="padding: 0 2rem 2rem 2rem;">
                    <iframe
                        id="googleMap"
                        src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDP4YgDI3gOakb5Y-kqrCCtCT4M8pj9Mzk&q=<?= $event['latitude']; ?>,<?= $event['longitude']; ?>"
                        width="100%"
                        height="350"
                        style="border:0; border-radius: 8px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
