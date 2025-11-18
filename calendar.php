<?php
require_once 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<link rel="stylesheet" href="./css/calendar.css">

<div class="container mt-5">
    <h1 class="text-center mb-4">Satsang Calendar</h1>
    <div id="calendar"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
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
            // Show tooltip with details
            var tooltip = document.createElement('div');
            tooltip.className = 'fc-tooltip';
            tooltip.innerHTML = '<strong>' + info.event.title + '</strong><br>' +
                                'Venue: ' + (info.event.extendedProps.venue || 'N/A') + '<br>' +
                                'Description: ' + (info.event.extendedProps.description || 'N/A');
            tooltip.style.position = 'absolute';
            tooltip.style.zIndex = '9999';
            tooltip.style.background = '#fff';
            tooltip.style.border = '1px solid #ccc';
            tooltip.style.padding = '5px';
            tooltip.style.borderRadius = '3px';
            tooltip.style.boxShadow = '0 2px 5px rgba(0,0,0,0.3)';
            document.body.appendChild(tooltip);

            var rect = info.el.getBoundingClientRect();
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';

            info.el.tooltip = tooltip;
        },
        eventMouseLeave: function(info) {
            if (info.el.tooltip) {
                document.body.removeChild(info.el.tooltip);
                info.el.tooltip = null;
            }
        }
    });
    calendar.render();
});
</script>

<?php
require_once 'includes/footer.php';
?>
