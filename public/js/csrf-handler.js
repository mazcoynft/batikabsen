/**
 * CSRF Token Handler for WebView Apps
 * Handles automatic token refresh and retry for failed requests
 */

class CSRFHandler {
    constructor() {
        this.init();
    }

    init() {
        // Auto-refresh token every 30 minutes
        setInterval(() => {
            this.refreshToken();
        }, 30 * 60 * 1000);

        // Handle CSRF errors in AJAX requests
        this.setupAjaxErrorHandler();
        
        // Handle form submissions
        this.setupFormHandler();
        
        // Refresh token on page focus (when app comes back from background)
        this.setupFocusHandler();
    }

    /**
     * Get current CSRF token
     */
    getToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
               document.querySelector('input[name="_token"]')?.value;
    }

    /**
     * Update CSRF token in all forms and meta tags
     */
    updateToken(newToken) {
        // Update meta tag
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            metaTag.setAttribute('content', newToken);
        }

        // Update all hidden token inputs
        document.querySelectorAll('input[name="_token"]').forEach(input => {
            input.value = newToken;
        });

        // Update axios default header if exists
        if (window.axios) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = newToken;
        }

        // Update jQuery AJAX setup if exists
        if (window.$ && $.ajaxSetup) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': newToken
                }
            });
        }
    }

    /**
     * Refresh CSRF token from server
     */
    async refreshToken() {
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
                this.updateToken(data.csrf_token);
                console.log('CSRF token refreshed');
            }
        } catch (error) {
            console.warn('Failed to refresh CSRF token:', error);
        }
    }

    /**
     * Setup AJAX error handler for automatic retry
     */
    setupAjaxErrorHandler() {
        // jQuery AJAX error handler
        if (window.$ && $.ajaxSetup) {
            $(document).ajaxError((event, xhr, settings, thrownError) => {
                if (xhr.status === 419) { // CSRF token mismatch
                    this.handleCSRFError(xhr, settings);
                }
            });
        }

        // Axios interceptor
        if (window.axios) {
            axios.interceptors.response.use(
                response => response,
                error => {
                    if (error.response?.status === 419) {
                        return this.handleAxiosCSRFError(error);
                    }
                    return Promise.reject(error);
                }
            );
        }
    }

    /**
     * Handle CSRF error in jQuery AJAX
     */
    async handleCSRFError(xhr, settings) {
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.csrf_token) {
                this.updateToken(response.csrf_token);
                
                // Retry the original request
                setTimeout(() => {
                    $.ajax(settings);
                }, 500);
            }
        } catch (error) {
            console.error('Failed to handle CSRF error:', error);
            this.showCSRFErrorMessage();
        }
    }

    /**
     * Handle CSRF error in Axios
     */
    async handleAxiosCSRFError(error) {
        try {
            const response = error.response.data;
            if (response.csrf_token) {
                this.updateToken(response.csrf_token);
                
                // Retry the original request
                const originalRequest = error.config;
                originalRequest.headers['X-CSRF-TOKEN'] = response.csrf_token;
                
                return axios(originalRequest);
            }
        } catch (retryError) {
            console.error('Failed to retry request:', retryError);
            this.showCSRFErrorMessage();
        }
        
        return Promise.reject(error);
    }

    /**
     * Setup form submission handler
     */
    setupFormHandler() {
        document.addEventListener('submit', async (event) => {
            const form = event.target;
            const tokenInput = form.querySelector('input[name="_token"]');
            
            if (tokenInput && !tokenInput.value) {
                event.preventDefault();
                await this.refreshToken();
                form.submit();
            }
        });
    }

    /**
     * Setup focus handler for WebView apps
     */
    setupFocusHandler() {
        let isHidden = false;
        
        // Handle visibility change (app goes to background/foreground)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                isHidden = true;
            } else if (isHidden) {
                // App came back from background, refresh token
                this.refreshToken();
                isHidden = false;
            }
        });

        // Handle window focus
        window.addEventListener('focus', () => {
            if (isHidden) {
                this.refreshToken();
                isHidden = false;
            }
        });
    }

    /**
     * Show user-friendly error message
     */
    showCSRFErrorMessage() {
        // Try to show a toast notification if available
        if (window.Swal) {
            Swal.fire({
                title: 'Session Expired',
                text: 'Please refresh the page and try again.',
                icon: 'warning',
                confirmButtonText: 'Refresh Page',
                allowOutsideClick: false
            }).then(() => {
                window.location.reload();
            });
        } else if (window.alert) {
            alert('Session expired. Please refresh the page and try again.');
            window.location.reload();
        }
    }
}

// Initialize CSRF handler when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.csrfHandler = new CSRFHandler();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CSRFHandler;
}