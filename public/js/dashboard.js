/**
 * Dashboard JavaScript
 * Tertiary School Management System
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Mobile sidebar toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        mainContent.addEventListener('click', function() {
            if (window.innerWidth <= 1024 && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Alert close buttons
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            this.parentElement.style.opacity = '0';
            setTimeout(() => {
                this.parentElement.remove();
            }, 300);
        });
    });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Confirm logout
    const logoutLinks = document.querySelectorAll('a[href*="logout"]');
    logoutLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to logout?')) {
                e.preventDefault();
            }
        });
    });

    // Table row click (if needed in future)
    const tableRows = document.querySelectorAll('.data-table tbody tr');
    tableRows.forEach(function(row) {
        const clickableElements = row.querySelectorAll('a, button');
        if (clickableElements.length > 0) {
            row.style.cursor = 'pointer';
        }
    });

    // Stat card click animations
    const clickableCards = document.querySelectorAll('.stat-card.clickable');
    clickableCards.forEach(function(card) {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });

    console.log('Dashboard JavaScript loaded successfully');
});

// Session timeout warning (optional)
function checkSessionTimeout() {
    const loginTime = document.documentElement.dataset.loginTime || null;
    const sessionLifetime = document.documentElement.dataset.sessionLifetime;
    
    if (loginTime) {
        const currentTime = Math.floor(Date.now() / 1000);
        const timeElapsed = currentTime - loginTime;
        const timeRemaining = sessionLifetime - timeElapsed;
        
        // Warn 5 minutes before timeout
        if (timeRemaining <= 300 && timeRemaining > 0) {
            console.log('Session will expire in ' + Math.floor(timeRemaining / 60) + ' minutes');
            // Show warning notification (implement as needed)
        }
        
        // Redirect to login if session expired
        if (timeRemaining <= 0) {
            window.location.href = '<?= BASE_URL ?>/auth/login';
        }
    }
}

// Check session every minute
setInterval(checkSessionTimeout, 60000);
