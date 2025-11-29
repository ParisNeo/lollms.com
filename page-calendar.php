<?php
/**
 * Template Name: App Calendar
 */
if ( !is_user_logged_in() ) { wp_redirect(wp_login_url(get_permalink())); exit; }
get_header(); 
?>

<main class="nexus-container">
    <div class="app-container">
        
        <!-- HEADER -->
        <div style="text-align:center; margin-bottom:20px;">
            <h2 style="margin:0;">📅 Calendar</h2>
            <p style="color:var(--text-dim); margin:0; font-size:0.9rem;">Agenda & To-Dos</p>
        </div>

        <!-- CALENDAR WIDGET -->
        <div class="calendar-wrapper">
            <div class="calendar-header">
                <button class="cal-nav-btn" onclick="changeMonth(-1)">◀</button>
                <span class="month-label" id="cal-month-label">Loading...</span>
                <button class="cal-nav-btn" onclick="changeMonth(1)">▶</button>
            </div>
            
            <!-- Days of Week -->
            <div class="calendar-grid" style="margin-bottom:5px;">
                <div class="cal-day-header">Sun</div>
                <div class="cal-day-header">Mon</div>
                <div class="cal-day-header">Tue</div>
                <div class="cal-day-header">Wed</div>
                <div class="cal-day-header">Thu</div>
                <div class="cal-day-header">Fri</div>
                <div class="cal-day-header">Sat</div>
            </div>

            <!-- Dates Grid -->
            <div id="calendar-grid" class="calendar-grid">
                <!-- JS Injects here -->
            </div>
        </div>

        <!-- INPUT GROUP -->
        <div class="input-group">
            <div class="input-row">
                <input type="text" id="task-name" class="app-input" placeholder="New Task...">
            </div>
            <div class="input-row">
                <input type="date" id="task-date" class="app-input date" placeholder="Due Date">
                <button class="btn-add" onclick="addTask()">Add</button>
            </div>
        </div>

        <!-- LISTS -->
        <div style="margin-bottom:10px; font-size:0.85rem; color:var(--text-dim); text-transform:uppercase;">Pending</div>
        <ul id="todo-list" class="item-list" style="padding-bottom: 20px;"></ul>
        
        <div style="margin-top:20px; margin-bottom:10px; font-size:0.85rem; color:var(--text-dim); text-transform:uppercase;">Completed</div>
        <ul id="done-list" class="item-list" style="padding-bottom: 20px; opacity:0.6;"></ul>

    </div>
</main>

<script>
const apiRoot = "<?php echo esc_url_raw(rest_url('lollms/v1/')); ?>";
const nonce = "<?php echo wp_create_nonce('wp_rest'); ?>";

// State
let currentDate = new Date();
let activeTasks = []; // Store fetched tasks to map to calendar

document.addEventListener('DOMContentLoaded', () => {
    // Set today as default in input
    document.getElementById('task-date').valueAsDate = new Date();
    loadTasks();
    
    document.getElementById('task-name').addEventListener('keypress', (e) => { if (e.key === 'Enter') addTask(); });
});

// --- CALENDAR LOGIC ---
function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update Header
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    document.getElementById('cal-month-label').innerText = `${monthNames[month]} ${year}`;

    const grid = document.getElementById('calendar-grid');
    grid.innerHTML = '';

    // Calculate Days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();

    // Empty slots for previous month
    for (let i = 0; i < firstDay; i++) {
        const div = document.createElement('div');
        div.className = 'cal-day empty';
        grid.appendChild(div);
    }

    // Days
    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const div = document.createElement('div');
        div.className = 'cal-day';
        div.innerText = d;
        
        // Highlight Today
        if (d === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
            div.classList.add('today');
        }

        // Check if selected (matches input)
        const inputVal = document.getElementById('task-date').value;
        if (inputVal === dateStr) {
            div.classList.add('selected');
        }

        // Check for Tasks (Dot Indicator)
        const hasTask = activeTasks.some(t => t.due_date && t.due_date.startsWith(dateStr));
        if (hasTask) {
            div.classList.add('has-task');
            div.innerHTML += '<div class="task-dot"></div>';
        }

        // Click Event
        div.onclick = () => {
            document.getElementById('task-date').value = dateStr;
            renderCalendar(); // Re-render to update 'selected' class
        };

        grid.appendChild(div);
    }
}

