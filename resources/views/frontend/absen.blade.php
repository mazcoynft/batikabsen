<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absen - USSIBATIK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/frontend-mobile.css') }}">
    <style>
        body {
            padding-bottom: 80px;
        }
        
        /* Modern Camera Container */
        .camera-container {
            position: relative;
            width: 100%;
            height: 320px;
            overflow: hidden;
            background-color: #000;
            border-radius: 0 0 20px 20px;
            border: 3px solid #0066ff;
        }
        
        #camera-view {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Modern Overlay */
        .overlay {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.75) 100%);
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-size: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .overlay div {
            margin-bottom: 4px;
            line-height: 1.4;
        }
        
        .overlay div:last-child {
            margin-bottom: 0;
        }
        
        #current-time {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px !important;
        }
        
        #shift-info {
            color: #00aaff;
            font-weight: 600;
            margin-bottom: 6px !important;
        }
        
        /* Modern Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            padding: 16px;
            background-color: #f8f9fa;
        }
        
        .btn {
            flex: 1;
            padding: 14px 10px;
            text-align: center;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            gap: 6px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn i {
            font-size: 18px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%);
        }
        
        .absen-pulang-button {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .btn-light-blue {
            background: linear-gradient(135deg, #87CEEB 0%, #4FC3F7 100%);
            color: #333 !important;
        }
        
        .onsite-pulang-button {
            background: linear-gradient(135deg, #87CEEB 0%, #4FC3F7 100%);
            color: #333 !important;
        }
        
        .btn-purple {
            background: linear-gradient(135deg, #DDA0DD 0%, #BA68C8 100%);
            color: #333 !important;
        }
        
        .wfh-pulang-button {
            background: linear-gradient(135deg, #DDA0DD 0%, #BA68C8 100%);
            color: #333 !important;
        }
        
        .disabled {
            background: #cccccc !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        .disabled:hover {
            transform: none !important;
        }
        
        /* Modern Map Container */
        .map-container {
            width: 100%;
            height: 220px;
            position: relative;
            margin: 16px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 3px solid #0066ff;
        }
        
        #map {
            width: 100%;
            height: 100%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
        }
        
        #map.map-loaded {
            background-color: transparent;
        }
        
        /* Modern Location Alert */
        .location-alert {
            position: absolute;
            top: 12px;
            left: 12px;
            right: 12px;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 12px;
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            display: none;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        @media (max-width: 576px) {
            .camera-container {
                height: 280px;
            }
            
            .action-buttons {
                padding: 12px;
                gap: 8px;
            }
            
            .btn {
                padding: 12px 8px;
                font-size: 13px;
            }
            
            .btn i {
                font-size: 20px;
                margin-bottom: 4px;
            }
            
            .overlay {
                font-size: 11px;
                padding: 10px;
            }
            
            #current-time {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h5 class="mb-0">USSIBATIK ABSEN</h5>
    </div>
    
    <div class="camera-container">
        <video id="camera-view" autoplay playsinline></video>
        <canvas id="canvas" style="display:none;"></canvas>
        <div class="overlay">
            <div id="current-time">00:00:00</div>
            <div id="shift-info">Normal</div>
            <div>Mulai: <span id="jam-mulai">00:00</span></div>
            <div>Masuk: <span id="jam-masuk">00:00</span></div>
            <div>Akhir: <span id="jam-akhir">00:00</span></div>
            <div>Pulang: <span id="jam-pulang">00:00</span></div>
        </div>
    </div>
    
    <div class="action-buttons" id="btn-container">
        <button class="btn btn-primary absen-button" id="btn-absen">
            <i class="fas fa-sign-in-alt"></i> Hadir
        </button>
        <button class="btn btn-light-blue onsite-button" id="btn-onsite">
            <i class="fas fa-building"></i> Onsite
        </button>
        <button class="btn btn-purple wfh-button" id="btn-wfh">
            <i class="fas fa-home"></i> WFH
        </button>
    </div>
    
    <div class="map-container">
        <div class="location-alert" id="location-alert">
            Anda berada di luar radius kantor! Tidak dapat melakukan absensi.
        </div>
        <div id="map">
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column;">
                <i class="fas fa-map-marker-alt" style="font-size: 24px; color: #0066ff; margin-bottom: 8px;"></i>
                <span>Memuat peta...</span>
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
                    <a class="nav-link active" href="{{ route('frontend.absen') }}">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script>
        // ==================================
        // GLOBAL VARIABLES
        // ==================================
        let stream;
        const video = document.getElementById('camera-view');
        const canvas = document.getElementById('canvas');
        const btnAbsen = document.getElementById('btn-absen');
        const locationAlert = document.getElementById('location-alert');

        let userLocation = null;
        let map = null;
        let userMarker = null;
        let radiusCircle = null;
        
        let cabangData = null;
        let jamKerjaData = null;
        let presensiHariIni = null;

        let isWithinRadius = false;
        // Default ke 'true' (Absen Masuk). Akan diperbarui dari server.
        let isAbsenMasuk = true; // Always start with true, will be updated from server data
        // Variable untuk mode WFH dan Onsite
        let isWfhMode = false;
        let isOnsiteMode = false;

        // ==================================
        // INITIALIZATION
        // ==================================
        document.addEventListener('DOMContentLoaded', function() {
            initCamera();
            initMap(); // Initialize immediately
            getJamKerja(); // Fungsi utama untuk mengambil data & mengatur status
            updateClock();
            setInterval(updateClock, 1000);
            
            btnAbsen.addEventListener('click', takeAbsen);
            
            // Tambahkan event listener untuk tombol WFH
            const btnWfh = document.getElementById('btn-wfh');
            btnWfh.addEventListener('click', function() {
                // Langsung ambil absen dengan mode WFH
                takeAbsenWfh();
            });
            
            // Tambahkan event listener untuk tombol Onsite
            const btnOnsite = document.getElementById('btn-onsite');
            btnOnsite.addEventListener('click', function() {
                // Langsung ambil absen dengan mode Onsite
                takeAbsenOnsite();
            });
        });
        
        /**
         * Fungsi untuk absen WFH
         */
        function takeAbsenWfh() {
            // Set WFH mode
            isWfhMode = true;
            isOnsiteMode = false;
            
            if (!userLocation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lokasi Tidak Tersedia',
                    text: 'Mohon tunggu sebentar dan pastikan GPS aktif.',
                    confirmButtonColor: '#1a73e8'
                });
                return;
            }
            
            // Tampilkan popup untuk input keterangan
            Swal.fire({
                title: 'Keterangan WFH',
                input: 'textarea',
                inputLabel: 'Masukkan keterangan (opsional)',
                inputPlaceholder: 'Contoh: WFH dari rumah, Meeting dengan client, dll...',
                showCancelButton: true,
                confirmButtonText: 'Absen WFH',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1a73e8',
                inputValidator: (value) => {
                    // Keterangan tidak wajib, jadi tidak perlu validasi
                    return null;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const keterangan = result.value || '';
                    
                    // Capture image from camera
                    const context = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = canvas.toDataURL('image/png');
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Send attendance data to server with CSRF handling
                    sendAttendanceRequest({
                        foto: imageData,
                        latitude: userLocation.lat,
                        longitude: userLocation.lng,
                        is_absen_masuk: true, // Selalu absen masuk untuk WFH
                        status_presensi_in: 4, // Status 4 untuk WFH (sesuai permintaan)
                        jenis_presensi: 'wfh', // Jenis presensi WFH
                        keterangan: keterangan
                    });
                }
            });
        }
        
        /**
         * Fungsi untuk absen Onsite
         */
        function takeAbsenOnsite() {
            // Set Onsite mode
            isOnsiteMode = true;
            isWfhMode = false;
            
            if (!userLocation) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lokasi Tidak Tersedia',
                    text: 'Mohon tunggu sebentar dan pastikan GPS aktif.',
                    confirmButtonColor: '#1a73e8'
                });
                return;
            }
            
            // Tampilkan popup untuk input keterangan
            Swal.fire({
                title: 'Keterangan Onsite',
                input: 'textarea',
                inputLabel: 'Masukkan keterangan (opsional)',
                inputPlaceholder: 'Contoh: Onsite di PT ABC, Kunjungan ke cabang Jakarta, dll...',
                showCancelButton: true,
                confirmButtonText: 'Absen Onsite',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1a73e8',
                inputValidator: (value) => {
                    // Keterangan tidak wajib, jadi tidak perlu validasi
                    return null;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const keterangan = result.value || '';
                    
                    // Capture image from camera
                    const context = canvas.getContext('2d');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = canvas.toDataURL('image/png');
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Send attendance data to server with CSRF handling
                    sendAttendanceRequest({
                        foto: imageData,
                        latitude: userLocation.lat,
                        longitude: userLocation.lng,
                        is_absen_masuk: true, // Selalu absen masuk untuk Onsite
                        status_presensi_in: 3, // Status 3 untuk Onsite (sesuai permintaan)
                        jenis_presensi: 'onsite', // Jenis presensi Onsite
                        keterangan: keterangan
                    });
                }
            });
        }
        
        /**
         * Initialize camera view
         */
        function initCamera() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Optimized camera constraints for mobile
                const constraints = {
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640, max: 1280 },
                        height: { ideal: 480, max: 720 },
                        frameRate: { ideal: 30, max: 30 }
                    }
                };

                navigator.mediaDevices.getUserMedia(constraints)
                    .then(function(mediaStream) {
                        stream = mediaStream;
                        video.srcObject = mediaStream;
                        
                        // Optimize video element for mobile
                        video.setAttribute('playsinline', true);
                        video.setAttribute('webkit-playsinline', true);
                        video.muted = true;
                        
                        // Ensure video plays on mobile
                        video.play().catch(e => {
                            console.log('Video play failed:', e);
                        });
                        
                        console.log('Camera initialized successfully');
                    })
                    .catch(function(error) {
                        console.error('Camera error:', error);
                        
                        // Try fallback constraints
                        const fallbackConstraints = {
                            video: {
                                facingMode: 'user',
                                width: { ideal: 320 },
                                height: { ideal: 240 }
                            }
                        };
                        
                        navigator.mediaDevices.getUserMedia(fallbackConstraints)
                            .then(function(mediaStream) {
                                stream = mediaStream;
                                video.srcObject = mediaStream;
                                video.play();
                                console.log('Camera initialized with fallback settings');
                            })
                            .catch(function(fallbackError) {
                                console.error('Fallback camera error:', fallbackError);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Akses Kamera Gagal',
                                    text: 'Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera dan refresh halaman.',
                                    confirmButtonColor: '#1a73e8',
                                    confirmButtonText: 'Refresh Halaman'
                                }).then(() => {
                                    window.location.reload();
                                });
                            });
                    });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Browser Tidak Mendukung',
                    text: 'Browser Anda tidak mendukung akses kamera.',
                    confirmButtonColor: '#1a73e8'
                });
            }
        }

        /**
         * Initialize Leaflet map and get user's location
         */
        function initMap() {
            console.log('Initializing map...');
            
            // Check if map container exists
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }
            
            // Immediately show default map - no waiting for GPS
            showDefaultMap();
            
            // Try to get user location in background and update map if successful
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        console.log('Got user location, updating map');
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        
                        // Update map with user location
                        if (map) {
                            map.setView([userLocation.lat, userLocation.lng], 17);
                            
                            // Add user marker
                            if (userMarker) {
                                map.removeLayer(userMarker);
                            }
                            userMarker = L.marker([userLocation.lat, userLocation.lng])
                                .addTo(map)
                                .bindPopup('Lokasi Anda')
                                .openPopup();
                        }
                    },
                    function(error) {
                        console.log('Geolocation failed, using default map');
                    },
                    {
                        enableHighAccuracy: false,
                        timeout: 5000,
                        maximumAge: 300000
                    }
                );
            }
        }

        /**
         * Show default map when geolocation fails
         */
        function showDefaultMap() {
            console.log('Showing default map...');
            const mapContainer = document.getElementById('map');
            if (!mapContainer) {
                console.error('Map container not found in showDefaultMap');
                return;
            }
            
            try {
                // Clear loading content
                mapContainer.innerHTML = '';
                
                // Default to Jakarta center
                let defaultLat = -6.2088;
                let defaultLng = 106.8456;
                let zoomLevel = 13;
                
                console.log('Creating map with Leaflet...');
                map = L.map('map').setView([defaultLat, defaultLng], zoomLevel);
                
                console.log('Adding tile layer...');
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                console.log('Adding marker...');
                L.marker([defaultLat, defaultLng])
                    .addTo(map)
                    .bindPopup('Peta berhasil dimuat!')
                    .openPopup();
                
                // Mark map as loaded
                mapContainer.classList.add('map-loaded');
                console.log('Map loaded successfully');
                
                // If we have cabang data, update with office location
                if (cabangData && cabangData.latitude && cabangData.longitude) {
                    console.log('Updating with office location...');
                    const officeLat = parseFloat(cabangData.latitude);
                    const officeLng = parseFloat(cabangData.longitude);
                    
                    map.setView([officeLat, officeLng], 17);
                    
                    L.marker([officeLat, officeLng])
                        .addTo(map)
                        .bindPopup(`${cabangData.nama_cabang || 'Kantor'}`)
                        .openPopup();
                    
                    // Add radius circle
                    addRadiusCircle();
                }
                
            } catch (error) {
                console.error('Error in showDefaultMap:', error);
                mapContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; flex-direction: column;"><i class="fas fa-exclamation-triangle" style="font-size: 24px; color: #ff6b6b; margin-bottom: 8px;"></i><span>Error: ' + error.message + '</span></div>';
            }
        }

        /**
         * Fetch work hours, branch location, and today's attendance data from the server
         */
        function getJamKerja() {
            fetch('{{ route("frontend.get-jam-kerja") }}', {
                method: 'GET',
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            })
            .then(response => response.json())
            .then(data => {
                jamKerjaData = data.jam_kerja;
                cabangData = data.cabang;
                presensiHariIni = data.presensi;

                console.log('Data presensi hari ini:', presensiHariIni);
                
                // Update UI with shift info
                if (jamKerjaData) {
                    document.getElementById('shift-info').textContent = jamKerjaData.nama_jam_kerja || 'Normal';
                    // Format jam tanpa detik (HH:mm)
                    document.getElementById('jam-mulai').textContent = formatTimeWithoutSeconds(jamKerjaData.awal_jam_masuk) || '00:00';
                    document.getElementById('jam-masuk').textContent = formatTimeWithoutSeconds(jamKerjaData.jam_masuk) || '00:00';
                    document.getElementById('jam-akhir').textContent = formatTimeWithoutSeconds(jamKerjaData.akhir_jam_masuk) || '00:00';
                    document.getElementById('jam-pulang').textContent = formatTimeWithoutSeconds(jamKerjaData.jam_pulang) || '00:00';
                }
                
                // Add radius circle to map if map is initialized
                if (map) {
                    addRadiusCircle();
                }

                // Determine button state based on server data (single source of truth)
                if (presensiHariIni && presensiHariIni.jam_in && !presensiHariIni.jam_out) {
                    // User has clocked in but not clocked out yet - show pulang button
                    isAbsenMasuk = false;
                    // Ensure jenis_presensi has a proper default value
                    let jenisPresensi = presensiHariIni.jenis_presensi || 'normal';
                    // Handle legacy data that might have null or empty jenis_presensi
                    if (!jenisPresensi || jenisPresensi === '' || jenisPresensi === null) {
                        jenisPresensi = 'normal';
                    }
                    updateButtonsForPulang(jenisPresensi);
                } else if (presensiHariIni && presensiHariIni.jam_in && presensiHariIni.jam_out) {
                    // User has completed attendance for today - show completed message
                    const buttonContainer = document.getElementById('btn-container');
                    while (buttonContainer.firstChild) {
                        buttonContainer.removeChild(buttonContainer.firstChild);
                    }
                    
                    const completedDiv = document.createElement('div');
                    completedDiv.style.textAlign = 'center';
                    completedDiv.style.padding = '20px';
                    completedDiv.style.color = '#28a745';
                    
                    const icon = document.createElement('i');
                    icon.className = 'fas fa-check-circle';
                    icon.style.fontSize = '24px';
                    icon.style.marginBottom = '8px';
                    
                    const text = document.createElement('br');
                    const textNode = document.createTextNode('Absensi hari ini sudah selesai');
                    
                    completedDiv.appendChild(icon);
                    completedDiv.appendChild(text);
                    completedDiv.appendChild(textNode);
                    buttonContainer.appendChild(completedDiv);
                } else {
                    // User has not clocked in yet - show masuk buttons
                    isAbsenMasuk = true;
                    updateButtonsForMasuk();
                }
                
                // Debug log untuk memastikan state yang benar
                console.log('Button state updated:', {
                    isAbsenMasuk: isAbsenMasuk,
                    jenisPresensi: presensiHariIni ? presensiHariIni.jenis_presensi : 'none',
                    jamIn: presensiHariIni ? presensiHariIni.jam_in : null,
                    jamOut: presensiHariIni ? presensiHariIni.jam_out : null,
                    fullPresensiData: presensiHariIni
                });
                
                // Debug log untuk button logic
                if (presensiHariIni && presensiHariIni.jam_in && !presensiHariIni.jam_out) {
                    console.log('Should show PULANG button for jenis:', presensiHariIni.jenis_presensi);
                } else if (!presensiHariIni || !presensiHariIni.jam_in) {
                    console.log('Should show MASUK buttons (Hadir, Onsite, WFH)');
                }
            })
            .catch(error => {
                console.error('Error fetching jam kerja:', error);
                // Silent fail - no alert to avoid interrupting user experience
                // Just log the error for debugging
            });
        }
        
        /**
         * The main function for clocking in/out
         */
        function takeAbsen() {
            // Reset modes for normal attendance
            isWfhMode = false;
            isOnsiteMode = false;
            
            if (!userLocation) {
                alert('Lokasi belum tersedia. Mohon tunggu sebentar dan pastikan GPS aktif.');
                return;
            }
            
            // Cek lokasi hanya jika bukan mode WFH
            if (!isWithinRadius && !isWfhMode) {
                alert('Anda berada di luar radius kantor. Tidak dapat melakukan absensi.');
                return;
            }
            
            // Validate clock-out time
            if (!isAbsenMasuk) {
                const now = moment().tz("Asia/Jakarta");
                const jamPulangStr = jamKerjaData.jam_pulang;
                if (!jamPulangStr) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Data jam pulang tidak tersedia.',
                        confirmButtonColor: '#1a73e8'
                    });
                    return;
                }
                const jamPulang = moment.tz(now.format('YYYY-MM-DD') + ' ' + jamPulangStr, 'YYYY-MM-DD HH:mm:ss', "Asia/Jakarta");
                
                if (now.isBefore(jamPulang)) {
                    // Show auto-disappearing alert without OK button
                    Swal.fire({
                        icon: 'warning',
                        title: 'Belum Waktunya Pulang',
                        text: 'Anda hanya dapat absen pulang setelah jam ' + jamPulangStr,
                        showConfirmButton: false,
                        timer: 1000,
                        timerProgressBar: true
                    });
                    return;
                }
            }
            
            // Capture image from camera
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvas.toDataURL('image/png');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Determine jenis presensi based on current mode
            let jenisPresensi = 'normal'; // default
            let statusPresensiIn = null;
            
            if (isWfhMode) {
                jenisPresensi = 'wfh';
                statusPresensiIn = 4; // Status 4 untuk WFH
            } else if (isOnsiteMode) {
                jenisPresensi = 'onsite';
                statusPresensiIn = 3; // Status 3 untuk Onsite
            }
            
            // Tambahkan log untuk debugging
            console.log('Mengirim data absensi:');
            console.log('Mode WFH:', isWfhMode);
            console.log('Mode Onsite:', isOnsiteMode);
            console.log('Status presensi:', statusPresensiIn);
            console.log('Jenis presensi:', jenisPresensi);
            console.log('Is Absen Masuk:', isAbsenMasuk);
            
            // Send attendance data to server with CSRF handling
            sendAttendanceRequest({
                foto: imageData,
                latitude: userLocation.lat,
                longitude: userLocation.lng,
                is_absen_masuk: isAbsenMasuk,
                status_presensi_in: statusPresensiIn,
                jenis_presensi: jenisPresensi
            });
        }
        
        /**
         * Update buttons for check-out (pulang) based on attendance type
         */
        function updateButtonsForPulang(jenisPresensi) {
            console.log('updateButtonsForPulang called with jenisPresensi:', jenisPresensi);
            
            // Clear buttons safely without using innerHTML
            const buttonContainer = document.getElementById('btn-container');
            while (buttonContainer.firstChild) {
                buttonContainer.removeChild(buttonContainer.firstChild);
            }
            
            // Create buttons based on jenis_presensi
            if (jenisPresensi === 'wfh') {
                // Create WFH Pulang button
                const wfhPulangBtn = document.createElement('button');
                wfhPulangBtn.id = 'btn-wfh-pulang';
                wfhPulangBtn.className = 'btn btn-purple wfh-pulang-button';
                
                const wfhIcon = document.createElement('i');
                wfhIcon.className = 'fas fa-home';
                const wfhText = document.createTextNode(' Pulang WFH');
                wfhPulangBtn.appendChild(wfhIcon);
                wfhPulangBtn.appendChild(wfhText);
                
                wfhPulangBtn.addEventListener('click', function() {
                    isWfhMode = true;
                    isOnsiteMode = false;
                    takeAbsen();
                });
                buttonContainer.appendChild(wfhPulangBtn);
            } else if (jenisPresensi === 'onsite') {
                // Create Onsite Pulang button
                const onsitePulangBtn = document.createElement('button');
                onsitePulangBtn.id = 'btn-onsite-pulang';
                onsitePulangBtn.className = 'btn btn-light-blue onsite-pulang-button';
                
                const onsiteIcon = document.createElement('i');
                onsiteIcon.className = 'fas fa-building';
                const onsiteText = document.createTextNode(' Pulang Onsite');
                onsitePulangBtn.appendChild(onsiteIcon);
                onsitePulangBtn.appendChild(onsiteText);
                
                onsitePulangBtn.addEventListener('click', function() {
                    isOnsiteMode = true;
                    isWfhMode = false;
                    takeAbsen();
                });
                buttonContainer.appendChild(onsitePulangBtn);
            } else {
                // Create regular Pulang button (for 'normal' or any other type)
                const pulangBtn = document.createElement('button');
                pulangBtn.id = 'btn-absen-pulang';
                pulangBtn.className = 'btn btn-primary absen-pulang-button';
                
                const pulangIcon = document.createElement('i');
                pulangIcon.className = 'fas fa-sign-out-alt';
                const pulangText = document.createTextNode(' Pulang');
                pulangBtn.appendChild(pulangIcon);
                pulangBtn.appendChild(pulangText);
                
                pulangBtn.addEventListener('click', function() {
                    isWfhMode = false;
                    isOnsiteMode = false;
                    takeAbsen();
                });
                buttonContainer.appendChild(pulangBtn);
            }
            
            // Update global state
            isAbsenMasuk = false;
        }
        
        /**
         * Update buttons for check-in (masuk)
         */
        function updateButtonsForMasuk() {
            // Clear buttons safely without using innerHTML
            const buttonContainer = document.getElementById('btn-container');
            while (buttonContainer.firstChild) {
                buttonContainer.removeChild(buttonContainer.firstChild);
            }
            
            // Create Hadir button
            const absenBtn = document.createElement('button');
            absenBtn.id = 'btn-absen';
            absenBtn.className = 'btn btn-primary absen-button';
            
            const hadirIcon = document.createElement('i');
            hadirIcon.className = 'fas fa-sign-in-alt';
            const hadirText = document.createTextNode(' Hadir');
            absenBtn.appendChild(hadirIcon);
            absenBtn.appendChild(hadirText);
            
            absenBtn.addEventListener('click', takeAbsen);
            buttonContainer.appendChild(absenBtn);
            
            // Create Onsite button
            const onsiteBtn = document.createElement('button');
            onsiteBtn.id = 'btn-onsite';
            onsiteBtn.className = 'btn btn-light-blue onsite-button';
            
            const onsiteIcon = document.createElement('i');
            onsiteIcon.className = 'fas fa-building';
            const onsiteText = document.createTextNode(' Onsite');
            onsiteBtn.appendChild(onsiteIcon);
            onsiteBtn.appendChild(onsiteText);
            
            onsiteBtn.addEventListener('click', takeAbsenOnsite);
            buttonContainer.appendChild(onsiteBtn);
            
            // Create WFH button
            const wfhBtn = document.createElement('button');
            wfhBtn.id = 'btn-wfh';
            wfhBtn.className = 'btn btn-purple wfh-button';
            
            const wfhIcon = document.createElement('i');
            wfhIcon.className = 'fas fa-home';
            const wfhText = document.createTextNode(' WFH');
            wfhBtn.appendChild(wfhIcon);
            wfhBtn.appendChild(wfhText);
            
            wfhBtn.addEventListener('click', takeAbsenWfh);
            buttonContainer.appendChild(wfhBtn);
            
            // Update global state
            isAbsenMasuk = true;
            isWfhMode = false;
        }
        
        /**
         * Add the office radius circle to the map
         */
        function addRadiusCircle() {
            if (map && cabangData && cabangData.lokasi && cabangData.radius) {
                const lokasi = cabangData.lokasi.split(',');
                const lat = parseFloat(lokasi[0]);
                const lng = parseFloat(lokasi[1]);
                const radius = parseInt(cabangData.radius);
                
                if (radiusCircle) {
                    map.removeLayer(radiusCircle);
                }
                
                radiusCircle = L.circle([lat, lng], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.3,
                    radius: radius
                }).addTo(map);
                
                // Center map on office location
                // map.setView([lat, lng], 17);
                
                checkUserInRadius();
            }
        }

        /**
         * Check if the user is within the office radius
         */
        function checkUserInRadius() {
            if (userLocation && cabangData && cabangData.lokasi && cabangData.radius) {
                const lokasi = cabangData.lokasi.split(',');
                const cabangLat = parseFloat(lokasi[0]);
                const cabangLng = parseFloat(lokasi[1]);
                const radius = parseInt(cabangData.radius);
                
                const distance = calculateDistance(
                    userLocation.lat, userLocation.lng,
                    cabangLat, cabangLng
                );
                
                isWithinRadius = distance <= radius;
                
                // Jika mode WFH aktif, abaikan pengecekan radius
                if (!isWithinRadius && !isWfhMode) {
                    locationAlert.style.display = 'block';
                    btnAbsen.classList.add('disabled');
                } else {
                    locationAlert.style.display = 'none';
                    btnAbsen.classList.remove('disabled');
                }
            }
        }
        
        /**
         * Update the digital clock
         */
        function updateClock() {
            const now = moment().tz("Asia/Jakarta");
            moment.locale('id'); // Set locale to Indonesian
            const timeString = now.format('HH:mm:ss');
            const dateString = now.format('DD MMM YYYY');
            
            const currentTimeElement = document.getElementById('current-time');
            if (currentTimeElement) {
                currentTimeElement.textContent = dateString + ' ' + timeString;
            }
        }

        /**
         * Format time to HH:mm (remove seconds)
         */
        function formatTimeWithoutSeconds(time) {
            if (!time) return '00:00';
            // If time is in HH:mm:ss format, remove seconds
            const parts = time.split(':');
            if (parts.length >= 2) {
                return parts[0] + ':' + parts[1];
            }
            return time;
        }
        
        /**
         * Send attendance request with CSRF handling and retry logic
         */
        async function sendAttendanceRequest(data, retryCount = 0) {
            const maxRetries = 2;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const response = await fetch('{{ route("frontend.absen.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.status === 419 && retryCount < maxRetries) {
                    // CSRF token expired, refresh and retry
                    console.log('CSRF token expired, refreshing...');
                    await refreshCSRFToken();
                    return sendAttendanceRequest(data, retryCount + 1);
                }
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        confirmButtonColor: '#1a73e8',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Clear localStorage to force refresh of state
                        localStorage.removeItem('isAbsenMasuk');
                        // Redirect to dashboard on success
                        window.location.href = '{{ route("frontend.dashboard") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: result.message,
                        confirmButtonColor: '#1a73e8'
                    });
                }
            } catch (error) {
                console.error('Error taking absen:', error);
                if (retryCount < maxRetries) {
                    console.log('Retrying attendance request...');
                    await refreshCSRFToken();
                    return sendAttendanceRequest(data, retryCount + 1);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Terjadi kesalahan saat melakukan absensi. Silakan coba lagi.',
                        confirmButtonColor: '#1a73e8'
                    });
                }
            }
        }
        
        /**
         * Refresh CSRF token from server
         */
        async function refreshCSRFToken() {
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
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    console.log('CSRF token refreshed successfully');
                } else {
                    throw new Error('Failed to refresh CSRF token');
                }
            } catch (error) {
                console.error('Error refreshing CSRF token:', error);
                throw error;
            }
        }

        /**
         * Calculate distance between two coordinates using Haversine formula
         * @returns distance in meters
         */
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Earth radius in meters
            const φ1 = lat1 * Math.PI / 180;
            const φ2 = lat2 * Math.PI / 180;
            const Δφ = (lat2 - lat1) * Math.PI / 180;
            const Δλ = (lon2 - lon1) * Math.PI / 180;
            
            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                      Math.cos(φ1) * Math.cos(φ2) *
                      Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            
            return R * c;
        }

    </script>
    
    {{-- CSRF Handler for WebView --}}
    <script src="{{ asset('js/csrf-handler.js') }}"></script>
    
    {{-- Mobile Optimizations --}}
    <script src="{{ asset('js/mobile-optimizations.js') }}"></script>
    
    {{-- Frontend Security --}}
    <script src="{{ asset('js/frontend-security.js') }}"></script>
</body>
</html>