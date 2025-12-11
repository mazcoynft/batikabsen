{!! $mapStyles !!}

<x-filament-panels::page>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Peta Lokasi Presensi Hari Ini</h3>
        <p class="text-sm text-gray-600 mb-4">Menampilkan lokasi absen masuk dan pulang karyawan</p>
        <div id="map" style="height: 600px; width: 100%; border-radius: 8px; background: #f3f4f6;"></div>
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Keterangan</h4>
            <div id="legend" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <p class="text-gray-500">Loading...</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>

{!! $mapScript !!}