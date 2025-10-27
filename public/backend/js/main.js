// DOM Elements
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const content = document.getElementById('content');
const topNavbar = document.querySelector('.top-navbar');

// Single toggle function that handles both mobile and desktop
function toggleSidebar() {
    if (window.innerWidth < 992) {
        // Mobile behavior: show/hide with overlay
        sidebar.classList.toggle('mobile-show');
        sidebarOverlay.classList.toggle('mobile-show');
    } else {
        // Desktop behavior: collapse/expand
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('collapsed');
        topNavbar.classList.toggle('collapsed');
    }
}

// Event listener for toggle button
sidebarToggle.addEventListener('click', toggleSidebar);

// Close mobile sidebar when clicking overlay
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('mobile-show');
    sidebarOverlay.classList.remove('mobile-show');
});

// Close mobile sidebar when clicking on a link
const sidebarLinks = document.querySelectorAll('#sidebar ul li a');
sidebarLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
            sidebar.classList.remove('mobile-show');
            sidebarOverlay.classList.remove('mobile-show');
        }
    });
});

// Handle window resize
window.addEventListener('resize', function () {
    if (window.innerWidth >= 992) {
        // Remove mobile classes when resizing to desktop
        sidebar.classList.remove('mobile-show');
        sidebarOverlay.classList.remove('mobile-show');
    } else {
        // Remove desktop classes when resizing to mobile
        sidebar.classList.remove('collapsed');
        content.classList.remove('collapsed');
        topNavbar.classList.remove('collapsed');
    }
});

// Performance Chart
const performanceCtx = document.getElementById('performanceChart').getContext('2d');
new Chart(performanceCtx, {
    type: 'doughnut',
    data: {
        labels: ['Completed', 'In Progress', 'Not Started'],
        datasets: [{
            data: [65, 25, 10],
            backgroundColor: [
                '#11998e',
                '#d4af37',
                '#f5576c'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        },
        cutout: '70%'
    }
});