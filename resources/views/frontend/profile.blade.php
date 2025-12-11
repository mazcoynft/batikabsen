<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        /* Profile specific styles */
        .profile-header {
            text-align: center;
            padding: 24px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 16px 16px 0 0;
        }
        
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            margin: 0 auto 16px auto;
            display: block;
            box-shadow: 0 8px 24px rgba(74, 144, 226, 0.2);
        }
        
        .profile-name {
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 4px;
            color: #333;
        }
        
        .profile-position {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 14px;
        }
        
        .info-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .info-item:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            margin-right: 16px;
            text-align: center;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .info-label {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 4px;
            font-weight: 500;
        }
        
        .info-value {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .change-password-btn {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            width: 100%;
            margin-top: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        .change-password-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
        }
        
        .modal-content {
            border-radius: 16px;
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }
        
        .modal-title {
            font-weight: 700;
            color: #333;
        }
        
        .form-control {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="user-profile">
            <div class="user-info">
                <p class="user-name">{{ Auth::user()->name }}</p>
                <p class="user-position">{{ Auth::user()->jabatan }}</p>
            </div>
            <form action="{{ route('frontend.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
    
    <div class="content">
        <div class="card">
            <div class="profile-header">
                <img src="{{ Auth::user()->avatar_url ? asset('storage/' . Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" alt="Profile Picture" class="profile-pic">
                <h4 class="profile-name">{{ Auth::user()->name }}</h4>
                <p class="profile-position">{{ Auth::user()->jabatan }}</p>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <div class="info-label">NIK</div>
                    <div class="info-value">{{ Auth::user()->nik_app }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ Auth::user()->email ?? 'Belum diatur' }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div>
                    <div class="info-label">Telepon</div>
                    <div class="info-value">{{ Auth::user()->phone ?? 'Belum diatur' }}</div>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <div class="info-label">Departemen</div>
                    <div class="info-value">{{ Auth::user()->karyawan->department->nama_dept ?? 'Belum diatur' }}</div>
                </div>
            </div>
            
            <!-- Bagian tanggal bergabung dihapus -->
            
        
            
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif
                <button type="button" class="change-password-btn mb-2" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                    <i class="fas fa-image"></i> Ganti Avatar
                </button>
                <button type="button" class="change-password-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-lock"></i> Ubah Password
                </button>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Avatar Modal -->
    <div class="modal fade" id="changeAvatarModal" tabindex="-1" aria-labelledby="changeAvatarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeAvatarModalLabel">Ganti Avatar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('frontend.profile.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pilih Gambar</label>
                            <input type="file" class="form-control" name="avatar" accept="image/*" required>
                            <div class="form-text">Format: JPG, PNG, WEBP. Maks 2MB.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Avatar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bottom Navigation Bar -->
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
                    <a class="nav-link active" href="{{ route('frontend.profile') }}">
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
