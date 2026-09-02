document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            aspectRatio: 1.8,
            contentHeight: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth, timeGridWeek, listMonth'
            },
            events: 'get_events.php',
            eventTextColor: '#ffffff',
            eventClick: function(info) {
                alert('Event Details: ' + info.event.title);
            },
            eventSourceFailure: function () {
                console.error('Calendar events could not be loaded.');
            }
        });

        calendar.render();
    } else {
        console.error('Element #calendar not found in DOM');
    }
});
