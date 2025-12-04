<?php
require_once __DIR__ . '/includes/header.php'; // Include the header component

try {
    // Fetch the upcoming 6 events
    $sql = "SELECT id, event_name, event_description, event_date, event_time, event_end_time, image_url FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 6";
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
                <video autoplay muted loop class="banner-video" id="bannerVideo" preload="metadata" poster="images/banner.png" loading="lazy">
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
            <h1 class="text-center text">Upcoming Events</h1>
            <div class="upcoming-events-grid">
                <?php foreach ($events as $event): ?>
                    <div class="event-card-index">
                        <a href="event.php?id=<?= $event['id'] ?>" class="card-image-link">
                            <img src="<?= !empty($event['image_url']) ? htmlspecialchars($event['image_url']) : 'images/Logo.svg' ?>" alt="<?= htmlspecialchars($event['event_name']); ?> Thumbnail" loading="lazy">
                        </a>
                        <div class="card-content">
                            <span class="card-date"><?= date('M d, Y', strtotime($event['event_date'])) ?></span>
                            <h3 class="card-title">
                                <a href="event.php?id=<?= $event['id']; ?>">
                                    <?= htmlspecialchars(strlen($event['event_name']) > 50 ? substr($event['event_name'], 0, 47) . '...' : $event['event_name']); ?>
                                </a>
                            </h3>
                            <p class="card-desc">
                                <?= htmlspecialchars(strlen($event['event_description']) > 100 ? substr($event['event_description'], 0, 97) . '...' : $event['event_description']); ?>
                            </p>
                            <div class="card-footer">
                                <a href="event.php?id=<?= $event['id']; ?>" class="read-more-link">Read More</a>
                                <details class="calendar-dropdown-wrapper">
                                    <summary class="btn-add-cal-small">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <span>Add to Cal</span>
                                    </summary>
                                    <div class="dropdown-content">
                                        <?php
                                            $start_time_iso = date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_time']));
                                            $end_time_iso = date('Y-m-d\TH:i:s', strtotime($event['event_date'] . ' ' . $event['event_end_time']));
                                            $title = urlencode($event['event_name']);
                                            $description = urlencode($event['event_description']);
                                        ?>
                                        <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=<?php echo $title; ?>&dates=<?php echo gmdate('Ymd\THis\Z', strtotime($start_time_iso)); ?>/<?php echo gmdate('Ymd\THis\Z', strtotime($end_time_iso)); ?>&details=<?php echo $description; ?>" target="_blank" rel="noopener noreferrer">Google Calendar</a>
                                        <a href="export_ics.php?id=<?php echo $event['id']; ?>">Apple / Mobile</a>
                                        <a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=<?php echo $title; ?>&startdt=<?php echo $start_time_iso; ?>&enddt=<?php echo $end_time_iso; ?>&body=<?php echo $description; ?>" target="_blank" rel="noopener noreferrer">Outlook</a>
                                    </div>
                                </details>
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

    <section id="index-podcasts">
    <div class="container">
        <h1 class="text-center text">we are available on !!</h1>
        <?php
        $cards = [
            [
                'link_class' => 'amazon-music',
                'alt' => 'Amazon Music',
                'img_src' => 'https://res.cloudinary.com/dfxl3oy4y/image/upload/v1764850505/amazon_music_o8yaqk.svg'
            ],
            [
                'link_class' => 'red-circle',
                'alt' => 'RedCircle',
                'img_src' => 'https://res.cloudinary.com/dfxl3oy4y/image/upload/v1764850467/redcircle_qhjsso.svg'
            ],
            [
                'link_class' => 'apple-podcasts',
                'alt' => 'Apple Podcasts',
                'img_src' => 'https://res.cloudinary.com/dfxl3oy4y/image/upload/v1764851629/applepodcasts_cssuzw.png'
            ]
        ];
        ?>
        <div class="card-container index-podcast-row">
            <?php foreach ($cards as $card): ?>
                <div class="card index-podcast-col">
                    <a href="podcast.php" class="index-podcast-link <?= $card['link_class'] ?>">
                        <img src="<?= $card['img_src'] ?>" alt="<?= $card['alt'] ?>" class="index-podcast-card">
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="view-div">
            <a href="podcast.php" class="view-link">View All</a>
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