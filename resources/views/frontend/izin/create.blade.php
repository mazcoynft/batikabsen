<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengajuan Izin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #sisa-cuti-info {
            display: none;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- App Header -->
    <div class="fixed inset-x-0 top-0 z-10 bg-blue-500 shadow-md">
        <div class="flex items-center justify-between p-4 text-white">
            <h1 class="text-lg font-semibold">Form Pengajuan Izin</h1>
        </div>
    </div>
    <!-- * App Header -->


    <!-- App Capsule -->
    <div id="appCapsule" class="pt-16 pb-24">
        <div class="p-4">
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        successAlert("{{ session('success') }}");
                    });
                </script>
            @endif
            @if (session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        errorAlert("{{ session('error') }}");
                    });
                </script>
            @endif

            <div class="section full mt-2">
                <div class="section-title">Ajukan Izin</div>
                <div class="wide-block">
                    <form action="{{ route('frontend.izin.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="cuti_id">Jenis Izin</label>
                            <select name="cuti_id" id="cuti_id" class="form-control" required>
                                <option value="">Pilih Jenis Izin</option>
                                @foreach ($jenisCuti as $cuti)
                                    <option value="{{ $cuti->id }}" data-jenis="{{ $cuti->jenis_cuti }}">
                                        {{ $cuti->nama_cuti }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_awal">Tanggal Mulai</label>
                            <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_akhir">Tanggal Selesai</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <p>Sisa Cuti: <span id="sisa_cuti_text">Pilih jenis izin</span></p>
                            <p>Jumlah Hari Izin: <span id="jumlah_hari_text">Pilih tanggal</span></p>
                        </div>
                        <div class="form-group">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" rows="4" required
                                class="block w-full px-3 py-2 mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                        </div>

                        <div>
                            <label for="file_pendukung" class="block text-sm font-medium text-gray-700">File Pendukung (Opsional)</label>
                            <input type="file" id="file_pendukung" name="file_pendukung"
                                class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-500">JPG, PNG, PDF. Maks 2MB.</p>
                        </div>

                        <div>
                            <button type="submit" id="submit-button"
                                class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Ajukan Izin
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- * App Capsule -->


    <!-- App Bottom Menu -->
    <div class="fixed inset-x-0 bottom-0 z-10 bg-white border-t border-gray-200 shadow-lg appBottomMenu">
        <div class="flex justify-around">
            <a href="{{ route('frontend.dashboard') }}" class="flex flex-col items-center justify-center flex-1 p-2 text-center text-gray-600">
                <ion-icon name="home-outline" class="text-2xl"></ion-icon>
                <span class="text-xs">Home</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center flex-1 p-2 text-center text-gray-600">
                <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                <span class="text-xs">Riwayat</span>
            </a>
            <a href="{{ route('frontend.presensi.store') }}" class="flex flex-col items-center justify-center flex-1 p-1 text-center text-gray-600" id="presensi-button">
                <div class="flex items-center justify-center w-16 h-16 text-white bg-blue-500 rounded-full shadow-lg" style="margin-top: -20px;">
                    <ion-icon name="camera-outline" class="text-4xl"></ion-icon>
                </div>
            </a>
            <a href="{{ route('frontend.izin.index') }}" class="flex flex-col items-center justify-center flex-1 p-2 text-center text-blue-500">
                <ion-icon name="document-attach-outline" class="text-2xl"></ion-icon>
                <span class="text-xs">Izin</span>
            </a>
            <a href="#" class="flex flex-col items-center justify-center flex-1 p-2 text-center text-gray-600">
                <ion-icon name="person-outline" class="text-2xl"></ion-icon>
                <span class="text-xs">Profil</span>
            </a>
        </div>
    </div>
    <!-- * App Bottom Menu -->

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cutiSelect = document.getElementById('cuti_id');
            const tanggalAwalInput = document.getElementById('tanggal_awal');
            const tanggalAkhirInput = document.getElementById('tanggal_akhir');
            const sisaCutiText = document.getElementById('sisa_cuti_text');
            const jumlahHariText = document.getElementById('jumlah_hari_text');
            const submitButton = document.querySelector('button[type="submit"]');

            const hariLibur = @json($hari_libur);
            let sisaCuti = null;
            let potongCuti = false;

            function calculateWorkingDays() {
                const tanggalAwal = tanggalAwalInput.value;
                const tanggalAkhir = tanggalAkhirInput.value;

                if (!tanggalAwal || !tanggalAkhir) {
                    jumlahHariText.textContent = 'Pilih tanggal';
                    return;
                }

                let startDate = new Date(tanggalAwal);
                let endDate = new Date(tanggalAkhir);

                if (startDate > endDate) {
                    jumlahHariText.textContent = 'Tanggal akhir tidak valid';
                    submitButton.disabled = true;
                    return;
                }

                let count = 0;
                const curDate = new Date(startDate.getTime());
                while (curDate <= endDate) {
                    const dayOfWeek = curDate.getDay();
                    // 0 = Sunday, 6 = Saturday
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                        // Check if it's a public holiday
                        const formattedDate = curDate.toISOString().split('T')[0];
                        if (!hariLibur.includes(formattedDate)) {
                            count++;
                        }
                    }
                    curDate.setDate(curDate.getDate() + 1);
                }
                
                jumlahHariText.textContent = `${count} hari`;

                if (potongCuti && sisaCuti !== null && count > sisaCuti) {
                    jumlahHariText.innerHTML += ' <span class="text-danger">(Melebihi sisa cuti!)</span>';
                    submitButton.disabled = true;
                } else {
                    submitButton.disabled = false;
                }
            }

            cutiSelect.addEventListener('change', function () {
                const cutiId = this.value;
                if (!cutiId) {
                    sisaCutiText.textContent = 'Pilih jenis izin';
                    potongCuti = false;
                    sisaCuti = null;
                    calculateWorkingDays();
                    return;
                }

                fetch(`/izin/get-cuti-details/${cutiId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            sisaCutiText.textContent = 'Error';
                            console.error(data.error);
                            return;
                        }
                        
                        potongCuti = data.potong_cuti;
                        if (data.potong_cuti) {
                            sisaCuti = data.sisa_cuti;
                            sisaCutiText.textContent = `${sisaCuti} hari`;
                        } else {
                            sisaCuti = null;
                            sisaCutiText.textContent = 'Tidak memotong cuti';
                        }
                        calculateWorkingDays();
                    })
                    .catch(error => {
                        console.error('Error fetching cuti details:', error);
                        sisaCutiText.textContent = 'Gagal memuat';
                    });
            });

            tanggalAwalInput.addEventListener('change', calculateWorkingDays);
            tanggalAkhirInput.addEventListener('change', calculateWorkingDays);
        });
        // Tambahkan konfirmasi sebelum submit
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (await confirmAlert('Konfirmasi Pengajuan', 'Apakah Anda yakin ingin mengajukan izin ini?')) {
                this.submit();
            }
        });
    </script>

</body>

</html>