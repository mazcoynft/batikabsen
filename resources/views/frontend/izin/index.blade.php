<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Izin - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        /* Page specific styles */
        .nav-tabs {
            border-bottom: none;
            margin-bottom: 16px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }
        
        .nav-tabs .nav-item {
            flex: 1;
            max-width: 200px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 12px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        
        .nav-tabs .nav-link:hover {
            color: #4a90e2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control, .form-select, textarea {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .form-control:focus, .form-select:focus, textarea:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 4px rgba(74, 144, 226, 0.1);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(74, 144, 226, 0.4);
        }
        
        .list-group-item {
            border-radius: 12px !important;
            border: 1px solid #f0f0f0 !important;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>
    <div class="header">
        <h5 class="mb-0">Pengajuan Izin</h5>
    </div>
    
    <div class="content">
        <ul class="nav nav-tabs" id="izinTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="form-pengajuan-tab" data-bs-toggle="tab" data-bs-target="#form-pengajuan" type="button" role="tab" aria-controls="form-pengajuan" aria-selected="true">Form Pengajuan</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="riwayat-pengajuan-tab" data-bs-toggle="tab" data-bs-target="#riwayat-pengajuan" type="button" role="tab" aria-controls="riwayat-pengajuan" aria-selected="false">Riwayat Izin</button>
            </li>
        </ul>

        <div class="tab-content" id="izinTabContent">
            <div class="tab-pane fade show active" id="form-pengajuan" role="tabpanel" aria-labelledby="form-pengajuan-tab">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('frontend.izin.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="jenis_izin" class="form-label">Jenis Izin</label>
                                <select class="form-select" id="jenis_izin" name="jenis_izin" required>
                                    <option selected disabled value="">Pilih Jenis Izin</option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                </select>
                            </div>
                            <div class="mb-3" id="jenis_cuti_div" style="display: none;">
                                <label for="cuti_id" class="form-label">Jenis Cuti</label>
                                <select class="form-select" id="cuti_id" name="cuti_id">
                                    <option selected disabled value="">Pilih Jenis Cuti</option>
                                    @foreach($jenis_cuti_db as $cuti)
                                        <option value="{{ $cuti->id }}" data-nama="{{ $cuti->nama_cuti }}">{{ $cuti->nama_cuti }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2" id="sisa_cuti_div" style="display: none;">
                                <span>Sisa Cuti Tahunan - </span>
                                <span id="sisa_cuti_value" style="color:#4a90e2; font-weight:600;">12 Hari</span>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                            </div>
                            <div class="mb-2" id="jumlah_hari_div" style="display: none;">
                                <span>Jumlah Hari Kerja - </span>
                                <span id="jumlah_hari_value" style="color:#4a90e2; font-weight:600;">0 Hari Kerja</span>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="bukti" class="form-label">Bukti (Opsional)</label>
                                <input class="form-control" type="file" id="bukti" name="bukti">
                                <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB</div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="riwayat-pengajuan" role="tabpanel" aria-labelledby="riwayat-pengajuan-tab">
                <div class="list-group">
                    @forelse($pengajuan_izin as $izin)
                        <div class="list-group-item mb-3 border rounded-3 p-3">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><strong>{{ $izin->jenis_pengajuan }}</strong> - {{ $izin->keterangan }}</h6>
                                <span class="badge 
                                    @switch($izin->status)
                                        @case('pending') bg-warning text-dark @break
                                        @case('approved') bg-success @break
                                        @case('rejected') bg-danger @break
                                        @default bg-secondary
                                    @endswitch\">
                                    {{ ucfirst($izin->status) }}
                                </span>
                            </div>
                            <p class="mb-1">
                                {{ \Carbon\Carbon::parse($izin->tanggal_awal)->format('d M') }} - {{ \Carbon\Carbon::parse($izin->tanggal_akhir)->format('d M Y') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-center">Belum ada riwayat izin.</p>
                    @endforelse
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
                    <a class="nav-link active" href="{{ route('frontend.izin.index') }}">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Menampilkan/menyembunyikan dropdown jenis cuti
            $('#jenis_izin').change(function() {
                if ($(this).val() == 'Cuti') {
                    $('#jenis_cuti_div').show();
                    $('#cuti_id').prop('required', true);
                } else {
                    $('#jenis_cuti_div').hide();
                    $('#sisa_cuti_div').hide();
                    $('#cuti_id').prop('required', false);
                }
            });

            // Menampilkan sisa cuti jika cuti tahunan dipilih
            $('#cuti_id').change(function() {
                var selectedCutiName = $(this).find('option:selected').data('nama');
                if (selectedCutiName && selectedCutiName.toLowerCase().includes('tahunan')) {
                    // Lakukan AJAX call untuk mendapatkan sisa cuti yang sebenarnya
                    let cutiId = $(this).val();
                    $.ajax({
                        url: `/izin/get-cuti-details/${cutiId}`,
                        type: 'GET',
                        success: function(data) {
                            if(data.potong_cuti && data.sisa_cuti !== null) {
                                $('#sisa_cuti_value').text(data.sisa_cuti + ' Hari');
                                $('#sisa_cuti_div').show();
                            } else {
                                $('#sisa_cuti_div').hide();
                            }
                        },
                        error: function() {
                             // Fallback jika AJAX gagal
                            $('#sisa_cuti_value').text('12 Hari');
                            $('#sisa_cuti_div').show();
                        }
                    });
                } else {
                    $('#sisa_cuti_div').hide();
                }
            });

            // Menghitung jumlah hari kerja (excluding weekends)
            function calculateWorkingDays() {
                var startDate = $('#tanggal_mulai').val();
                var endDate = $('#tanggal_selesai').val();

                if (startDate && endDate) {
                    var start = new Date(startDate);
                    var end = new Date(endDate);
                    
                    if (end < start) {
                        $('#jumlah_hari_div').hide();
                        return;
                    }

                    var workingDays = 0;
                    var currentDate = new Date(start);
                    
                    while (currentDate <= end) {
                        var dayOfWeek = currentDate.getDay();
                        // 0 = Sunday, 6 = Saturday
                        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                            workingDays++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                    
                    $('#jumlah_hari_value').text(workingDays + ' Hari Kerja');
                    $('#jumlah_hari_div').show();
                }
                else {
                    $('#jumlah_hari_div').hide();
                }
            }

            $('#tanggal_mulai, #tanggal_selesai').change(calculateWorkingDays);
        });
    </script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
</body>
</html>
