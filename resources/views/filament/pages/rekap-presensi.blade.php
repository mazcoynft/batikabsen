<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Rekap Presensi</h2>
            
            <form wire:submit="submit">
                {{ $this->form }}
            </form>
            
            <div class="flex space-x-4 mt-6">
                <x-filament::button
                    wire:click="cetakPDF"
                    icon="heroicon-o-printer"
                    color="primary"
                >
                    Cetak
                </x-filament::button>
                
                <x-filament::button
                    wire:click="exportExcel"
                    icon="heroicon-o-arrow-down-tray"
                    color="success"
                >
                    Export to Excel
                </x-filament::button>
            </div>
        </div>
    </div>
</x-filament-panels::page>