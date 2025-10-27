const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const content = document.getElementById('content');
const topNavbar = document.querySelector('.top-navbar');

document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    initializeTooltips();
});

function initializeSidebar() {
    if (!sidebarToggle || !sidebar || !sidebarOverlay || !content || !topNavbar) {
        console.log('Sidebar elements not found');
        return;
    }

    function toggleSidebar() {
        if (window.innerWidth < 992) {
            // Mobile behavior: show/hide with overlay
            sidebar.classList.toggle('mobile-show');
            sidebarOverlay.classList.toggle('mobile-show');
            document.body.classList.toggle('sidebar-mobile-open');
        } else {
            // Desktop behavior: collapse/expand
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            if (topNavbar) {
                topNavbar.classList.toggle('collapsed');
            }
        }
        
        // Save sidebar state to localStorage
        saveSidebarState();
    }

    // Event listener for toggle button
    sidebarToggle.addEventListener('click', toggleSidebar);

    // Close mobile sidebar when clicking overlay
    sidebarOverlay.addEventListener('click', function () {
        sidebar.classList.remove('mobile-show');
        sidebarOverlay.classList.remove('mobile-show');
        document.body.classList.remove('sidebar-mobile-open');
    });

    // Close mobile sidebar
    const sidebarLinks = document.querySelectorAll('#sidebar ul li a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('mobile-show');
                sidebarOverlay.classList.remove('mobile-show');
                document.body.classList.remove('sidebar-mobile-open');
            }
        });
    });

    // Handle window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {

            sidebar.classList.remove('mobile-show');
            sidebarOverlay.classList.remove('mobile-show');
            document.body.classList.remove('sidebar-mobile-open');
        } else {

            sidebar.classList.remove('collapsed');
            content.classList.remove('collapsed');
            if (topNavbar) {
                topNavbar.classList.remove('collapsed');
            }
        }
    });

    // Load saved sidebar state
    loadSidebarState();
}

// Save sidebar state to localStorage
function saveSidebarState() {
    if (window.innerWidth >= 992) {
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }
}

// Load sidebar state from localStorage
function loadSidebarState() {
    if (window.innerWidth >= 992) {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            content.classList.add('collapsed');
            if (topNavbar) {
                topNavbar.classList.add('collapsed');
            }
        }
    }
}

// Initialize Bootstrap tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        try {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        } catch (error) {
            console.error('Error initializing tooltip:', error);
            return null;
        }
    });
}
