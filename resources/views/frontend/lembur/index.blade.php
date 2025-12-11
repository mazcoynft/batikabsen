<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lembur - USSIBATIK ABSEN</title>
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

        /* Modern Header */
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

        /* Modern Tabs */
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

        /* Modern Card */
        .modern-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Modern Form */
        .modern-form-group {
            margin-bottom: 1.25rem;
        }

        .modern-form-label {
            display: block;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .modern-form-label .required {
            color: var(--danger-color);
        }

        .modern-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 0.9375rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            outline: none !important;
        }

        .modern-input:focus {
            outline: none !important;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 102, 255, 0.08);
            transform: translateY(-2px);
        }

        .modern-input:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .modern-input.is-invalid {
            border-color: var(--danger-color);
        }

        .modern-input.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .modern-textarea {
            min-height: 120px;
            resize: vertical;
        }

        /* Modern Button */
        .modern-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            outline: none !important;
        }

        .modern-btn:focus {
            outline: none !important;
        }

        .modern-btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .modern-btn-primary:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.3);
            animation: buttonBounce 0.6s ease;
        }

        .modern-btn-primary:active {
            transform: translateY(-2px) scale(0.98);
        }

        @keyframes buttonBounce {
            0%, 100% {
                transform: translateY(-6px) scale(1.02);
            }
            50% {
                transform: translateY(-10px) scale(1.05);
            }
        }

        /* Modern Badge */
        .modern-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .modern-badge-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .modern-badge-approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .modern-badge-rejected {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        /* History Card */
        .history-card {
            background: white;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .history-card:hover {
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

        .history-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
        }

        .history-card-title {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-primary);
            margin: 0;
        }

        .history-card-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .history-card-date i {
            color: var(--primary-color);
        }

        .history-card-description {
            color: var(--text-secondary);
            font-size: 0.9375rem;
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .history-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid var(--border-color);
        }

        .history-card-time {
            font-size: 0.8125rem;
            color: var(--text-secondary);
        }

        /* Empty State */
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

        /* Alert */
        .modern-alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: start;
            gap: 0.75rem;
            border: 1px solid;
        }

        .modern-alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success-color);
            color: var(--success-color);
        }

        .modern-alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger-color);
            color: var(--danger-color);
        }

        .modern-alert-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }

        .modern-alert-content {
            flex: 1;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        .modern-alert-close {
            flex-shrink: 0;
            background: none;
            border: none;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
        }

        .modern-alert-close:hover {
            opacity: 1;
        }

        /* Content Container */
        .modern-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1.5rem 1rem 6rem;
        }

        /* Tab Content */
        .tab-content-wrapper {
            display: none;
        }

        .tab-content-wrapper.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Invalid Feedback */
        .invalid-feedback {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="modern-header">
        <div class="header-content">
            <a href="{{ route('frontend.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="header-title">Pengajuan Lembur</h1>
        </div>
    </div>
    
    <div class="modern-container">
        @if(session('success'))
            <div class="modern-alert modern-alert-success">
                <div class="modern-alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="modern-alert-content">
                    {{ session('success') }}
                </div>
                <button type="button" class="modern-alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="modern-alert modern-alert-danger">
                <div class="modern-alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="modern-alert-content">
                    {{ session('error') }}
                </div>
                <button type="button" class="modern-alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        
        <!-- Modern Tab Navigation -->
        <div class="modern-tabs">
            <button class="tab-btn active" onclick="switchTab('form')">
                <i class="fas fa-plus-circle"></i>
                <span>Ajukan</span>
            </button>
            <button class="tab-btn" onclick="switchTab('history')">
                <i class="fas fa-history"></i>
                <span>Riwayat</span>
            </button>
        </div>
        
        <!-- Form Tab -->
        <div class="tab-content-wrapper active" id="form-content">
            <div class="modern-card">
                <form action="{{ route('frontend.lembur.store') }}" method="POST">
                    @csrf
                    
                    <div class="modern-form-group">
                        <label class="modern-form-label">
                            Tanggal Awal Lembur <span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_awal_lembur" 
                            class="modern-input @error('tanggal_awal_lembur') is-invalid @enderror" 
                            value="{{ old('tanggal_awal_lembur') }}" 
                            required
                        >
                        @error('tanggal_awal_lembur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="modern-form-group">
                        <label class="modern-form-label">
                            Tanggal Akhir Lembur <span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="tanggal_akhir_lembur" 
                            class="modern-input @error('tanggal_akhir_lembur') is-invalid @enderror" 
                            value="{{ old('tanggal_akhir_lembur') }}" 
                            required
                        >
                        @error('tanggal_akhir_lembur')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="modern-form-group">
                        <label class="modern-form-label">
                            Nama Lembaga <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_lembaga" 
                            class="modern-input @error('nama_lembaga') is-invalid @enderror" 
                            value="{{ old('nama_lembaga') }}" 
                            placeholder="Contoh: BMT Bahtera" 
                            required
                        >
                        @error('nama_lembaga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="modern-form-group">
                        <label class="modern-form-label">
                            Keterangan <span class="required">*</span>
                        </label>
                        <textarea 
                            name="keterangan" 
                            class="modern-input modern-textarea @error('keterangan') is-invalid @enderror" 
                            placeholder="Contoh: Update database, maintenance server" 
                            required
                        >{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="modern-btn modern-btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        <span>Submit Pengajuan</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- History Tab -->
        <div class="tab-content-wrapper" id="history-content">
            @if($lemburs->count() > 0)
                @foreach($lemburs as $lembur)
                    <div class="history-card">
                        <div class="history-card-header">
                            <h3 class="history-card-title">{{ $lembur->nama_lembaga }}</h3>
                            <span class="modern-badge modern-badge-{{ $lembur->status }}">
                                {{ ucfirst($lembur->status) }}
                            </span>
                        </div>
                        
                        <div class="history-card-date">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ date('d/m/Y', strtotime($lembur->tanggal_awal_lembur)) }} - {{ date('d/m/Y', strtotime($lembur->tanggal_akhir_lembur)) }}</span>
                        </div>
                        
                        <p class="history-card-description">{{ $lembur->keterangan }}</p>
                        
                        <div class="history-card-footer">
                            <span class="history-card-time">
                                <i class="fas fa-clock"></i>
                                {{ $lembur->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="empty-state-title">Belum Ada Riwayat</h3>
                    <p class="empty-state-text">Pengajuan lembur Anda akan muncul di sini</p>
                </div>
            @endif
        </div>
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
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.tab-btn').classList.add('active');
            
            // Update tab content
            document.querySelectorAll('.tab-content-wrapper').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabName + '-content').classList.add('active');
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.modern-alert').forEach(alert => {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
</body>
</html>
