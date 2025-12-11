<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pengajuan Izin - USSIBATIK ABSEN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 160px;
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header-title {
            text-align: center;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .container {
            padding: 0 15px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .nav-tabs {
            border-bottom: none;
            display: flex;
            justify-content: center;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 0;
            text-align: center;
            flex: 1;
            border-radius: 0;
            background-color: #f8f9fa;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #4a90e2 0%, #7b61ff 50%, #00bcd4 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            color: transparent !important;
            background-color: #fff !important;
            border-bottom: 3px solid transparent !important;
            border-image: linear-gradient(90deg, #4a90e2 0%, #7b61ff 50%, #00bcd4 100%) 1 !important;
            font-weight: 600 !important;
        }

        .tab-content {
            padding: 20px;
            background-color: #fff;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            box-shadow: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        }

        .btn-primary {
            background-color: #4a90e2;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary i {
            margin-right: 8px;
        }

        .history-item {
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-info .date {
            font-weight: 500;
            color: #333;
        }

        .history-info .type {
            font-size: 0.9rem;
            color: #666;
        }

        .history-status .badge {
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 8px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border-radius: 20px 20px 0 0;
            padding: 10px 0 5px 0;
            display: flex;
            justify-content: space-around;
        }

        .bottom-nav .nav-item {
            flex: 1;
            text-align: center;
        }

        .bottom-nav .nav-link {
            padding: 8px 0;
            color: #6c757d;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 12px;
            text-decoration: none;
        }

        .bottom-nav .nav-link.active {
            color: #4a90e2;
        }

        .bottom-nav .nav-link i {
            font-size: 1.3rem;
            margin-bottom: 4px;
        }

        .circle-icon {
            background-color: #4a90e2;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -30px;
            box-shadow: 0 4px 10px rgba(74, 144, 226, 0.5);
            border: 4px solid white;
        }

        .circle-icon i {
            font-size: 24px !important;
            margin: 0 !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="header-title">Pengajuan Izin</h1>

        <div class="card">
            <ul class="nav nav-tabs" id="izinTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#form-content"
                        type="button" role="tab" aria-controls="form-content" aria-selected="true">Form
                        Pengajuan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content"
                        type="button" role="tab" aria-controls="history-content" aria-selected="false">Riwayat
                        Pengajuan</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Form Pengajuan -->
                <div class="tab-pane fade show active" id="form-content" role="tabpanel" aria-labelledby="form-tab">
                    <form action="{{ route('frontend.izin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Dropdown Jenis Pengajuan -->
                        <div class="mb-3">
                            <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan</label>
                            <select class="form-select" id="jenis_pengajuan" name="status" required>
                                <option value="">Pilih Jenis Pengajuan</option>
                                <option value="i">Izin</option>
                                <option value="s">Sakit</option>
                                <option value="c">Cuti</option>
                            </select>
                        </div>

                        <!-- Dropdown Jenis Cuti (muncul jika memilih Cuti) -->
                        <div class="mb-3" id="jenis_cuti_section" style="display: none;">
                            <label for="kode_cuti" class="form-label">Jenis Cuti</label>
                            <select class="form-select" id="kode_cuti" name="kode_cuti">
                                <option value="">Pilih Jenis Cuti</option>
                                @foreach ($jenis_cuti as $cuti)
                                    <option value="{{ $cuti->kode_cuti }}" data-nama-cuti="{{ $cuti->nama_cuti }}">
                                        {{ $cuti->nama_cuti }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Info Sisa Cuti (muncul jika memilih CUTI TAHUNAN) -->
                        <div class="mb-3" id="sisa_cuti_section" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Sisa Cuti Tahunan Anda: <span id="sisa_cuti_text">{{ $sisa_cuti_tahunan ?? 12 }}</span> hari</strong>
                                <br><small>Total jatah cuti tahunan: 12 hari per tahun</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tgl_izin_dari" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tgl_izin_dari" name="tgl_izin_dari" required>
                        </div>

                        <div class="mb-3">
                            <label for="tgl_izin_sampai" class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" id="tgl_izin_sampai" name="tgl_izin_sampai" required>
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="doc_sid" class="form-label">Bukti (Opsional)</label>
                            <input class="form-control" type="file" id="doc_sid" name="doc_sid">
                            <div class="form-text">Format: JPG, PNG, PDF. Maks: 2MB</div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-paper-plane"></i> Kirim Pengajuan
                        </button>
                    </form>
                </div>

                <!-- Riwayat Pengajuan -->
                <div class="tab-pane fade" id="history-content" role="tabpanel" aria-labelledby="history-tab">
                    @if ($riwayat_izin->count() > 0)
                        @foreach ($riwayat_izin as $izin)
                            <div class="history-item">
                                <div class="history-info">
                                    <div class="date">{{ \Carbon\Carbon::parse($izin->tgl_izin_dari)->format('d M Y') }} -
                                        {{ \Carbon\Carbon::parse($izin->tgl_izin_sampai)->format('d M Y') }}</div>
                                    <div class="type">
                                        @if($izin->status == 'i')
                                            Izin
                                        @elseif($izin->status == 's')
                                            Sakit
                                        @elseif($izin->status == 'c' && $izin->cuti)
                                            {{ $izin->cuti->nama_cuti }}
                                        @else
                                            Cuti
                                        @endif
                                    </div>
                                </div>
                                <div class="history-status">
                                    @if ($izin->status_approved == '0')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($izin->status_approved == '1')
                                        <span class="badge bg-success">Disetujui</span>
                                    @elseif($izin->status_approved == '2')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">Dibatalkan</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-center text-muted">Belum ada riwayat pengajuan.</p>
                    @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jenisPengajuanSelect = document.getElementById('jenis_pengajuan');
            const jenisCutiSection = document.getElementById('jenis_cuti_section');
            const kodeCutiSelect = document.getElementById('kode_cuti');
            const sisaCutiSection = document.getElementById('sisa_cuti_section');

            // Event listener untuk dropdown jenis pengajuan
            jenisPengajuanSelect.addEventListener('change', function() {
                const value = this.value;
                
                if (value === 'c') {
                    // Jika memilih Cuti, tampilkan dropdown jenis cuti
                    jenisCutiSection.style.display = 'block';
                    kodeCutiSelect.setAttribute('required', 'required');
                } else {
                    // Jika memilih Izin atau Sakit, sembunyikan dropdown jenis cuti dan sisa cuti
                    jenisCutiSection.style.display = 'none';
                    sisaCutiSection.style.display = 'none';
                    kodeCutiSelect.removeAttribute('required');
                    kodeCutiSelect.value = '';
                }
            });

            // Event listener untuk dropdown jenis cuti
            kodeCutiSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const namaCuti = selectedOption.getAttribute('data-nama-cuti');
                
                // Jika memilih CUTI TAHUNAN, tampilkan info sisa cuti
                if (namaCuti && namaCuti.toUpperCase().includes('CUTI TAHUNAN')) {
                    sisaCutiSection.style.display = 'block';
                } else {
                    sisaCutiSection.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>