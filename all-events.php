<?php
// Connect to the database
require_once 'config/db_connect.php';
require_once 'vendor/autoload.php';

$conn = DB::getInstance()->getConnection();

// Define the number of events per page
$events_per_page = 20;

// Get the current page from the request or default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $events_per_page;

// Fetch events for the current page
$sql = "
    SELECT
        id,
        day,
        event_date,
        DATE_FORMAT(event_date, '%Y-%m') AS event_month,
        event_name,
        event_description,
        event_time,
        event_end_time,
        time_zone,
        event_venue,
        is_featured,
        image_url
    FROM events
    WHERE event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT $events_per_page OFFSET $offset";

$result = $conn->query($sql);

// Prepare the events data
$events = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $events[] = $row;
}

$has_more_events_sql = "
    SELECT 1
    FROM events
    WHERE event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 1 OFFSET " . ($offset + $events_per_page);

$has_more_events_result = $conn->query($has_more_events_sql);
$has_more_events = $has_more_events_result->rowCount() > 0;

// Return JSON response if it's an AJAX request
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    echo json_encode([
        'events' => $events,
        'has_next' => $has_more_events, // Indicates if the next page is available
        'has_prev' => $page > 1         // Indicates if the previous page is available
    ]);
    exit;
}

require_once 'includes/header.php';
?>


    <div class="container">
    <div class="page-header">
        <h1>All Events</h1>
        <details class="calendar-dropdown-wrapper">
            <summary class="btn-calendar-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                <span class="btn-text">Subscribe to Calendar</span>
            </summary>
            <!-- Previous dynamic URLs using $_SERVER['HTTP_HOST'] (commented out for production deployment) -->
            <!--
            <div class="dropdown-content">
                <a href="https://calendar.google.com/calendar/render?cid=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">
                    Subscribe on Android / Google
                </a>
                <a href="webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php">
                    Subscribe on iPhone / Apple
                </a>
                <a href="https://outlook.live.com/calendar/0/addfromurl?url=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">
                    Subscribe on Outlook
                </a>
                <a href="feed.php">Sync to Mobile</a>
            </div>
            -->
            <div class="dropdown-content">
                <a href="https://calendar.google.com/calendar/render?cid=webcal://jyotidham.ca/feed.php" target="_blank">
                    Subscribe on Android / Google
                </a>
                <a href="webcal://jyotidham.ca/feed.php">
                    Subscribe on iPhone / Apple
                </a>
                <a href="https://outlook.live.com/calendar/0/addfromurl?url=webcal://jyotidham.ca/feed.php" target="_blank">
                    Subscribe on Outlook
                </a>
                <a href="feed.php">Sync to Mobile</a>
            </div>
        </details>
    </div>
</div>
<div class="container">
    <div id="event-container">            <p class="text-center">Loading events...</p>
        </div>

        <!-- Pagination Links -->
        <div id="pagination" class="text-center">
            <button id="prev-page" class="btn btn-dark" style="display: none;">Previous</button>
            <button id="next-page" class="btn btn-dark" style="display: none;">Next</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentPage = 1;
            const prevButton = document.getElementById("prev-page");
            const nextButton = document.getElementById("next-page");
            const eventContainer = document.getElementById("event-container");

            // Function to fetch events and update the DOM
            function fetchEvents(page) {
                fetch(`all-events.php?page=${page}&ajax=1`)
                    .then(response => response.json())
                    .then(data => {
                        eventContainer.innerHTML = ""; // Clear existing events

                        if (data.events.length === 0) {
                            eventContainer.innerHTML = `
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-calendar-x" viewBox="0 0 16 16">
                                        <path d="M8.982 8.982a.5.5 0 0 1-.707 0L6 6.707 3.707 9.000a.5.5 0 0 1-.707-.707L5.293 6 3.000 3.707a.5.5 0 0 1 .707-.707L6 5.293 8.293 3.000a.5.5 0 0 1 .707.707L6.707 6l2.275 2.275a.5.5 0 0 1 0 .707z"/>
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    <p class="mt-3">No more upcoming events available at the moment. Please check back later.</p>
                                    <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
                                </div>
                            `;
                        } else {
                            data.events.forEach(event => {
                                const eventHTML = `
                                    <div class="event-card">
                                        <div class="event-card__image">
                                            <a href="event.php?id=${event.id}">
                                                <img src="${event.image_url || 'https://via.placeholder.com/400x300'}" alt="${event.event_name}">
                                            </a>
                                        </div>
                                        <div class="event-card__content">
                                            <div class="event-date">
                                                <span>${new Date(event.event_date + 'T00:00:00').toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                                <span class="separator"></span>
                                                <span class="event-day-name">${event.day}</span>
                                            </div>
                                            <h3 class="event-title">
                                                <a href="event.php?id=${event.id}" class="event-title-link">${event.event_name}</a>
                                            </h3>
                                            <div class="event-meta">
                                                <div class="meta-row">
                                                    <svg class="meta-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                    <span>${event.event_time} - ${event.event_end_time} ${event.time_zone}</span>
                                                </div>
                                                <div class="meta-row">
                                                    <svg class="meta-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                    <span>${event.event_venue}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="event-card__action">
                                            <details class="calendar-dropdown-wrapper">
                                                <summary class="btn-calendar-action">
                                                    Add to Calendar
                                                </summary>
                                                <div class="dropdown-content">
                                                    <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.event_name)}&dates=${encodeURIComponent(event.event_date.replace(/-/g, '') + 'T' + event.event_time.replace(/:/g, '') + '00')}/${encodeURIComponent(event.event_date.replace(/-/g, '') + 'T' + event.event_end_time.replace(/:/g, '') + '00')}&details=${encodeURIComponent(event.event_description)}&location=${encodeURIComponent(event.event_venue)}&ctz=${event.time_zone}" target="_blank">Google Calendar</a>
                                                    <a href="export_ics.php?id=${event.id}">Apple / Mobile</a>
                                                </div>
                                            </details>
                                        </div>
                                    </div>
                                `;
                                eventContainer.insertAdjacentHTML("beforeend", eventHTML);
                            });
                        }

                        // Enable/Disable pagination buttons
                        prevButton.style.display = data.has_prev ? "inline-block" : "none";
                        nextButton.style.display = data.has_next ? "inline-block" : "none";
                    })
                    .catch(error => {
                        console.error("Error fetching events:", error);
                        eventContainer.innerHTML = "<p class='text-center'>There was an error loading the events. Please try again later.</p>";
                    });
            }

            // Event listeners for pagination
            prevButton.addEventListener("click", function () {
                if (currentPage > 1) {
                    currentPage--;
                    fetchEvents(currentPage);
                }
            });

            nextButton.addEventListener("click", function () {
                currentPage++;
                fetchEvents(currentPage);
            });

            // Initial fetch
            fetchEvents(currentPage);
        });
    </script>

<?php include 'includes/footer.php'; ?>