// --- DATA LOGIC ---
async function loadTasks() {
    document.getElementById('todo-list').innerHTML = '<div style="padding:20px; text-align:center; color:#666;">Loading...</div>';
    
    // Get Active Tasks
    const res1 = await fetch(`${apiRoot}items/todo`, { headers: { 'X-WP-Nonce': nonce } });
    const todos = await res1.json();
    activeTasks = todos; // Update global state for calendar
    
    // Get Completed
    const res2 = await fetch(`${apiRoot}suggestions`, { headers: { 'X-WP-Nonce': nonce } });
    const done = await res2.json();
    
    // Render Lists
    renderList(todos, 'todo-list', false);
    renderList(done.filter(i => i.list_type === 'todo'), 'done-list', true);
    
    // Render Calendar (Now that we have tasks)
    renderCalendar();
}

function renderList(items, containerId, isDone) {
    const list = document.getElementById(containerId);
    list.innerHTML = '';
    
    if(items.length === 0) {
        list.innerHTML = `<div style="padding:10px; text-align:center; font-size:0.85rem; color:rgba(255,255,255,0.1);">${isDone ? 'No history' : 'No pending tasks'}</div>`;
        return;
    }

    items.forEach(item => {
        const li = document.createElement('li');
        li.className = 'todo-item';
        li.id = `task-${item.id}`;
        
        // Format Date
        let dateHtml = '';
        if(item.due_date) {
            const d = new Date(item.due_date);
            // Simple date formatting
            const dateStr = d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            dateHtml = `<span class="item-date" style="color:var(--accent);">📅 ${dateStr}</span>`;
        }

        if(isDone) {
            li.innerHTML = `
                <div class="todo-checkbox" style="background:var(--primary);" onclick="restoreTask(${item.id})"></div>
                <div style="flex-grow:1; text-decoration:line-through; color:var(--text-dim);">
                    ${item.item_name}
                </div>
                <button class="btn-icon" style="color:var(--danger);" onclick="deleteTask(${item.id})">🗑</button>
            `;
        } else {
            li.innerHTML = `
                <div class="todo-checkbox" onclick="completeTask(${item.id})"></div>
                <div class="item-details">
                    <span class="item-name">${item.item_name}</span>
                    ${dateHtml}
                </div>
                <input type="date" class="inline-input date" value="${item.due_date ? item.due_date.split(' ')[0] : ''}" 
                       onchange="updateTask(${item.id}, 'due_date', this.value)">
            `;
        }
        list.appendChild(li);
    });
}

async function addTask() {
    const nameInput = document.getElementById('task-name');
    const dateInput = document.getElementById('task-date');
    if(!nameInput.value) return;

    await fetch(`${apiRoot}add`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({
            name: nameInput.value,
            type: 'todo',
            date: dateInput.value
        })
    });
    nameInput.value = ''; 
    // Don't reset date, users often add multiple tasks for same day
    loadTasks();
}

async function updateTask(id, field, value) {
    await fetch(`${apiRoot}update`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({ id: id, field: field, value: value })
    });
    // Reload to refresh calendar dots
    loadTasks();
}

async function completeTask(id) {
    const el = document.getElementById(`task-${id}`);
    el.classList.add('slide-out');
    await fetch(`${apiRoot}toggle`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({ id: id })
    });
    setTimeout(() => loadTasks(), 300);
}

async function restoreTask(id) {
    const el = document.getElementById(`task-${id}`);
    const name = el.querySelector('div[style]').innerText.trim();
    
    await fetch(`${apiRoot}add`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({ name: name, type: 'todo' })
    });
    loadTasks();
}

async function deleteTask(id) {
    if(!confirm('Permanently delete task?')) return;
    const el = document.getElementById(`task-${id}`);
    el.classList.add('slide-out'); setTimeout(() => el.remove(), 300);
    await fetch(`${apiRoot}delete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        body: JSON.stringify({ id: id })
    });
}
</script>

<?php get_footer(); ?>
