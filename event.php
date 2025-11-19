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
                            <details class="calendar-dropdown-wrapper">
                                <summary class="btn-calendar-action">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    <span class="btn-text">Add to Calendar</span>
                                </summary>
                                <div class="dropdown-content">
                                    <?php
                                        $start_time_google = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time'])));
                                        $end_time_google = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_end_time'])));
                                        $title_google = urlencode($event['event_name']);
                                        $description_google = urlencode($event['event_description']);

                                        $start_time_outlook = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time'])));
                                        $end_time_outlook = urlencode(date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_end_time'])));
                                        $title_outlook = urlencode($event['event_name']);
                                        $description_outlook = urlencode($event['event_description']);
                                    ?>
                                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?php echo $title_google; ?>&dates=<?php echo $start_time_google; ?>/<?php echo $end_time_google; ?>&details=<?php echo $description_google; ?>" target="_blank">Google Calendar</a>
                                    <a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=<?php echo $title_outlook; ?>&startdt=<?php echo $start_time_outlook; ?>&enddt=<?php echo $end_time_outlook; ?>&body=<?php echo $description_outlook; ?>" target="_blank">Outlook</a>
                                    <a href="export_ics.php?id=<?php echo $event['id']; ?>">Apple / Samsung / File</a>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
            <div class="map-section mt-4">
                <h3 class="section-title">Map Location</h3>
                <div>
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
