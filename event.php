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


<div class="back-link-container">
    <a href="all-events.php" class="back-link">
        &larr; Back to All Events
    </a>
</div>

<div class="event-page-container">

    <div class="main-content">

        <div class="event-header">
            <h1 class="event-title"><?= htmlspecialchars($event['event_name']); ?></h1>
            <div class="event-date-large">
                <?= date('l, F j, Y • g:i A', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?> - <?= date('g:i A', strtotime($event['event_end_time'])); ?>
            </div>
        </div>

        <div class="hero-image-container">
            <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
        </div>

        <div class="event-description">
            <h3>About this Event</h3>
            <p>
                <?= nl2br(htmlspecialchars($event['event_description'])); ?>
            </p>
        </div>
    </div>

    <div class="event-sidebar">

        <div class="sidebar-card">
            <span class="sidebar-heading">Date & Time</span>
            <p style="font-weight: 600; font-size: 1.1rem; margin-bottom: 15px;">
                <?= date('M d, Y', strtotime($event['event_date'])); ?><br>
                <?= date('g:i A', strtotime($event['event_time'])); ?> - <?= date('g:i A', strtotime($event['event_end_time'])); ?>
            </p>
            <details class="calendar-dropdown-wrapper">
                <summary class="btn-calendar-action" style="width: 100%; border-radius: 8px; padding: 12px; justify-content: center;">
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

        <div class="sidebar-card">
            <span class="sidebar-heading">Venue</span>
            <p style="margin-bottom: 10px; font-weight: 500;">
                <?= htmlspecialchars($event['event_venue']); ?>
            </p>
        </div>

        <div class="sidebar-card">
            <span class="sidebar-heading">Location</span>
            
            <?php if (!empty($event['address'])): ?>
            <p style="margin-bottom: 10px; font-weight: 500;">
                <?= htmlspecialchars($event['address']); ?>
            </p>
            <?php endif; ?>

            <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
            <div style="width: 100%; background: #eee; border-radius: 8px; overflow: hidden;">
                <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDP4YgDI3gOakb5Y-kqrCCtCT4M8pj9Mzk&q=<?= $event['latitude']; ?>,<?= $event['longitude']; ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <?php endif; ?>
        </div>

        <div class="sidebar-card">
            <span class="sidebar-heading">Organizer</span>
            <div class="organizer-details-container" style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 40px; height: 40px; background: #991b1b; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">JD</div>
                <span><?= htmlspecialchars($event['organizer']); ?></span>
            </div>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>

