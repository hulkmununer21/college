/**
 * Main JavaScript File
 * Tertiary School Management System
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.alert');
    if (flashMessages.length > 0) {
        flashMessages.forEach(function(message) {
            setTimeout(function() {
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 300);
            }, 5000);
        });
    }

    // Form validation helper
    window.validateForm = function(formId) {
        const form = document.getElementById(formId);
        if (!form) return false;

        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(function(input) {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    };

    // AJAX helper function
    window.ajax = function(url, method, data, callback) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    callback(null, response);
                } catch (e) {
                    callback(e, null);
                }
            } else {
                callback(new Error('Request failed'), null);
            }
        };

        xhr.onerror = function() {
            callback(new Error('Network error'), null);
        };

        if (method === 'POST' || method === 'PUT') {
            xhr.send(JSON.stringify(data));
        } else {
            xhr.send();
        }
    };

    // Show loading spinner
    window.showLoading = function() {
        // Implement loading spinner if needed
        console.log('Loading...');
    };

    // Hide loading spinner
    window.hideLoading = function() {
        console.log('Loading complete');
    };

    // Confirmation dialog helper
    window.confirm = function(message, callback) {
        if (window.confirm(message)) {
            callback(true);
        } else {
            callback(false);
        }
    };

    console.log('TSMS JavaScript loaded successfully');
});
