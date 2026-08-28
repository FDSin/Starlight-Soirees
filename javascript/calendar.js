document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        var calendar = new FullCalendar.Calendar(calendarEl, {
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

            loading: function(isloading) {
                if (!isloading){
                    console.log('Calendar loaded successfully!');
                }
            }
        });
        calendar.render();
    } else {
        console.error("Element #calendar not found in DOM");
    }
});