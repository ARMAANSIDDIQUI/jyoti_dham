<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

try {
    // Fetch the upcoming 6 events
    $sql = "SELECT id, event_name, event_description FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 6";
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
                                <a href="event.php?id=<?= $event['id']; ?>" class="read">Read More</a>
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

    <?php include 'includes/footer.php'; ?>
</html>