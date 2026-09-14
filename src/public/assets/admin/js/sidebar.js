document.addEventListener('DOMContentLoaded', function () {
    // Set data-title attributes for tooltip functionality
    document.querySelectorAll('.sidebar .nav-item').forEach(item => {
        const navText = item.querySelector('.nav-text');
        if (navText) {
            item.setAttribute('data-title', navText.textContent.trim());
        }
    });

    // Sidebar toggle functionality
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    const sidebar = document.querySelector('.sidebar');
    const contentWrapper = document.querySelector('.content-wrapper');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');

    // Helper function to set cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Helper function to get cookie
    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // Function to toggle sidebar
    function toggleSidebar() {
        document.body.classList.toggle('sidebar-collapsed');

        const isCollapsed = document.body.classList.contains('sidebar-collapsed');

        // Update button icon
        if (sidebarCollapseBtn) {
            const icon = sidebarCollapseBtn.querySelector('i');
            if (icon) {
                if (isCollapsed) {
                    icon.className = 'fas fa-angle-double-right text-primary';
                } else {
                    icon.className = 'fas fa-angle-double-left text-primary';
                }
            }
        }

        // Save state to cookie
        setCookie('sidebarCollapsed', isCollapsed, 30);
    }

    // Initialize sidebar state from cookie
    const savedState = getCookie('sidebarCollapsed');
    if (savedState === 'true' && window.innerWidth >= 768) {
        document.body.classList.add('sidebar-collapsed');

        if (sidebarCollapseBtn) {
            const icon = sidebarCollapseBtn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-angle-double-right text-primary';
            }
        }
    } else {
        if (sidebarCollapseBtn) {
            const icon = sidebarCollapseBtn.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-angle-double-left text-primary';
            }
        }
    }

    // Event listeners
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', toggleSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            if (document.body.classList.contains('sidebar-collapsed')) {
                toggleSidebar();
            }
        });
    }
});