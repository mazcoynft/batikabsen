<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Presensi;
use App\Models\Cabang;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class LokasiPresensi extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    
    protected static ?string $navigationLabel = 'Lokasi Presensi';
    
    protected static ?string $navigationGroup = 'Presensi';
    
    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.lokasi-presensi';

    public function getMapData(): array
    {
        // Ambil data presensi hari ini dengan lokasi
        $presensiData = Presensi::with(['karyawan'])
            ->whereNotNull('lokasi_in')
            ->whereDate('tgl_presensi', now()->format('Y-m-d'))
            ->get();

        $locations = [];
        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'];
        $colorIndex = 0;

        foreach ($presensiData as $presensi) {
            if ($presensi->lokasi_in) {
                $coords = explode(',', $presensi->lokasi_in);
                if (count($coords) === 2) {
                    $lat = (float) trim($coords[0]);
                    $lng = (float) trim($coords[1]);
                    
                    // Assign warna unik per karyawan
                    $karyawanId = $presensi->karyawan_id;
                    if (!isset($locations[$karyawanId])) {
                        $locations[$karyawanId] = [
                            'karyawan' => $presensi->karyawan,
                            'color' => $colors[$colorIndex % count($colors)],
                            'markers' => []
                        ];
                        $colorIndex++;
                    }
                    
                    $locations[$karyawanId]['markers'][] = [
                        'lat' => $lat,
                        'lng' => $lng,
                        'type' => 'masuk',
                        'jam' => $presensi->jam_in ? Carbon::parse($presensi->jam_in)->format('H:i') : '-',
                        'status' => $presensi->status,
                        'keterangan' => $presensi->keterangan ?: '-'
                    ];
                    
                    // Tambahkan marker pulang jika ada
                    if ($presensi->lokasi_out) {
                        $coordsOut = explode(',', $presensi->lokasi_out);
                        if (count($coordsOut) === 2) {
                            $latOut = (float) trim($coordsOut[0]);
                            $lngOut = (float) trim($coordsOut[1]);
                            
                            $locations[$karyawanId]['markers'][] = [
                                'lat' => $latOut,
                                'lng' => $lngOut,
                                'type' => 'pulang',
                                'jam' => $presensi->jam_out ? Carbon::parse($presensi->jam_out)->format('H:i') : '-',
                                'status' => $presensi->status,
                                'keterangan' => $presensi->keterangan ?: '-'
                            ];
                        }
                    }
                }
            }
        }

        return $locations;
    }

    public function getOfficeData(): array
    {
        // Ambil data semua cabang/kantor
        $offices = Cabang::all();
        $officeData = [];

        foreach ($offices as $office) {
            if ($office->lokasi) {
                $coords = explode(',', $office->lokasi);
                if (count($coords) === 2) {
                    $lat = (float) trim($coords[0]);
                    $lng = (float) trim($coords[1]);
                    
                    $officeData[] = [
                        'nama' => $office->nama_cabang,
                        'kode' => $office->kode_cabang,
                        'lat' => $lat,
                        'lng' => $lng,
                        'radius' => (int) $office->radius
                    ];
                }
            }
        }

        return $officeData;
    }

    public function getExtraBodyAttributes(): array
    {
        return [
            'onload' => new HtmlString('initLokasiPresensi()'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'mapScript' => $this->getMapScript(),
            'mapStyles' => $this->getMapStyles(),
        ];
    }

    private function getMapScript(): HtmlString
    {
        $mapData = json_encode($this->getMapData());
        $officeData = json_encode($this->getOfficeData());
        
        return new HtmlString("
        <script>
        function initLokasiPresensi() {
            if (!document.querySelector('link[href*=\"leaflet\"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }
            
            if (typeof L === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = function() { setupMap($mapData, $officeData); };
                document.head.appendChild(script);
            } else {
                setupMap($mapData, $officeData);
            }
        }
        
        function setupMap(presensiData, officeData) {
            if (typeof L === 'undefined') {
                setTimeout(function() { setupMap(presensiData, officeData); }, 100);
                return;
            }

            const map = L.map('map').setView([-6.2088, 106.8456], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            const legend = document.getElementById('legend');
            legend.innerHTML = '';

            // Tambahkan marker dan radius untuk kantor/cabang
            if (officeData && officeData.length > 0) {
                const officeLegend = document.createElement('div');
                officeLegend.className = 'mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200';
                officeLegend.innerHTML = '<h4 class=\"text-sm font-semibold text-blue-800 mb-2\">📍 Lokasi Kantor</h4>';
                
                officeData.forEach(office => {
                    // Marker kantor dengan icon khusus
                    const officeMarker = L.marker([office.lat, office.lng], {
                        icon: L.divIcon({
                            className: 'office-marker',
                            html: '<div style=\"background-color: #1e40af; width: 30px; height: 30px; border-radius: 50%; border: 4px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; color: white;\">🏢</div>',
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    }).addTo(map);

                    // Circle radius
                    const circle = L.circle([office.lat, office.lng], {
                        color: '#1e40af',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.1,
                        radius: office.radius
                    }).addTo(map);

                    // Popup untuk kantor
                    officeMarker.bindPopup('<div style=\"min-width: 200px;\"><h4 style=\"margin: 0 0 8px 0; font-weight: bold; color: #1e40af;\">🏢 ' + office.nama + '</h4><div style=\"font-size: 12px; line-height: 1.4;\"><div><strong>Kode:</strong> ' + office.kode + '</div><div><strong>Radius:</strong> ' + office.radius + ' meter</div><div><strong>Koordinat:</strong> ' + office.lat + ', ' + office.lng + '</div></div></div>');

                    // Tambahkan ke legend
                    const officeItem = document.createElement('div');
                    officeItem.className = 'flex items-center space-x-2 text-xs';
                    officeItem.innerHTML = '<div class=\"w-3 h-3 rounded-full bg-blue-600\"></div><span class=\"font-medium text-blue-800\">' + office.nama + '</span><span class=\"text-blue-600\">(' + office.radius + 'm)</span>';
                    officeLegend.appendChild(officeItem);
                });
                
                legend.appendChild(officeLegend);
            }

            // Tambahkan legend untuk karyawan
            if (Object.keys(presensiData).length > 0) {
                const employeeLegend = document.createElement('div');
                employeeLegend.className = 'p-3 bg-gray-50 rounded-lg border border-gray-200';
                employeeLegend.innerHTML = '<h4 class=\"text-sm font-semibold text-gray-800 mb-2\">👥 Presensi Karyawan</h4>';

                Object.keys(presensiData).forEach(karyawanId => {
                    const data = presensiData[karyawanId];
                    const karyawan = data.karyawan;
                    const color = data.color;
                    const markers = data.markers;

                    const legendItem = document.createElement('div');
                    legendItem.className = 'flex items-center space-x-2 text-xs';
                    legendItem.innerHTML = '<div class=\"w-3 h-3 rounded-full\" style=\"background-color: ' + color + '\"></div><span class=\"font-medium\">' + karyawan.nama + '</span><span class=\"text-gray-500\">(' + karyawan.nik + ')</span>';
                    employeeLegend.appendChild(legendItem);

                    markers.forEach(marker => {
                        const markerElement = L.marker([marker.lat, marker.lng], {
                            icon: L.divIcon({
                                className: 'custom-marker',
                                html: '<div style=\"background-color: ' + color + '; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: bold; color: white;\">' + (marker.type === 'masuk' ? 'M' : 'P') + '</div>',
                                iconSize: [20, 20],
                                iconAnchor: [10, 10]
                            })
                        }).addTo(map);

                        const statusText = {'h': 'Hadir', 's': 'Sakit', 'i': 'Izin', 'c': 'Cuti'}[marker.status] || 'Unknown';
                        markerElement.bindPopup('<div style=\"min-width: 200px;\"><h4 style=\"margin: 0 0 8px 0; font-weight: bold; color: ' + color + ';\">' + karyawan.nama + '</h4><div style=\"font-size: 12px; line-height: 1.4;\"><div><strong>NIK:</strong> ' + karyawan.nik + '</div><div><strong>Tipe:</strong> ' + (marker.type === 'masuk' ? 'Absen Masuk' : 'Absen Pulang') + '</div><div><strong>Jam:</strong> ' + marker.jam + '</div><div><strong>Status:</strong> ' + statusText + '</div><div><strong>Keterangan:</strong> ' + marker.keterangan + '</div></div></div>');
                    });
                });

                legend.appendChild(employeeLegend);
            } else {
                const noDataLegend = document.createElement('div');
                noDataLegend.className = 'p-3 bg-gray-50 rounded-lg border border-gray-200';
                noDataLegend.innerHTML = '<p class=\"text-gray-500 text-sm\">Tidak ada data presensi hari ini</p>';
                legend.appendChild(noDataLegend);
            }

            // Auto fit bounds untuk semua marker
            const allPoints = [];
            
            // Tambahkan office locations
            if (officeData && officeData.length > 0) {
                officeData.forEach(office => {
                    allPoints.push([office.lat, office.lng]);
                });
            }
            
            // Tambahkan presensi markers
            const allMarkers = Object.values(presensiData).flatMap(data => data.markers);
            allMarkers.forEach(marker => {
                allPoints.push([marker.lat, marker.lng]);
            });

            if (allPoints.length > 0) {
                const group = new L.featureGroup();
                allPoints.forEach(point => {
                    L.marker(point).addTo(group);
                });
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
        </script>
        ");
    }

    private function getMapStyles(): HtmlString
    {
        return new HtmlString("
        <style>
        .custom-marker, .office-marker {
            background: transparent !important;
            border: none !important;
        }
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
        }
        .leaflet-popup-content {
            margin: 12px;
        }
        #legend {
            max-height: 400px;
            overflow-y: auto;
        }
        #legend::-webkit-scrollbar {
            width: 4px;
        }
        #legend::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }
        #legend::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }
        #legend::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        </style>
        ");
    }
}
