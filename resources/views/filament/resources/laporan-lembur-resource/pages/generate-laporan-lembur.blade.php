<x-filament-panels::page>
    <form wire:submit="generatePreview">
        {{ $this->form }}
        
        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" color="primary">
                <x-heroicon-o-eye class="w-5 h-5 mr-2" />
                Preview Laporan
            </x-filament::button>
            
            @if($lemburData && $lemburData->count() > 0)
                <x-filament::button wire:click="downloadPdf" color="success">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                    Download PDF
                </x-filament::button>
                
                <x-filament::button wire:click="downloadExcel" color="info">
                    <x-heroicon-o-table-cells class="w-5 h-5 mr-2" />
                    Download Excel
                </x-filament::button>
            @endif
        </div>
    </form>

    @if($lemburData !== null)
        <div class="mt-8">
            <x-filament::section>
                <x-slot name="heading">
                    Preview Laporan Lembur
                </x-slot>

                @if($lemburData->count() > 0)
                    <div class="bg-white p-8 rounded-lg shadow-sm border">
                        <!-- Header Laporan -->
                        <div class="text-center mb-6">
                            <h1 class="text-2xl font-bold mb-2">LAPORAN LEMBUR</h1>
                            <p class="text-lg">
                                Periode: 
                                @php
                                    $bulanNama = [
                                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                                    ];
                                @endphp
                                {{ $bulanNama[$selectedMonth] }} {{ $selectedYear }}
                            </p>
                        </div>

                        <!-- Info Karyawan -->
                        <div class="mb-6">
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-1 w-32 font-semibold">Nama</td>
                                    <td class="py-1 w-4">:</td>
                                    <td class="py-1">{{ $selectedKaryawan->nama }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-semibold">NIK</td>
                                    <td class="py-1">:</td>
                                    <td class="py-1">{{ $selectedKaryawan->nik }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-semibold">Jabatan</td>
                                    <td class="py-1">:</td>
                                    <td class="py-1">{{ $selectedKaryawan->jabatan }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-semibold">Department</td>
                                    <td class="py-1">:</td>
                                    <td class="py-1">{{ $selectedKaryawan->department->nama_dept ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Tabel Detail Lembur -->
                        <div class="mb-6">
                            <h3 class="font-bold text-lg mb-3">RINCIAN LEMBUR</h3>
                            <table class="w-full border-collapse border border-gray-400">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-400 px-3 py-2 text-center">No.</th>
                                        <th class="border border-gray-400 px-3 py-2">Tanggal Awal</th>
                                        <th class="border border-gray-400 px-3 py-2">Tanggal Akhir</th>
                                        <th class="border border-gray-400 px-3 py-2">Lembaga</th>
                                        <th class="border border-gray-400 px-3 py-2">Keterangan</th>
                                        <th class="border border-gray-400 px-3 py-2 text-center">Jumlah Hari</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalHari = 0;
                                    @endphp
                                    @foreach($lemburData as $index => $lembur)
                                        @php
                                            $jumlahHari = $lembur->tanggal_awal_lembur->diffInDays($lembur->tanggal_akhir_lembur) + 1;
                                            $totalHari += $jumlahHari;
                                        @endphp
                                        <tr>
                                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $index + 1 }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $lembur->tanggal_awal_lembur->format('d/m/Y') }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $lembur->tanggal_akhir_lembur->format('d/m/Y') }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $lembur->nama_lembaga }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $lembur->keterangan }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $jumlahHari }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-blue-50 font-bold">
                                        <td colspan="5" class="border border-gray-400 px-3 py-2 text-center">TOTAL</td>
                                        <td class="border border-gray-400 px-3 py-2 text-center">{{ $totalHari }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer -->
                        <div class="mt-8 flex justify-end">
                            <div class="text-center">
                                <p class="mb-16">Pekalongan, {{ now()->format('d F Y') }}</p>
                                <p class="font-bold border-t border-gray-800 pt-2">
                                    Zaki Muttaqien
                                </p>
                                <p class="text-sm">PT. USSI BATIK</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <x-heroicon-o-document-text class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                        <p class="text-gray-500 text-lg">Tidak ada data lembur untuk periode yang dipilih</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
