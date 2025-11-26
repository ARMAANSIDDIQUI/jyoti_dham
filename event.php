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
<style>
    /* Custom CSS for animations and subtle effects */
    .fade-in {
        animation: fadeIn 0.8s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-hover-shadow:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transition: all 0.3s ease-in-out;
    }
    .card-hover-shadow {
        transition: all 0.3s ease-in-out;
    }
    .section-divider {
        border-bottom: 1px solid #e9ecef; /* neutral gray */
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
    }
    /* Responsive adjustments for iframe */
    .embed-responsive {
        position: relative;
        display: block;
        width: 100%;
        padding: 0;
        overflow: hidden;
    }
    .embed-responsive::before {
        content: "";
        display: block;
        padding-top: 56.25%; /* 16:9 aspect ratio */
    }
    .embed-responsive .embed-responsive-item,
    .embed-responsive iframe,
    .embed-responsive embed,
    .embed-responsive object,
    .embed-responsive video {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    .custom-rounded-xl {
        border-radius: 2rem !important; /* Extra large rounded corners */
    }
</style>

<div class="container my-5 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="all-events.php" class="btn btn-outline-secondary btn-sm">&larr; View All Events</a>
        <!-- Potentially add a share button or other actions here -->
    </div>
    <div class="card shadow-lg p-4 p-md-5 bg-white custom-rounded-xl">
        <section class="event-header-section mb-4">
            <div class="card shadow-sm p-4 bg-white">
                <h1 class="display-5 fw-bold text-dark mb-2"><?= htmlspecialchars($event['event_name']); ?></h1>
                <p class="lead text-muted mb-0">
                    <i class="bi bi-calendar me-2"></i>
                    <span><?= date('F d, Y', strtotime($event['event_date'])); ?></span>
                    <i class="bi bi-clock ms-3 me-2"></i>
                    <span><?= date('g:i A', strtotime($event['event_time'])); ?> to <?= date('g:i A', strtotime($event['event_end_time'])); ?></span>
                    <span class="text-secondary ms-2">(<?= htmlspecialchars($event['time_zone']); ?>)</span>
                </p>
            </div>
        </section>

        <div class="row">
            <div class="col-lg-8">
                <section class="event-image-section mb-4">
                    <div class="card shadow-sm p-4 bg-white card-hover-shadow">
                        <img src="<?= htmlspecialchars($event['image_url']) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($event['event_name']) ?>">
                    </div>
                </section>

                <section class="event-description-section mb-4">
                    <div class="card shadow-sm p-4 bg-white card-hover-shadow">
                        <h3 class="h5 fw-bold text-dark section-divider pb-2 mb-3">Event Details</h3>
                        <div class="text-secondary">
                            <p><?= nl2br(htmlspecialchars($event['event_description'])); ?></p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <aside class="event-sidebar-section">

                                        <div class="card shadow-sm mb-4 bg-white card-hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark section-divider pb-2 mb-3">Add to Calendar</h5>
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

                    <div class="card shadow-sm mb-4 bg-white card-hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark section-divider pb-2 mb-3">Organizer</h5>
                            <p class="card-text text-secondary"><?= htmlspecialchars($event['organizer']); ?></p>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4 bg-white card-hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark section-divider pb-2 mb-3">Venue</h5>
                            <p class="card-text text-secondary"><?= htmlspecialchars($event['event_venue']); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
                    <div class="card shadow-sm mb-4 bg-white card-hover-shadow">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-dark section-divider pb-2 mb-3">Location</h5>
                            <div class="embed-responsive embed-responsive-16by9 rounded overflow-hidden">
                                <iframe
                                    id="googleMap"
                                    src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDP4YgDI3gOakb5Y-kqrCCtCT4M8pj9Mzk&q=<?= $event['latitude']; ?>,<?= $event['longitude']; ?>"
                                    width="100%"
                                    height="350"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </aside>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
