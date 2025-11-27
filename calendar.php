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

    .fc-event-main, .fc-event-title {
        color: white !important;
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

    @media screen and (max-width: 768px) {
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

        /* From user */
        .calendar-header {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .calendar-header h1 {
            font-size: 1.5rem;
            margin: 0;
            text-align: center;
        }
        .calendar-controls-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
        }
        .calendar-controls-row h2, 
        .current-month-label {
            font-size: 1.1rem;
            margin: 0;
        }
        .nav-btn {
            padding: 10px 15px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .view-switcher {
            display: flex !important;
            flex-direction: row !important;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .view-switcher button {
            flex: 1;
            padding: 12px 0;
            background: #f9fafb;
            border: none;
            border-right: 1px solid #eee;
            font-size: 0.9rem;
            text-align: center;
        }
        .view-switcher button:last-child {
            border-right: none;
        }
        .mobile-action-row {
            display: flex !important;
            flex-direction: row !important;
            gap: 10px;
            width: 100%;
            align-items: stretch;
        }
        .btn-view-all {
            flex: 1;
            background-color: #991b1b;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-weight: 600;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-subscribe {
            flex: 1;
            background-color: transparent;
            color: #991b1b;
            border: 1px solid #991b1b;
            border-radius: 8px;
            padding: 12px 0;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }
    }

    /* 2. Polish the "Subscribe" Button */
    .btn-calendar-action {
        background-color: transparent;
        border: 2px solid #800000; /* Maroon color */
        color: #800000;
        padding: 8px 18px;
        border-radius: 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-left: 1rem;
    }
    .btn-calendar-action:hover {
        background-color: #800000;
        color: white;
    }
    .calendar-icon {
        margin-right: 5px;
    }

    /* 3. Refine the Calendar Grid */
    /* Header Row */
    /* .fc-col-header-cell {
        background-color: #FFE4E1;
    } */
    .fc-col-header-cell-cushion {
        color: #495057; /* Dark grey for better contrast */
        font-weight: 600; /* Slightly bold */
        font-size: 0.9em;
        padding: 5px;
    }
    /* Current Day Indicator */
    .fc-day-today {
        background-color: #f8f9fa !important; /* Use a very soft neutral gray fill */
        border: 2px solid #800000 !important; /* Thick maroon border */
    }
    /* Grid Lines */
    .fc-daygrid-day, .fc-timegrid-slot-lane, .fc-col-header-cell {
        border-color: #f0f0f0 !important; /* Lighter grid lines */
    }

    /* 4. Upgrade the Event "Chips" */
    .fc-event {
        border-radius: 4px !important;
        padding-left: 5px !important;
        background-color: #DB7093 !important; /* Soft Rose */
        border-color: #DB7093 !important;
    }
    /* Example for color coding - you can assign classes in get_events.php */
    .fc-event.event-type-1 {
        background-color: #E6E6FA !important; /* Lavender */
        border-color: #E6E6FA !important;
    }
    .fc-event.event-type-2 {
        background-color: #FFDAB9 !important; /* Pale Gold */
        border-color: #FFDAB9 !important;
    }

    /* 5. Vertical Rhythm (Spacing) */
    .calendar-container {
        max-width: 1200px;
        margin: 0 auto; /* Center the container */
    }
</style>

<div class="container mt-5 mb-5 fade-in calendar-container">
    <div class="card shadow-lg" style="overflow: hidden; border-radius: .75rem;">
        <div class="p-4 border-bottom bg-white">
            <h2 class="h4 fw-bold text-dark my-0 text-center">Satsang Calendar</h2>
        
            <!-- Desktop Header -->
            <div class="d-none d-lg-flex justify-content-between align-items-center mt-3">
                <div class="d-flex align-items-center">
                    <div id="calendar-nav" class="btn-group">
                        <button id="prev-btn" title="Previous" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
                        </button>
                        <button id="today-btn" title="Today" class="btn btn-outline-secondary">Today</button>
                        <button id="next-btn" title="Next" class="btn btn-outline-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
                        </button>
                    </div>
                    <h3 id="calendar-title" class="h5 my-0 mx-3 text-nowrap"></h3>
                </div>
        
                <div class="d-flex align-items-center gap-3">
                    <a href="all-events.php" class="btn-view-all">View All</a>
                    <div class="view-switcher me-2">
                        <button type="button" class="active" data-view="dayGridMonth">Month</button>
                        <button type="button" data-view="timeGridWeek">Week</button>
                        <button type="button" data-view="timeGridDay">Day</button>
                    </div>
                    <details class="calendar-dropdown-wrapper ms-2" style="margin-bottom: 0;">
                        <summary class="btn-calendar-action">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="calendar-icon">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                            <span class="btn-text">Subscribe to Calendar</span>
                        </summary>
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
        
            <!-- Mobile Header -->
            <div class="d-lg-none calendar-header mt-3">
                <div class="calendar-controls-row">
                    <button id="mobile-prev-btn" class="nav-btn">&lt;</button>
                    <h3 id="mobile-calendar-title" class="current-month-label"></h3>
                    <button id="mobile-next-btn" class="nav-btn">&gt;</button>
                </div>
            
                <div class="view-switcher">
                    <button type="button" data-view="dayGridMonth">Month</button>
                    <button type="button" data-view="timeGridWeek">Week</button>
                    <button type="button" data-view="timeGridDay">Day</button>
                </div>
            
                <div class="mobile-action-row">
                    <a href="all-events.php" class="btn-view-all">View All</a>
                    <a href="feed.php" class="btn-subscribe">Subscribe</a>
                </div>
            </div>
        </div>
        <div class="p-0">
            <div id="calendar"></div>
        </div>
    </div>
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
        let view = localStorage.getItem('calendarView') || 'dayGridMonth';
        // Make sure the active button corresponds
        const activeButtons = document.querySelectorAll('.view-switcher button.active');
        activeButtons.forEach(btn => btn.classList.remove('active'));
        
        const newActiveButtons = document.querySelectorAll(`.view-switcher button[data-view="${view}"]`);
        newActiveButtons.forEach(btn => btn.classList.add('active'));

        return view;
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: getCalendarView(),
        headerToolbar: false, // Custom header
        aspectRatio: 1.8,
        events: 'api/get_events.php',
        dayMaxEvents: true, // show "+more" link when there are too many events
        eventClick: function(info) {
            window.location.href = 'event.php?id=' + info.event.id;
        },
        datesSet: function(dateInfo) {
            localStorage.setItem('calendarView', dateInfo.view.type);
            document.getElementById('calendar-title').innerText = dateInfo.view.title;
            document.getElementById('mobile-calendar-title').innerText = dateInfo.view.title;
        },
        eventMouseEnter: function(info) {
            if (window.innerWidth >= 768) { // Only show modal on desktop
                document.getElementById('modalTitle').innerText = info.event.title;
                document.getElementById('modalVenue').innerText = 'Venue: ' + (info.event.extendedProps.venue || 'N/A');
                document.getElementById('modalDescription').innerText = 'Description: ' + (info.event.extendedProps.description || 'N/A');
                
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
    document.getElementById('calendar-title').innerText = calendar.view.title;
    document.getElementById('mobile-calendar-title').innerText = calendar.view.title;

    // Custom header buttons
    document.getElementById('prev-btn').addEventListener('click', function() {
        calendar.prev();
    });
    document.getElementById('today-btn').addEventListener('click', function() {
        calendar.today();
    });
    document.getElementById('next-btn').addEventListener('click', function() {
        calendar.next();
    });

    // Mobile header buttons
    document.getElementById('mobile-prev-btn').addEventListener('click', function() {
        calendar.prev();
    });
    document.getElementById('mobile-next-btn').addEventListener('click', function() {
        calendar.next();
    });

    var viewButtons = document.querySelectorAll('.view-switcher button');
    viewButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Remove active class from all view switcher buttons in both headers
            document.querySelectorAll('.view-switcher button').forEach(btn => btn.classList.remove('active'));
            
            // Add active class to the clicked button
            this.classList.add('active');
            
            // Also add active class to the corresponding button in the other header
            const view = this.dataset.view;
            document.querySelectorAll(`.view-switcher button[data-view="${view}"]`).forEach(btn => btn.classList.add('active'));

            calendar.changeView(view);
        });
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
