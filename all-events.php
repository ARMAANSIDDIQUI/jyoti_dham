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
<link rel="stylesheet" href="css/all-events.css">

    <div class="container">
        <h1 class="text-center mb-4">All Events</h1>
        <details class="calendar-dropdown-wrapper">
            <summary class="btn-calendar-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                <span class="btn-text">Subscribe to Calendar</span>
            </summary>
                        <!-- <div class="dropdown-content">
                            <a href="feed.php">Sync to Mobile</a>
                            <a href="https://calendar.google.com/calendar/render?cid=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">Google Calendar</a>
                            <a href="https://outlook.live.com/calendar/0/addfromurl?url=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">Outlook</a>
                        </div> -->
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
        </details>
        <!-- Event container to dynamically update -->
        <div id="event-container" class="card-view">
            <p class="text-center">Loading events...</p>
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
                                    <article class="event-item" role="article" aria-labelledby="event-title-${event.id}">
                                        <a href="event.php?id=${event.id}" class="event-image-link">
                                            <img src="${event.image_url}" alt="${event.event_name}" class="event-image">
                                        </a>
                                        <div class="event-content">
                                            <div class="event-date">
                                                <span class="day">${event.day.substring(0, 3).toUpperCase()}</span>
                                                <span class="date">${new Date(event.event_date).getDate()}</span>
                                            </div>
                                            <div class="event-details">
                                                <div class="event-header">
                                                    ${event.is_featured ? `
                                                    <span class="featured-icon">
                                                        <svg width="15px" height="15px" viewBox="0 0 8 10" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0 0h8v10L4.049 7.439 0 10V0z"></path>
                                                        </svg>
                                                        <span class="featured-text">Featured</span>
                                                    </span>` : ''}
                                                    <span class="event-time">${event.event_date} @ ${event.event_time} - ${event.event_end_time} ${event.time_zone}</span>
                                                </div>
                                                <h3 class="event-title" id="event-title-${event.id}">
                                                    <a href="event.php?id=${event.id}" class="event-link">${event.event_name}</a>
                                                </h3>
                                                <address class="event-venue">${event.event_venue}</address>
                                                <div class="event-description" id="event-description-${event.id}">
                                                    <p>${event.event_description.substring(0, 150)}${event.event_description.length > 150 ? "..." : ""}</p>
                                                </div>
                                                <details class="calendar-dropdown-wrapper">
                                                    <summary class="btn-calendar-action">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                                                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                                        </svg>
                                                        <span class="btn-text">Add to Calendar</span>
                                                    </summary>
                                                    <div class="dropdown-content">
                                                        <a href="https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(event.event_name)}&dates=${encodeURIComponent(event.event_date + 'T' + event.event_time)}/${encodeURIComponent(event.event_date + 'T' + event.event_end_time)}&details=${encodeURIComponent(event.event_description)}" target="_blank">Google Calendar</a>
                                                        <a href="export_ics.php?id=${event.id}">Apple / Mobile</a>
                                                        <a href="https://outlook.live.com/calendar/0/deeplink/compose?subject=${encodeURIComponent(event.event_name)}&startdt=${encodeURIComponent(event.event_date + 'T' + event.event_time)}&enddt=${encodeURIComponent(event.event_date + 'T' + event.event_end_time)}&body=${encodeURIComponent(event.event_description)}" target="_blank">Outlook</a>
                                                    </div>
                                                </details>
                                            </div>
                                        </div>
                                    </article>
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
