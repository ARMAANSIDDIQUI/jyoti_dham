<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

try {
    // Fetch the upcoming 6 events
    $sql = "SELECT id, event_name, event_description, event_date, event_time, event_end_time FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 6";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching events: " . $e->getMessage());
    $events = []; // No upcoming events or an error occurred
}
?>



        <!-- Banner Section -->
        <section class="banner-section">
            <div class="video-container">
                <video autoplay muted loop class="banner-video" id="bannerVideo">
                    <source src="./images/video.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <!-- <div class="banner-content">
        <h1>Welcome to Jyotidham</h1>
        <p>Experience divine spirituality with us</p>
        <a href="#" class="btn btn-primary">Learn More</a> -->
    </div>
</section>
    <section id="upcoming-events">
        <div class="container">
            <h1 class="text-left text">Upcoming Events</h1>
            <div class="row">
                <?php foreach ($events as $event): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card event-card" style="height: 100%; width: 100%;">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="event.php?id=<?= $event['id']; ?>">
                                        <?= strlen($event['event_name']) > 50 ? substr($event['event_name'], 0, 47) . '...' : $event['event_name']; ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <?= strlen($event['event_description']) > 100 ? substr($event['event_description'], 0, 97) . '...' : $event['event_description']; ?>
                                </p>
                                <div class="card-actions">
                                    <div>
                                        <a href="event.php?id=<?= $event['id']; ?>" class="read">Read More</a>
                                    </div>
                                    <div>
                                        <details class="calendar-dropdown-wrapper">
                                            <summary class="btn-calendar-action">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                                </svg>
                                                <span class="btn-text">Add to Calendar</span>
                                            </summary>
                                            <div class="dropdown-content">
                                                <?php
                                                    $start_time_iso = date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time']));
                                                    $end_time_iso = date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_end_time']));
                                                    $title = urlencode($event['event_name']);
                                                    $description = urlencode($event['event_description']);
                                                ?>
                                                <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?php echo $title; ?>&dates=<?php echo gmdate('Ymd\THis\Z', strtotime($start_time_iso)); ?>/<?php echo gmdate('Ymd\THis\Z', strtotime($end_time_iso)); ?>&details=<?php echo $description; ?>" target="_blank">Google Calendar</a>
                                                <a href="export_ics.php?id=<?php echo $event['id']; ?>">Apple / Mobile</a>
                                                <a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=<?php echo $title; ?>&startdt=<?php echo $start_time_iso; ?>&enddt=<?php echo $end_time_iso; ?>&body=<?php echo $description; ?>" target="_blank">Outlook</a>
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-div">
                <a href="all-events.php" class="view-link">View All</a>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let zIndexCounter = 10;
            const eventCards = document.querySelectorAll('.event-card');

            eventCards.forEach(card => {
                card.addEventListener('click', function() {
                    this.style.zIndex = zIndexCounter++;
                });
            });
        });
    </script>

    <?php include 'includes/footer.php'; ?>
</html>