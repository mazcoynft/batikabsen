<x-filament-panels::page>
    <form wire:submit="generatePreview">
        {{ $this->form }}
        
        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit" color="primary">
                <x-heroicon-o-eye class="w-5 h-5 mr-2" />
                Preview Laporan
            </x-filament::button>
            
            @if($piketData && $piketData->count() > 0)
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

    @if($piketData !== null)
        <div class="mt-8">
            <x-filament::section>
                <x-slot name="heading">
                    Preview Laporan Piket
                </x-slot>

                @if($piketData->count() > 0)
                    <div class="bg-white p-8 rounded-lg shadow-sm border">
                        <!-- Header Laporan -->
                        <div class="text-center mb-6">
                            <h1 class="text-2xl font-bold mb-2">LAPORAN PIKET</h1>
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

                        <!-- Tabel Detail Piket -->
                        <div class="mb-6">
                            <h3 class="font-bold text-lg mb-3">RINCIAN PERHITUNGAN PETUGAS</h3>
                            <table class="w-full border-collapse border border-gray-400">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-400 px-3 py-2 text-center">No.</th>
                                        <th class="border border-gray-400 px-3 py-2">Tanggal</th>
                                        <th class="border border-gray-400 px-3 py-2">Jenis Piket</th>
                                        <th class="border border-gray-400 px-3 py-2">Lembaga</th>
                                        <th class="border border-gray-400 px-3 py-2 text-center">QTY Hari</th>
                                        <th class="border border-gray-400 px-3 py-2 text-right">Satuan (Rp.)</th>
                                        <th class="border border-gray-400 px-3 py-2 text-right">Total (Rp.)</th>
                                        <th class="border border-gray-400 px-3 py-2">Ket.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalNominal = 0;
                                        $totalHari = 0;
                                    @endphp
                                    @foreach($piketData as $index => $piket)
                                        @php
                                            $totalNominal += $piket->nominal_piket;
                                            $totalHari += $piket->jumlah_hari;
                                            $satuan = $piket->jumlah_hari > 0 ? $piket->nominal_piket / $piket->jumlah_hari : 0;
                                        @endphp
                                        <tr>
                                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $index + 1 }}</td>
                                            <td class="border border-gray-400 px-3 py-2">
                                                {{ $piket->tanggal_awal_piket->format('d/m/Y') }}
                                                @if($piket->tanggal_awal_piket->format('Y-m-d') != $piket->tanggal_akhir_piket->format('Y-m-d'))
                                                    - {{ $piket->tanggal_akhir_piket->format('d/m/Y') }}
                                                @endif
                                            </td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $piket->jenis_piket }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $piket->nama_lembaga ?? '-' }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $piket->jumlah_hari }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-right">{{ number_format($satuan, 0, ',', '.') }}</td>
                                            <td class="border border-gray-400 px-3 py-2 text-right">{{ number_format($piket->nominal_piket, 0, ',', '.') }}</td>
                                            <td class="border border-gray-400 px-3 py-2">{{ $piket->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-blue-50 font-bold">
                                        <td colspan="4" class="border border-gray-400 px-3 py-2 text-center">TOTAL</td>
                                        <td class="border border-gray-400 px-3 py-2 text-center">{{ $totalHari }}</td>
                                        <td class="border border-gray-400 px-3 py-2"></td>
                                        <td class="border border-gray-400 px-3 py-2 text-right">{{ number_format($totalNominal, 0, ',', '.') }}</td>
                                        <td class="border border-gray-400 px-3 py-2"></td>
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
                        <p class="text-gray-500 text-lg">Tidak ada data piket untuk periode yang dipilih</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
