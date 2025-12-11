/**
 * Frontend Security Enhancement for USSIBATIK ABSEN
 * Comprehensive security measures for frontend-backend communication
 */

class FrontendSecurity {
    constructor() {
        this.init();
    }

    init() {
        this.setupCSRFProtection();
        this.setupRequestInterceptors();
        this.setupSecurityHeaders();
        this.setupInputSanitization();
        this.setupXSSProtection();
        this.monitorSecurity();
    }

    /**
     * Enhanced CSRF Protection
     */
    setupCSRFProtection() {
        // Get CSRF token from meta tag
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (token) {
            // Set default headers for all AJAX requests
            if (window.jQuery) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }

            // Set default headers for fetch API
            const originalFetch = window.fetch;
            window.fetch = function(url, options = {}) {
                options.headers = options.headers || {};
                options.headers['X-CSRF-TOKEN'] = token;
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
                
                return originalFetch(url, options);
            };

            // Auto-refresh CSRF token
            this.autoRefreshCSRF();
        }
    }

    /**
     * Auto-refresh CSRF token
     */
    autoRefreshCSRF() {
        setInterval(async () => {
            try {
                const response = await fetch('/csrf-token', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    
                    if (metaTag && data.csrf_token) {
                        metaTag.setAttribute('content', data.csrf_token);
                        
                        // Update jQuery CSRF token
                        if (window.jQuery) {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': data.csrf_token
                                }
                            });
                        }
                        
                        console.log('CSRF token refreshed successfully');
                    }
                }
            } catch (error) {
                console.warn('CSRF token refresh failed:', error);
            }
        }, 25 * 60 * 1000); // Every 25 minutes
    }

    /**
     * Setup request interceptors for security
     */
    setupRequestInterceptors() {
        // Intercept all form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Add security headers to forms
            if (!form.querySelector('input[name="_token"]')) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = csrfToken;
                    form.appendChild(tokenInput);
                }
            }

            // Sanitize form data
            this.sanitizeFormData(form);
        });

        // Monitor suspicious requests
        this.monitorRequests();
    }

    /**
     * Setup security headers
     */
    setupSecurityHeaders() {
        // Add security meta tags if not present
        const securityMetas = [
            { name: 'referrer', content: 'strict-origin-when-cross-origin' },
            { 'http-equiv': 'X-Content-Type-Options', content: 'nosniff' },
            { 'http-equiv': 'X-Frame-Options', content: 'SAMEORIGIN' }
        ];

        securityMetas.forEach(meta => {
            if (!document.querySelector(`meta[name="${meta.name}"], meta[http-equiv="${meta['http-equiv']}"]`)) {
                const metaTag = document.createElement('meta');
                if (meta.name) metaTag.name = meta.name;
                if (meta['http-equiv']) metaTag.httpEquiv = meta['http-equiv'];
                metaTag.content = meta.content;
                document.head.appendChild(metaTag);
            }
        });
    }

    /**
     * Input sanitization
     */
    setupInputSanitization() {
        // Sanitize all text inputs on blur
        document.addEventListener('blur', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                e.target.value = this.sanitizeInput(e.target.value);
            }
        }, true);
    }

    /**
     * Sanitize input value
     */
    sanitizeInput(value) {
        if (typeof value !== 'string') return value;
        
        // Remove potentially dangerous characters
        return value
            .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '') // Remove script tags
            .replace(/javascript:/gi, '') // Remove javascript: protocol
            .replace(/on\w+\s*=/gi, '') // Remove event handlers
            .replace(/[<>]/g, (match) => ({ '<': '&lt;', '>': '&gt;' }[match])); // Escape HTML
    }

    /**
     * Sanitize form data
     */
    sanitizeFormData(form) {
        const inputs = form.querySelectorAll('input[type="text"], input[type="email"], textarea');
        inputs.forEach(input => {
            input.value = this.sanitizeInput(input.value);
        });
    }

    /**
     * XSS Protection
     */
    setupXSSProtection() {
        // Monitor for XSS attempts
        const originalInnerHTML = Object.getOwnPropertyDescriptor(Element.prototype, 'innerHTML');
        
        Object.defineProperty(Element.prototype, 'innerHTML', {
            set: function(value) {
                if (typeof value === 'string' && this.isXSSAttempt(value)) {
                    console.warn('Potential XSS attempt blocked:', value);
                    return;
                }
                originalInnerHTML.set.call(this, value);
            },
            get: originalInnerHTML.get
        });
    }

    /**
     * Check for XSS attempt
     */
    isXSSAttempt(value) {
        const xssPatterns = [
            /<script/i,
            /javascript:/i,
            /on\w+\s*=/i,
            /eval\s*\(/i,
            /expression\s*\(/i
        ];

        return xssPatterns.some(pattern => pattern.test(value));
    }

    /**
     * Monitor requests for security
     */
    monitorRequests() {
        let requestCount = 0;
        let suspiciousRequests = 0;

        // Monitor request frequency
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            requestCount++;
            
            // Check for suspicious patterns
            if (url.includes('admin') && !url.includes(window.location.origin)) {
                suspiciousRequests++;
                console.warn('Suspicious external admin request:', url);
            }

            // Rate limiting check
            if (requestCount > 100) { // More than 100 requests
                console.warn('High request frequency detected');
                requestCount = 0; // Reset counter
            }

            return originalFetch(url, options);
        };

        // Reset counters periodically
        setInterval(() => {
            requestCount = 0;
            suspiciousRequests = 0;
        }, 60000); // Every minute
    }

    /**
     * Security monitoring
     */
    monitorSecurity() {
        // Monitor for suspicious activity
        let clickCount = 0;
        let keyCount = 0;

        document.addEventListener('click', () => {
            clickCount++;
            if (clickCount > 1000) { // Suspicious clicking
                console.warn('Suspicious clicking activity detected');
                clickCount = 0;
            }
        });

        document.addEventListener('keydown', () => {
            keyCount++;
            if (keyCount > 5000) { // Suspicious typing
                console.warn('Suspicious keyboard activity detected');
                keyCount = 0;
            }
        });

        // Reset counters
        setInterval(() => {
            clickCount = 0;
            keyCount = 0;
        }, 300000); // Every 5 minutes

        // Monitor console access (basic protection)
        let devtools = false;
        setInterval(() => {
            if (window.outerHeight - window.innerHeight > 200 || 
                window.outerWidth - window.innerWidth > 200) {
                if (!devtools) {
                    devtools = true;
                    console.warn('Developer tools detected');
                }
            } else {
                devtools = false;
            }
        }, 1000);
    }

    /**
     * Secure local storage
     */
    secureStorage(key, value) {
        if (value === undefined) {
            // Get value
            const stored = localStorage.getItem(key);
            if (stored) {
                try {
                    return JSON.parse(atob(stored));
                } catch (e) {
                    return null;
                }
            }
            return null;
        } else {
            // Set value (encoded)
            localStorage.setItem(key, btoa(JSON.stringify(value)));
        }
    }

    /**
     * Clear sensitive data on page unload
     */
    clearSensitiveData() {
        window.addEventListener('beforeunload', () => {
            // Clear sensitive form data
            document.querySelectorAll('input[type="password"]').forEach(input => {
                input.value = '';
            });

            // Clear temporary storage
            sessionStorage.clear();
        });
    }
}

// Initialize frontend security
document.addEventListener('DOMContentLoaded', () => {
    window.frontendSecurity = new FrontendSecurity();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FrontendSecurity;
}