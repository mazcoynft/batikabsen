<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>History - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        /* Minimalist History Page */
        .page-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        
        /* Minimalist Filter */
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        
        .filter-card .form-label {
            font-size: 12px;
            font-weight: 500;
            color: #666;
            margin-bottom: 6px;
        }
        
        .filter-card .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 12px;
            font-size: 14px;
            background: #fafafa;
        }
        
        .filter-card .form-control:focus {
            border-color: #0066ff;
            background: white;
            outline: none;
            box-shadow: none;
        }
        
        .filter-card .btn-primary {
            background: #0066ff;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .filter-card .btn-primary:hover {
            background: #0052cc;
        }
        
        /* Minimalist History List */
        .history-card {
            background: transparent;
            padding: 0;
        }
        
        .history-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .history-item {
            background: white;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            border: 2px solid #0066ff;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.2);
        }
        
        .history-date {
            font-size: 12px;
            color: #666;
            font-weight: 500;
            margin-bottom: 12px;
            padding-right: 140px;
        }
        
        .history-content {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        .history-time-info {
            flex: 1;
            font-size: 14px;
            color: #333;
            font-weight: 500;
            line-height: 1.8;
        }
        
        .history-time-info strong {
            color: #0066ff;
            font-weight: 600;
        }
        
        /* Minimalist Photos - Side by Side */
        .photo-container {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .photo-item {
            text-align: center;
        }
        
        .photo-thumbnail {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #e8e8e8;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .photo-label {
            font-size: 10px;
            color: #666;
            margin-top: 6px;
            font-weight: 500;
        }
        
        /* Minimalist Badges */
        .history-status {
            display: inline-block;
            font-weight: 500;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .status-hadir {
            background: #e6f3ff;
            color: #0066ff;
        }
        
        .status-izin {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .status-sakit {
            background: #e0f2f1;
            color: #00796b;
        }
        
        .status-cuti {
            background: #e6f3ff;
            color: #0066ff;
        }
        
        .status-ontime {
            background: #e8f5e9;
            color: #388e3c;
            margin-left: 6px;
        }
        
        .status-late {
            background: #ffebee;
            color: #d32f2f;
            margin-left: 6px;
        }
        
        .status-container {
            position: absolute;
            top: 16px;
            right: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            color: #999;
        }
        
        .empty-state i {
            font-size: 40px;
            color: #e0e0e0;
            margin-bottom: 12px;
        }
        
        .empty-state p {
            font-size: 13px;
            margin: 0;
            line-height: 1.6;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .page-title {
                font-size: 17px;
            }
            
            .filter-card {
                padding: 14px;
            }
            
            .history-item {
                padding: 14px;
            }
            
            .photo-thumbnail {
                width: 65px;
                height: 65px;
            }
            
            .photo-container {
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="user-profile">
            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
            </div>
            <form action="{{ route('frontend.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <div class="content">
        <h4 class="page-title">Riwayat Kehadiran</h4>
        
        <!-- Filter -->
        <div class="filter-card">
            <form action="{{ route('frontend.history') }}" method="GET">
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- History List -->
        <div class="history-card">
            <ul class="history-list">
                @forelse ($presensi as $p)
                    <li class="history-item">
                        <!-- Status Badges - Top Right -->
                        <div class="status-container">
                            <span class="history-status {{ $p->status == 'h' ? 'status-hadir' : ($p->status == 'i' ? 'status-izin' : ($p->status == 's' ? 'status-sakit' : ($p->status == 'c' ? 'status-cuti' : 'status-izin'))) }}">
                                @if($p->status == 'h')
                                    Hadir
                                @elseif($p->status == 'i')
                                    Izin
                                @elseif($p->status == 's')
                                    Sakit
                                @elseif($p->status == 'c')
                                    Cuti
                                @else
                                    {{ ucfirst($p->status) }}
                                @endif
                            </span>
                            
                            @if($p->status == 'h')
                                @if($p->status_presensi_in == '1')
                                    <span class="history-status status-ontime">Ontime</span>
                                @else
                                    <span class="history-status status-late">Late</span>
                                @endif
                            @endif
                        </div>
                        
                        <!-- Date -->
                        <div class="history-date">
                            {{ \Carbon\Carbon::parse($p->tgl_presensi)->locale('id')->translatedFormat('l, d M Y') }}
                        </div>
                        
                        <!-- Content: Time Info + Photos Side by Side -->
                        <div class="history-content">
                            <!-- Time Info -->
                            <div class="history-time-info">
                                @if($p->jam_in)
                                    Masuk: <strong>{{ $p->jam_in }}</strong>
                                @endif
                                @if($p->jam_out)
                                    <br>Pulang: <strong>{{ $p->jam_out }}</strong>
                                @endif
                                @if(!$p->jam_in && !$p->jam_out)
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            
                            <!-- Photos -->
                            @if($p->foto_in || $p->foto_out)
                                <div class="photo-container">
                                    @if($p->foto_in)
                                        <div class="photo-item">
                                            <img src="{{ Storage::url($p->foto_in) }}" alt="Foto Masuk" class="photo-thumbnail">
                                            <div class="photo-label">Masuk</div>
                                        </div>
                                    @endif
                                    @if($p->foto_out)
                                        <div class="photo-item">
                                            <img src="{{ Storage::url($p->foto_out) }}" alt="Foto Pulang" class="photo-thumbnail">
                                            <div class="photo-label">Pulang</div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Tidak ada data presensi</p>
                        <p class="text-muted mt-2" style="font-size: 12px;">Pilih rentang tanggal untuk melihat riwayat</p>
                    </li>
                @endforelse
            </ul>
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
                    <a class="nav-link active" href="{{ route('frontend.history') }}">
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
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
</body>
</html>
