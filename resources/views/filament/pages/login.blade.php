<x-filament-panels::page.simple>
    {{-- CSRF Token for Filament --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Preload critical resources --}}
    <link rel="preload" href="{{ asset('images/login3d.png') }}" as="image">
    <link rel="preload" href="{{ asset('js/csrf-handler.js') }}" as="script">
    
    <style>
        /* Override semua style Filament */
        .fi-simple-page {
            all: unset !important;
            display: flex !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .fi-simple-main {
            all: unset !important;
            display: flex !important;
            width: 100% !important;
            height: 100% !important;
        }

        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 50%, #e9d5ff 100%) !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .login-container {
            display: flex !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .login-left {
            flex: 1 !important;
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%) !important;
            padding: 60px 50px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .welcome-content h1 {
            font-size: 48px !important;
            font-weight: 700 !important;
            color: #4f46e5 !important;
            margin-bottom: 16px !important;
            line-height: 1.2 !important;
        }

        .welcome-content p {
            font-size: 16px !important;
            color: #6b7280 !important;
            margin-bottom: 40px !important;
        }

        .illustration {
            margin-top: 30px !important;
            text-align: center !important;
        }

        .illustration img {
            max-width: 400px !important;
            height: auto !important;
        }

        .login-right {
            flex: 0 0 450px !important;
            background: white !important;
            padding: 60px 50px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        /* Hide Filament card */
        .fi-simple-page > div,
        .fi-simple-main > div {
            all: unset !important;
            display: contents !important;
        }

        /* Style Filament form */
        .fi-form {
            width: 100% !important;
        }

        .fi-input-wrp input {
            width: 100% !important;
            padding: 14px 16px !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0 !important;
            font-size: 15px !important;
            background: #f9fafb !important;
        }

        .fi-input-wrp input:focus {
            border-color: #4f46e5 !important;
            background: white !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }

        .fi-btn {
            width: 100% !important;
            padding: 16px !important;
            background: #4f46e5 !important;
            color: white !important;
            border: none !important;
            border-radius: 0 !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .fi-btn:hover {
            background: #4338ca !important;
        }

        .fi-fo-field-wrp {
            margin-bottom: 20px !important;
        }

        .fi-fo-field-wrp-label {
            display: block !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #374151 !important;
            margin-bottom: 8px !important;
        }

        .fi-btn {
            position: relative !important;
        }

        .fi-btn * {
            visibility: hidden !important;
        }

        .fi-btn::after {
            content: 'LOG IN' !important;
            visibility: visible !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
        }

        /* Hide Filament branding/logo di atas */
        .fi-simple-header,
        .fi-logo,
        header {
            display: none !important;
        }

        @media (max-width: 968px) {
            .login-left {
                display: none !important;
            }
            .login-right {
                flex: 1 !important;
            }
        }
    </style>

    <div class="login-container">
        <div class="login-left">
            <div class="welcome-content">
                <h1>Welcome to our<br>Community</h1>
                <p>A whole new productive journey<br>starts right here</p>
            </div>
            <div class="illustration">
                <img src="{{ asset('images/login3d.png') }}" alt="Illustration">
            </div>
        </div>

        <div class="login-right">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 28px; font-weight: 700; color: #1a202c; margin-bottom: 8px;">LOG IN</h2>
                <p style="font-size: 14px; color: #6b7280;">Welcome back! Please enter your details</p>
            </div>

            <x-filament-panels::form wire:submit="authenticate" id="filamentLoginForm">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
    </div>

    {{-- Enhanced JavaScript for Filament Admin --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // CSRF Token Management
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                // Update Livewire CSRF token
                if (window.Livewire) {
                    window.Livewire.hook('request', ({ options }) => {
                        options.headers = options.headers || {};
                        options.headers['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                    });
                }
            }

            // Auto-refresh CSRF token every 30 minutes
            setInterval(() => {
                fetch('/csrf-token')
                    .then(response => response.json())
                    .then(data => {
                        if (csrfToken && data.csrf_token) {
                            csrfToken.setAttribute('content', data.csrf_token);
                        }
                    })
                    .catch(error => console.warn('CSRF token refresh failed:', error));
            }, 30 * 60 * 1000);

            // Enhanced form handling
            const loginForm = document.getElementById('filamentLoginForm');
            if (loginForm) {
                // Add loading state
                loginForm.addEventListener('submit', function(e) {
                    const submitBtn = loginForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.7';
                        
                        // Re-enable after timeout (fallback)
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.style.opacity = '1';
                        }, 10000);
                    }
                });

                // Enter key support
                const inputs = loginForm.querySelectorAll('input');
                inputs.forEach((input, index) => {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            
                            const nextInput = inputs[index + 1];
                            if (nextInput) {
                                nextInput.focus();
                            } else {
                                // Submit form if last input
                                const submitBtn = loginForm.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.click();
                                }
                            }
                        }
                    });
                });
            }

            // Performance monitoring
            if (window.performance) {
                window.addEventListener('load', () => {
                    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                    console.log('Filament login page load time:', loadTime + 'ms');
                });
            }

            // Security: Prevent right-click and F12 in production
            if (window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                document.addEventListener('contextmenu', e => e.preventDefault());
                document.addEventListener('keydown', e => {
                    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                        e.preventDefault();
                    }
                });
            }
        });

        // Livewire optimization
        document.addEventListener('livewire:init', () => {
            // Optimize Livewire requests
            Livewire.hook('request', ({ options }) => {
                options.headers = options.headers || {};
                options.headers['X-Requested-With'] = 'XMLHttpRequest';
                options.headers['Cache-Control'] = 'no-cache';
            });

            // Handle Livewire errors
            Livewire.hook('request.exception', ({ status, content, preventDefault }) => {
                if (status === 419) { // CSRF token mismatch
                    preventDefault();
                    
                    // Refresh CSRF token and retry
                    fetch('/csrf-token')
                        .then(response => response.json())
                        .then(data => {
                            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                            if (csrfMeta && data.csrf_token) {
                                csrfMeta.setAttribute('content', data.csrf_token);
                                window.location.reload();
                            }
                        });
                }
            });
        });
    </script>

    {{-- Load CSRF Handler --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
</x-filament-panels::page.simple>
