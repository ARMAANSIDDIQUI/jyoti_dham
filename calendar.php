<?php
require_once 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<link rel="stylesheet" href="./css/calendar.css">
<style>
    /* Custom CSS for animations and calendar cell styling */
    .fade-in {
        animation: fadeIn 0.8s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Calendar Cell Styling */
    .fc-daygrid-day {
        background-color: #f8f9fa; /* Light gray background */
        border: 1px solid #dee2e6; /* Darker gray border */
        transition: all 0.3s ease;
        cursor: pointer; /* Indicate interactivity */
    }

    .fc-daygrid-day:hover {
        transform: scale(1.02);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Subtle shadow on hover */
        z-index: 1; /* Ensure hover effect is on top */
    }

    /* Ensure day numbers and events are visible on hover */
    .fc-daygrid-day:hover .fc-daygrid-day-number,
    .fc-daygrid-day:hover .fc-event {
        color: inherit; /* Maintain original color or adjust if needed */
    }

    /* Adjust FullCalendar's default border for a cleaner look */
    .fc-theme-standard td, .fc-theme-standard th {
        border: none; /* Remove default FullCalendar borders */
    }
    .fc-daygrid-body-unbalanced .fc-daygrid-day-events {
        min-height: 30px; /* Ensure some space for events */
    }
</style>

<div class="container mt-5 mb-5 fade-in">
    <h1 class="text-center mb-4">Satsang Calendar</h1>

    <div class="d-grid gap-2 d-md-block mb-4">
        <details class="calendar-dropdown-wrapper">
            <summary class="btn-calendar-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                </svg>
                <span class="btn-text">Subscribe to Calendar</span>
            </summary>
            <div class="dropdown-content">
                <a href="webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php">Sync to Mobile</a>
                <a href="https://calendar.google.com/calendar/render?cid=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">Google Calendar</a>
                <a href="https://outlook.live.com/calendar/0/addfromurl?url=webcal://<?php echo $_SERVER['HTTP_HOST']; ?>/feed.php" target="_blank">Outlook</a>
            </div>
        </details>
    </div>

    <div id="calendar"></div>
</div>

<div id="eventModal" class="modal">
    <div class="modal-content">
        <h2 id="modalTitle"></h2>
        <div id="modalVenue"></div>
        <div id="modalDescription"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var modal = document.getElementById('eventModal');
    var span = document.getElementsByClassName("close-button")[0];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: 'api/get_events.php',
        eventClick: function(info) {
            window.location.href = 'event.php?id=' + info.event.id;
        },
        eventMouseEnter: function(info) {
            // Populate and show the modal
            document.getElementById('modalTitle').innerText = info.event.title;
            document.getElementById('modalVenue').innerText = 'Venue: ' + (info.event.extendedProps.venue || 'N/A');
            document.getElementById('modalDescription').innerText = 'Description: ' + (info.event.extendedProps.description || 'N/A');
            
            // Position the modal
            modal.style.left = info.jsEvent.pageX + 'px';
            modal.style.top = info.jsEvent.pageY + 'px';
            
            modal.style.display = "block";
        },
        eventMouseLeave: function(info) {
            modal.style.display = "none";
        }
    });

    calendar.render();
});
</script>

<?php
require_once 'includes/footer.php';
?>
