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

    /* Remove existing hover styles to replace with new ones */
    .fc-daygrid-day:hover {
        background: white !important; /* Light gray background on hover */
        transition: background 0.15s ease-in-out; /* Smooth background transition */
        transform: scale(1.02);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); /* Subtle shadow on hover */
        z-index: 1; /* Ensure hover effect is on top */
    }

    /* Ensure day numbers and events are visible on hover */
    .fc-daygrid-day:hover .fc-daygrid-day-number,
    .fc-daygrid-day:hover .fc-event {
        color: inherit; /* Maintain original color or adjust if needed */
    }

    /* FullCalendar specific overrides for borders */
    .fc-daygrid-body-unbalanced .fc-daygrid-day-events {
        min-height: 20px; /* Ensure some space for events */
    }

    .fc-col-header-cell-cushion {
        font-size: 0.8em;
        padding: 2px 4px;
        line-height: 1.2;
    }

    /* Ensure borders are visible for all cells */
    .fc-daygrid-day {
        border: 1px solid #dee2e6; /* Explicitly set border for all day cells */
    }
    /* Remove the line between date number and event list */
    .fc-daygrid-day-top {
        border-bottom: none !important;
        display: flex; /* Keep flex for alignment */
        justify-content: flex-end;
        padding: 5px;
    }
    .fc-daygrid-event-harness {
        border-top: none !important;
    }

    .fc-daygrid-day-frame {
        border-right: 1px solid #dee2e6; /* Add border to the right of the cell frame */
    }
    .fc-daygrid-day:last-child .fc-daygrid-day-frame {
        border-right: none; /* Remove right border for the last cell in a row */
    }
    .fc-daygrid-day-number {
        font-weight: bold;
    }

    /* Event Hover Popover Styling */
    .fc-popover {
        background: #ffffff !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    }

    /* Modal Styling */
    #eventModal.modal {
        background-color: #ffffff; /* Make modal background transparent to show card shadow */
        box-shadow: none; /* Remove default modal shadow */
        padding: 0;
        border-radius: 0.5rem; /* Match Bootstrap card border-radius */
        overflow: visible; /* Allow shadow to be visible */
    }
    #eventModal .card-body {
        padding: 1rem;
    }
    #eventModal .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        color: #333;
    }
    #eventModal .card-text {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 0.25rem;
    }

    /* Smaller font in week/day view */
    .fc-timegrid-event .fc-event-title {
        font-size: 0.85em;
        line-height: 1.2;
    }

    @media (max-width: 768px) {
        #calendar {
            height: 70vh;
        }
        .display-5 {
            font-size: 2rem;
            text-align: center;
        }
        .fc-header-toolbar {
            flex-direction: column !important;
            align-items: center !important;
        }
        .fc-header-toolbar .fc-toolbar-chunk {
            margin-bottom: 10px;
        }
        .container.mt-5 {
            margin-top: 2rem !important;
        }
        .container.mb-5 {
            margin-bottom: 2rem !important;
        }
        .card.p-4 {
            padding: 1rem !important;
        }
        .fc-daygrid-day-events {
            overflow: hidden;
        }
        .fc-event-title {
            visibility: hidden;
        }
    }
</style>

<div class="container mt-5 mb-5 fade-in">
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                <h1 class="display-5 fw-bold text-dark mb-3 mb-md-0">Satsang Calendar</h1>
                <div class="d-grid gap-2 d-md-block">
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
                </div>
            </div>
        </div>
    
        <div id="calendar"></div>
</div>

<div id="eventModal" class="modal card shadow-sm">
    <div class="card-body">
        <h5 class="card-title" id="modalTitle"></h5>
        <p class="card-text" id="modalVenue"></p>
        <p class="card-text" id="modalDescription"></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var modal = document.getElementById('eventModal');

    function getCalendarView() {
        return localStorage.getItem('calendarView') || 'dayGridMonth';
    }

    function getHeaderToolbar() {
        return {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        };
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: getCalendarView(),
        headerToolbar: getHeaderToolbar(),
        aspectRatio: 2.5,
        events: 'api/get_events.php',
        eventClick: function(info) {
            window.location.href = 'event.php?id=' + info.event.id;
        },
        datesSet: function(dateInfo) {
            localStorage.setItem('calendarView', dateInfo.view.type);
        },
        eventMouseEnter: function(info) {
            if (window.innerWidth >= 768) { // Only show modal on desktop
                // Populate and show the modal
                document.getElementById('modalTitle').innerText = info.event.title;
                document.getElementById('modalVenue').innerText = 'Venue: ' + (info.event.extendedProps.venue || 'N/A');
                document.getElementById('modalDescription').innerText = 'Description: ' + (info.event.extendedProps.description || 'N/A');
                
                // Position the modal
                modal.style.left = info.jsEvent.pageX + 'px';
                modal.style.top = info.jsEvent.pageY + 'px';
                
                modal.style.display = "block";
            }
        },
        eventMouseLeave: function(info) {
            if (window.innerWidth >= 768) { // Only hide modal on desktop
                modal.style.display = "none";
            }
        }
    });

    calendar.render();
});
</script>

<?php
require_once 'includes/footer.php';
?>
