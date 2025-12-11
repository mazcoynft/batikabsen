<!-- Load Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div x-data="{
    showModal: false,
    mapInstance: null,
    currentLokasi: null,
    currentNama: null,
    
    openMapModal(lokasi, namaKaryawan) {
        console.log('Opening map for:', namaKaryawan, 'at location:', lokasi);
        
        if (!lokasi || lokasi === '-' || lokasi.trim() === '') {
            alert('Lokasi tidak tersedia untuk karyawan ini');
            return;
        }
        
        this.currentLokasi = lokasi.trim();
        this.currentNama = namaKaryawan;
        this.showModal = true;
        
        this.$nextTick(() => {
            setTimeout(() => {
                this.initializeMap();
            }, 100);
        });
    },
    
    initializeMap() {
        // Clean up existing map
        if (this.mapInstance) {
            try {
                this.mapInstance.remove();
            } catch (e) {
                console.warn('Error removing existing map:', e);
            }
            this.mapInstance = null;
        }
        
        // Validate requirements
        if (!this.currentLokasi || !this.$refs.map) {
            console.error('No location or map reference available');
            alert('Error: Map container tidak tersedia');
            return;
        }
        
        try {
            // Parse koordinat (format: lat,lon)
            const coords = this.currentLokasi.split(',');
            if (coords.length !== 2) {
                throw new Error('Format koordinat tidak valid. Harus berupa: lat,lon');
            }
            
            const lat = parseFloat(coords[0].trim());
            const lon = parseFloat(coords[1].trim());
            
            if (isNaN(lat) || isNaN(lon)) {
                throw new Error('Nilai koordinat tidak valid');
            }
            
            if (lat < -90 || lat > 90 || lon < -180 || lon > 180) {
                throw new Error('Koordinat di luar jangkauan yang valid');
            }
            
            console.log('Initializing map at:', lat, lon);
            
            // Initialize map
            this.mapInstance = L.map(this.$refs.map, {
                center: [lat, lon],
                zoom: 16,
                zoomControl: true,
                scrollWheelZoom: true
            });
            
            // Add tile layer with error handling
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE4IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+VGlsZSBub3QgZm91bmQ8L3RleHQ+PC9zdmc+'
            });
            
            tileLayer.addTo(this.mapInstance);
            
            // Add marker
            const marker = L.marker([lat, lon]).addTo(this.mapInstance);
            
            const popupContent = `
                <div style="text-align: center; min-width: 200px;">
                    <strong>${this.currentNama}</strong><br>
                    <small>Lokasi Presensi</small><br>
                    <small>Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}</small>
                </div>
            `;
            
            marker.bindPopup(popupContent).openPopup();
            
            // Ensure map renders properly
            setTimeout(() => {
                if (this.mapInstance) {
                    try {
                        this.mapInstance.invalidateSize();
                    } catch (e) {
                        console.warn('Error invalidating map size:', e);
                    }
                }
            }, 300);
            
        } catch (e) {
            console.error('Error initializing map:', e);
            alert('Gagal memuat peta: ' + e.message);
            this.closeModal();
        }
    },
    
    closeModal() {
        this.showModal = false;
        if (this.mapInstance) {
            this.mapInstance.remove();
            this.mapInstance = null;
        }
        this.currentLokasi = null;
        this.currentNama = null;
    }
}" x-init="console.log('Lokasi component initialized')">

    @if($getRecord()->lokasi_in && $getRecord()->lokasi_in !== '-')
        <button 
            @click="openMapModal('{{ $getRecord()->lokasi_in }}', '{{ str_replace("'", "\\'", $getRecord()->karyawan->nama ?? 'Unknown') }}')" 
            class="inline-flex items-center justify-center w-8 h-8 text-blue-600 hover:bg-blue-100 rounded-full transition-colors duration-200"
            title="Lihat Lokasi Presensi"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
        </button>
    @else
        <span class="text-gray-400 text-sm">-</span>
    @endif

    <!-- Modal -->
    <div 
        x-show="showModal" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeModal()" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        style="display: none;"
    >
        <div 
            @click.away="closeModal()" 
            class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
        >
            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Lokasi Presensi</h3>
                    <p class="text-sm text-gray-600" x-text="currentNama"></p>
                </div>
                <button 
                    @click="closeModal()" 
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Map Container -->
            <div class="p-6">
                <div x-ref="map" class="w-full h-96 rounded-lg border"></div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t">
                <p class="text-xs text-gray-500">
                    Koordinat: <span x-text="currentLokasi"></span>
                </p>
            </div>
        </div>
    </div>
</div>