/**
 * JEMS Task Manager - Optimized JavaScript
 * Minimal, performance-focused client-side functionality
 */

// Performance optimizations
(function() {
    'use strict';

    // Debounce function for performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Optimized sidebar functionality
    window.toggleSidebar = function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        if (sidebar && mainContent) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage for persistence
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    };

    window.toggleMobileSidebar = function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('mobile-open');
        }
    };

    // Optimized search functionality with debouncing
    function initializeSearch() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            const debouncedSearch = debounce(function() {
                // Auto-submit search form after user stops typing
                const form = searchInput.closest('form');
                if (form) {
                    form.submit();
                }
            }, 500);

            searchInput.addEventListener('input', debouncedSearch);
        }
    }

    // Lazy loading for images
    function initializeLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // Optimize form submissions
    function initializeFormOptimizations() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                // Disable submit button to prevent double submissions
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
            });
        });
    }

    // Performance monitoring
    function logPerformanceMetrics() {
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    if (perfData) {
                        console.log('Page Load Performance:', {
                            'DNS Lookup': perfData.domainLookupEnd - perfData.domainLookupStart,
                            'TCP Connection': perfData.connectEnd - perfData.connectStart,
                            'Request': perfData.responseStart - perfData.requestStart,
                            'Response': perfData.responseEnd - perfData.responseStart,
                            'DOM Processing': perfData.domContentLoadedEventEnd - perfData.responseEnd,
                            'Total Load Time': perfData.loadEventEnd - perfData.navigationStart
                        });
                    }
                }, 0);
            });
        }
    }

    // Initialize all optimizations when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Restore sidebar state
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            document.getElementById('sidebar')?.classList.add('collapsed');
            document.getElementById('mainContent')?.classList.add('expanded');
        }
        
        // Add fade-in animation to main content
        document.getElementById('mainContent')?.classList.add('fade-in');

        // Initialize features
        initializeSearch();
        initializeLazyLoading();
        initializeFormOptimizations();
        
        // Performance monitoring (only in development)
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            logPerformanceMetrics();
        }

        // Auto-dismiss flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (window.bootstrap && window.bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    });

    // Service Worker registration for caching (if available)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                })
                .catch(function(err) {
                    console.log('ServiceWorker registration failed');
                });
        });
    }

})();

// Export for use in other scripts
window.JEMS = {
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};
