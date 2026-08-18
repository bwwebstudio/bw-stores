/**
 * BW Store — App JavaScript
 * 
 * CSRF token injection, flash message dismissal, mobile sidebar toggle.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ─────────────────────────────────────────
    // Auto-dismiss flash messages after 5 seconds
    // ─────────────────────────────────────────
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // ─────────────────────────────────────────
    // Mobile Sidebar Toggle
    // ─────────────────────────────────────────
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('dashboardSidebar');
    var overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        });

        if (overlay) {
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            });
        }
    }

    // ─────────────────────────────────────────
    // Password Toggle Visibility
    // ─────────────────────────────────────────
    document.querySelectorAll('.toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.closest('.password-toggle').querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '&#x1F441;';
            } else {
                input.type = 'password';
                this.innerHTML = '&#x1F441;&#x200D;&#x1F5E8;';
            }
        });
    });

    // ─────────────────────────────────────────
    // CSRF Token for AJAX/Fetch Requests
    // ─────────────────────────────────────────
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        var csrfToken = csrfMeta.getAttribute('content');

        // Override fetch to include CSRF token
        var originalFetch = window.fetch;
        window.fetch = function (url, options) {
            options = options || {};
            if (options.method && options.method.toUpperCase() !== 'GET') {
                options.headers = options.headers || {};
                if (!(options.headers instanceof Headers)) {
                    options.headers['X-CSRF-TOKEN'] = csrfToken;
                }
            }
            return originalFetch.call(this, url, options);
        };
    }

    // ─────────────────────────────────────────
    // Form Submission Loading State
    // ─────────────────────────────────────────
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.disabled) {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Please wait...';
            }
        });
    });

    // ─────────────────────────────────────────
    // Confirm Delete Actions
    // ─────────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var message = this.dataset.confirm || 'Are you sure you want to proceed?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});
