<!-- APP: CALENDAR -->
<div id="app-calendar" class="app-module" style="display:none; height:100%;">
    <div id="calendar-container"></div>
</div>

<script>
function loadCalendar() { 
    const calendarEl=document.getElementById('calendar-container'); 
    if(calendarInstance) calendarInstance.destroy(); 
    
    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {left:'prev,next', center:'title', right:'dayGridMonth,listWeek'},
        height: 'auto',
        contentHeight: 500,
        events: async function(info, successCallback){
            const res = await fetch(`${apiRoot}items/calendar`, {headers:{'X-WP-Nonce':nonce}});
            const data = await res.json();
            successCallback(data.map(item=>({
                id: item.id,
                title: item.item_name,
                start: item.due_date,
                end: item.end_date,
                extendedProps: item
            })));
        },
        eventClick: function(info){
            openEditor('calendar', Object.assign({
                id: info.event.id,
                item_name: info.event.title,
                due_date: info.event.startStr
            }, info.event.extendedProps));
        },
        dateClick: function(info){
            openEditor('calendar');
            setTimeout(()=>{ document.getElementById('edit-start').value = info.dateStr+'T09:00'; }, 50);
        }
    });
    calendarInstance.render(); 
}
</script>
