<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - USSIBATIK ABSEN</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-wrapper {
            width: 100%;
            max-width: 400px;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .logo-section img {
            width: 100px;
            height: 100px;
            margin-bottom: 16px;
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
        }
        
        .logo-section h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 4px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .logo-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
        }
        
        .login-card {
            background: white;
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .login-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            text-align: center;
        }
        
        .login-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 28px;
            text-align: center;
        }
        
        .alert {
            background: #fee;
            border: 1px solid #fcc;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #c33;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .alert li {
            margin: 4px 0;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }
        
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border: 2px solid #e5e5e5;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #fafafa;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-input::placeholder {
            color: #aaa;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 16px;
            transition: color 0.3s ease;
            padding: 4px;
        }
        
        .password-toggle:active {
            color: #667eea;
        }
        
        .forgot-link {
            display: block;
            text-align: right;
            font-size: 13px;
            color: #667eea;
            text-decoration: none;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .forgot-link:active {
            color: #764ba2;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin-top: 24px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-login:active {
            transform: scale(0.98);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        /* Remove number input arrows */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        input[type=number] {
            -moz-appearance: textfield;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            
            .login-card {
                padding: 28px 20px;
            }
            
            .logo-section h1 {
                font-size: 22px;
            }
            
            .logo-section img {
                width: 90px;
                height: 90px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="logo-section">
            <img src="{{ asset('images/login3d.png') }}" alt="USSIBATIK ABSEN">
            <h1>USSIBATIK ABSEN</h1>
            <p>Smart Attendance System</p>
        </div>
        
        <div class="login-card">
            <h2 class="login-title">Selamat Datang</h2>
            <p class="login-subtitle">Silahkan login untuk melanjutkan</p>
            
            @if ($errors->any())
                <div class="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('frontend.login.post') }}" method="POST" id="loginForm">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="number" 
                               class="form-input" 
                               id="nik" 
                               name="nik" 
                               placeholder="Masukkan NIK Anda" 
                               required 
                               autofocus
                               autocomplete="username"
                               inputmode="numeric">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-input" 
                               id="password" 
                               name="password" 
                               placeholder="Masukkan password Anda" 
                               required
                               autocomplete="current-password">
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="far fa-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                    <a href="#" class="forgot-link">Lupa Password?</a>
                </div>
                
                <button type="submit" class="btn-login" id="loginButton">
                    <span class="btn-text">MASUK</span>
                    <span class="btn-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Memproses...
                    </span>
                </button>
            </form>
        </div>
        
        <div class="footer-text">
            © 2024 USSIBATIK. All rights reserved.
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }

        // Mobile-optimized login handling
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            const btnText = loginButton.querySelector('.btn-text');
            const btnLoading = loginButton.querySelector('.btn-loading');
            const nikInput = document.getElementById('nik');
            const passwordInput = document.getElementById('password');

            // Enter key support for mobile keyboards
            function handleEnterKey(event) {
                if (event.key === 'Enter' || event.keyCode === 13) {
                    event.preventDefault();
                    
                    if (event.target === nikInput && passwordInput.value === '') {
                        // If NIK is filled and password is empty, focus password
                        passwordInput.focus();
                    } else if (nikInput.value !== '' && passwordInput.value !== '') {
                        // If both fields are filled, submit form
                        submitForm();
                    }
                }
            }

            // Add enter key listeners
            nikInput.addEventListener('keydown', handleEnterKey);
            passwordInput.addEventListener('keydown', handleEnterKey);

            // Form submission with loading state
            function submitForm() {
                if (nikInput.value === '' || passwordInput.value === '') {
                    return false;
                }

                // Show loading state
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';
                loginButton.disabled = true;

                // Refresh CSRF token before submit (WebView optimization)
                if (window.csrfHandler) {
                    window.csrfHandler.refreshToken().then(() => {
                        loginForm.submit();
                    }).catch(() => {
                        loginForm.submit(); // Submit anyway if refresh fails
                    });
                } else {
                    loginForm.submit();
                }
            }

            // Handle form submit
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm();
            });

            // Mobile keyboard optimization
            nikInput.addEventListener('focus', function() {
                // Scroll to input on mobile
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });

            passwordInput.addEventListener('focus', function() {
                // Scroll to input on mobile
                setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            });

            // Auto-focus NIK input on page load (mobile friendly)
            setTimeout(() => {
                if (nikInput && !nikInput.value) {
                    nikInput.focus();
                }
            }, 500);
        });
        
        // Prevent zoom on input focus for iOS
        document.addEventListener('touchstart', function() {}, {passive: true});
        
        // Prevent double-tap zoom
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
    
    {{-- Mobile Optimizations --}}
    <script src="{{ asset('js/mobile-optimizations.js') }}"></script>
    
    {{-- Frontend Security --}}
    <script src="{{ asset('js/frontend-security.js') }}"></script>
</body>
</html>