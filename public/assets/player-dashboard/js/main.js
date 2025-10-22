// Dropdown toggle with arrow rotation
document.querySelectorAll('.dropdown-toggle').forEach(item => {
    item.addEventListener('click', () => {
        let parent = item.parentElement;
        parent.classList.toggle('open');
    });
});




// Calendar Start
const events = {};
// Merge server-provided events if available
if (window.calendarEvents && typeof window.calendarEvents === 'object') {
    for (const [dateKey, list] of Object.entries(window.calendarEvents)) {
        events[dateKey] = (events[dateKey] || []).concat(list);
    }
}

const monthYear = document.getElementById("monthYear");
const calendarDays = document.getElementById("calendarDays");
let currentDate = new Date();
function renderCalendar(date) {
    if (!calendarDays || !monthYear) return; // guard when calendar is not present on page
    calendarDays.innerHTML = "";
    const year = date.getFullYear();
    const month = date.getMonth();
    monthYear.textContent = date.toLocaleDateString("en-US", { month: "long", year: "numeric" });
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();
    const prevLastDate = new Date(year, month, 0).getDate();
    let days = [];
    // Fill empty days from previous month
    for (let i = 1; i < (firstDay === 0 ? 7 : firstDay); i++) {
        days.push({ day: prevLastDate - firstDay + i + 1, prev: true });
    }
    // Current month days
    for (let i = 1; i <= lastDate; i++) {
        const dateKey = `${year}-${String(month + 1).padStart(2, "0")}-${String(i).padStart(2, "0")}`;
        days.push({ day: i, dateKey });
    }
    // Render days
    days.forEach(d => {
        const div = document.createElement("div");
        div.classList.add("day");
        div.innerHTML = `<div class="date">${d.day}</div>`;
        if (d.dateKey && events[d.dateKey]) {
            events[d.dateKey].forEach(ev => {
                const evEl = document.createElement('div');
                evEl.className = `event ${ev.color || ''}`.trim();
                evEl.textContent = `${ev.time ? ev.time + ' ' : ''}${ev.text || ''}`.trim();
                // attach details for modal
                ['title','text','time','type','location','url','description','date','id','venue_name','lat','lng','resource_type','resource_id'].forEach(k => {
                    if (ev[k] != null) evEl.dataset[k] = String(ev[k]);
                });
                evEl.addEventListener('click', () => {
                    if (window.openCalendarEvent) window.openCalendarEvent(evEl.dataset);
                });
                div.appendChild(evEl);
            });
        }
        calendarDays.appendChild(div);
    });
}
function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
}
function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
}
// Only render calendar on pages that include the calendar elements
if (calendarDays && monthYear) {
    renderCalendar(currentDate);
}



// Calendar Tabs Start
const tabs = document.querySelectorAll(".tab");

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
    });
});




// Video thumbnail Start
const videoCard = document.getElementById("videoCard");
const videoFrame = document.getElementById("videoFrame");
if (videoCard && videoFrame) {
    videoCard.addEventListener("click", () => {
        const videoId = "GyO1MtLhyt0";
        videoFrame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        videoFrame.style.display = "block";
        const thumb = videoCard.querySelector(".thumbnail");
        const playBtn = videoCard.querySelector(".play-btn");
        if (thumb) thumb.style.display = "none";
        if (playBtn) playBtn.style.display = "none";
    });
}




// Sidebar Toggle Start
const hamburger = document.getElementById("hamburger");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");

function toggleSidebar() {
    if (!sidebar || !overlay || !hamburger) return;
    sidebar.classList.toggle("show");
    overlay.classList.toggle("show");
    if (hamburger.classList.contains("fa-bars")) {
        hamburger.classList.remove("fa-bars");
        hamburger.classList.add("fa-times");
    } else {
        hamburger.classList.remove("fa-times");
        hamburger.classList.add("fa-bars");
    }
}

if (hamburger) hamburger.addEventListener("click", toggleSidebar);
if (overlay) overlay.addEventListener("click", toggleSidebar);



//Awards Open modal Start
const openModalFunction = (imgUrl, tagValue, paragraph = "") => {
    const modal = document.getElementById("myModal-a");
    modal.style.display = "flex";
    const img = document.getElementById("modal-img");
    img.src = imgUrl;
    const tag = document.getElementById("modal-tag");
    tag.innerHTML = tagValue;
    const para = document.getElementById("desc-para");
    para.innerHTML = paragraph;

}

const closeBtn = document.querySelector(".close-a");
if (closeBtn) {
    closeBtn.onclick = function () {
        const modal = document.getElementById("myModal-a");
        if (modal) modal.style.display = "none";
    }
}


// Li List Button Start
const list = document.querySelectorAll("#awardList li");
const btn = document.getElementById("showMoreBtn");
if (btn && list.length) {
    list.forEach((item, index) => {
        if (index >= 6) item.classList.add("hidden");
    });
    btn.addEventListener("click", () => {
        const hiddenItems = document.querySelectorAll("#awardList li.hidden");
        if (hiddenItems.length > 0) {
            hiddenItems.forEach(item => item.classList.remove("hidden"));
            btn.textContent = "Show Less";
        } else {
            list.forEach((item, index) => {
                if (index >= 6) item.classList.add("hidden");
            });
            btn.textContent = "Show More";
        }
    });
}









// Theme Toggle Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Check for saved theme preference or default to 'light'
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);

    // Create theme toggle button if it doesn't exist
    if (!document.querySelector('.theme-toggle')) {
        const themeToggle = document.createElement('button');
        themeToggle.className = 'theme-toggle';
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        document.body.appendChild(themeToggle);

        // Update icon based on current theme
        updateThemeIcon(currentTheme, themeToggle);

        // Add click event listener
        themeToggle.addEventListener('click', function () {
            const theme = document.documentElement.getAttribute('data-theme');
            const newTheme = theme === 'light' ? 'dark' : 'light';

            // Update theme
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update icon
            updateThemeIcon(newTheme, themeToggle);
        });
    }

    function updateThemeIcon(theme, button) {
        const icon = button.querySelector('i');
        if (theme === 'dark') {
            icon.className = 'fas fa-sun';
        } else {
            icon.className = 'fas fa-moon';
        }
    }

    // Add Font Awesome for icons if not already included
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const fontAwesome = document.createElement('link');
        fontAwesome.rel = 'stylesheet';
        fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css';
        document.head.appendChild(fontAwesome);
    }
});


















