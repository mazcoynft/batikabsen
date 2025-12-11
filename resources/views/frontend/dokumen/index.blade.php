<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $tipe == 'slip_gaji' ? 'Slip Gaji' : 'Dokumen' }} - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        :root {
            --primary-color: #0066ff;
            --primary-dark: #0052cc;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --bg-light: #F9FAFB;
            --border-color: #E5E7EB;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            background: var(--bg-light);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .modern-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            padding: 1.5rem 1rem;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .modern-header .header-content {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .modern-header .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none !important;
        }

        .modern-header .back-btn:focus {
            outline: none !important;
        }

        .modern-header .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(-6px) scale(1.1);
            animation: backBounce 0.6s ease;
        }

        @keyframes backBounce {
            0%, 100% {
                transform: translateX(-6px) scale(1.1);
            }
            50% {
                transform: translateX(-10px) scale(1.15);
            }
        }

        .modern-header .header-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .modern-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1.5rem 1rem 6rem;
        }

        .modern-tabs {
            background: white;
            border-radius: 16px;
            padding: 0.5rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .modern-tabs .tab-btn {
            flex: 1;
            padding: 0.875rem 1rem;
            border: none;
            background: transparent;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-secondary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            outline: none !important;
            cursor: pointer;
            text-decoration: none;
        }

        .modern-tabs .tab-btn:focus {
            outline: none !important;
            box-shadow: none !important;
        }

        .modern-tabs .tab-btn.active {
            background: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .modern-tabs .tab-btn:hover:not(.active) {
            background: var(--bg-light);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            animation: bounce 0.6s ease;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(-4px) scale(1.02);
            }
            50% {
                transform: translateY(-8px) scale(1.05);
            }
        }

        .dokumen-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .dokumen-card:hover {
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            border-color: var(--primary-color);
            transform: translateY(-6px) scale(1.02);
            animation: cardBounce 0.6s ease;
        }

        @keyframes cardBounce {
            0%, 100% {
                transform: translateY(-6px) scale(1.02);
            }
            50% {
                transform: translateY(-10px) scale(1.03);
            }
        }

        .dokumen-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .dokumen-card-title {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-primary);
            margin: 0;
            flex: 1;
        }

        .dokumen-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .dokumen-badge-unread {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .dokumen-badge-read {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .dokumen-card-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .dokumen-card-date i {
            color: var(--primary-color);
        }

        .dokumen-card-description {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .dokumen-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--bg-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-state-icon i {
            font-size: 2rem;
            color: var(--text-secondary);
        }

        .empty-state-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state-text {
            color: var(--text-secondary);
            font-size: 0.9375rem;
        }
    </style>
</head>
<body>
    <div class="modern-header">
        <div class="header-content">
            <a href="{{ route('frontend.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="header-title">{{ $tipe == 'slip_gaji' ? 'Slip Gaji' : 'Dokumen' }}</h1>
        </div>
    </div>

    <div class="modern-container">
        <!-- Tab Navigation -->
        <div class="modern-tabs">
            <a href="{{ route('frontend.dokumen.index', ['tipe' => 'slip_gaji']) }}" 
               class="tab-btn {{ $tipe == 'slip_gaji' ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i>
                <span>Slip Gaji</span>
            </a>
            <a href="{{ route('frontend.dokumen.index', ['tipe' => 'dokumen']) }}" 
               class="tab-btn {{ $tipe == 'dokumen' ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>Dokumen</span>
            </a>
        </div>

        <!-- Document List -->
        @if($dokumens->count() > 0)
            @foreach($dokumens as $dokumen)
                <div class="dokumen-card">
                    <div class="dokumen-card-header">
                        <h3 class="dokumen-card-title">{{ $dokumen->judul }}</h3>
                    </div>
                    
                    <div class="dokumen-card-date">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $dokumen->created_at->format('d F Y, H:i') }}</span>
                    </div>
                    
                    @if($dokumen->keterangan)
                        <p class="dokumen-card-description">{{ $dokumen->keterangan }}</p>
                    @endif
                    
                    <div class="dokumen-card-footer">
                        <span style="color: var(--text-secondary); font-size: 0.875rem;">
                            <i class="fas fa-file-pdf" style="color: #dc3545;"></i>
                            PDF Document
                        </span>
                        <a href="{{ Storage::url($dokumen->file_path) }}" 
                           class="download-btn" 
                           target="_blank"
                           onclick="event.stopPropagation();">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-state-title">Belum Ada {{ $tipe == 'slip_gaji' ? 'Slip Gaji' : 'Dokumen' }}</h3>
                <p class="empty-state-text">{{ $tipe == 'slip_gaji' ? 'Slip gaji' : 'Dokumen' }} yang dikirim admin akan muncul di sini</p>
            </div>
        @endif
    </div>

    <!-- Bottom Navigation -->
    <nav class="navbar navbar-expand navbar-light bottom-nav">
        <div class="container">
            <ul class="navbar-nav w-100">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.dashboard') }}">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.history') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>History</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.absen') }}">
                        <div class="circle-icon">
                            <i class="fas fa-camera"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.izin.index') }}">
                        <i class="fas fa-calendar"></i>
                        <span>Izin</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('frontend.profile') }}">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAsRead(id) {
            fetch(`/dokumen/${id}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }
    </script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
</body>
</html>
