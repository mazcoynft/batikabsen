<?php

namespace App\Filament\Pages;

use App\Exports\RekapPresensiHarianExport;
use App\Models\Cabang;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\Presensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapPresensi extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Rekap Presensi';
    protected static ?string $title = 'Rekap Presensi Karyawan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.rekap-presensi';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->format('Y-m-d'),
            'tanggal_akhir' => now()->format('Y-m-d'),
            'cabang_id' => null,
            'departemen_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required()
                    ->default(now()),
                
                DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->required()
                    ->default(now())
                    ->afterOrEqual('tanggal_mulai'),
                
                Select::make('cabang_id')
                    ->label('Cabang')
                    ->options(Cabang::query()->pluck('nama_cabang', 'kode_cabang'))
                    ->searchable()
                    ->placeholder('Semua Cabang'),
                
                Select::make('departemen_id')
                    ->label('Departemen')
                    ->options(Department::query()->pluck('nama_dept', 'kode_dept'))
                    ->searchable()
                    ->placeholder('Semua Departemen'),
            ])
            ->statePath('data');
    }

    public function cetakPDF()
    {
        try {
            $this->form->validate();
            
            $tanggalMulai = Carbon::parse($this->data['tanggal_mulai']);
            $tanggalAkhir = Carbon::parse($this->data['tanggal_akhir']);
            $cabangId = $this->data['cabang_id'] ?? null;
            $departemenId = $this->data['departemen_id'] ?? null;
            
            // Validasi rentang tanggal
            if ($tanggalAkhir->diffInDays($tanggalMulai) > 31) {
                $this->addError('data.tanggal_akhir', 'Rentang tanggal tidak boleh lebih dari 31 hari');
                return null;
            }
            
            // Query karyawan berdasarkan filter
            $karyawanQuery = Karyawan::with(['department', 'cabang']);
            
            if ($cabangId) {
                $karyawanQuery->where('kode_cabang', $cabangId);
            }
            
            if ($departemenId) {
                $karyawanQuery->where('kode_dept', $departemenId);
            }
            
            $karyawans = $karyawanQuery->get();
            
            // Siapkan data untuk laporan
            $dataKaryawan = [];
            $totalUangMakan = 0;
            $uangMakanPerHari = 20000;
            
            foreach ($karyawans as $karyawan) {
                $presensiData = [];
                $countH = 0;
                $countI = 0;
                $countS = 0;
                $countC = 0;
                $countA = 0;
                $countTerlambat = 0;
                $totalUangMakanKaryawan = 0;
                
                // Loop melalui setiap hari dalam rentang tanggal
                $currentDate = clone $tanggalMulai;
                while ($currentDate <= $tanggalAkhir) {
                    $tanggal = $currentDate->format('Y-m-d');
                    $presensi = Presensi::where('karyawan_id', $karyawan->id)
                        ->whereDate('tgl_presensi', $tanggal)
                        ->first();
                    
                    $status = '-';
                    $uangMakanHarian = 0;
                    
                    if ($presensi) {
                        if ($presensi->status == 'h') {
                            $countH++;
                            
                            // Cek apakah terlambat
                            if ($presensi->status_presensi_in == 2) {
                                $status = 't'; // t = terlambat (warna merah)
                                $countTerlambat++;
                                // TIDAK dapat uang makan jika terlambat
                            } else {
                                $status = 'h'; // h = hadir tepat waktu (warna hijau)
                                // Berikan uang makan HANYA untuk yang hadir tepat waktu
                                $uangMakanHarian = $uangMakanPerHari;
                                $totalUangMakanKaryawan += $uangMakanHarian;
                            }
                        } else if ($presensi->status == 'i') {
                            $status = 'i';
                            $countI++;
                        } else if ($presensi->status == 's') {
                            $status = 's';
                            $countS++;
                        } else if ($presensi->status == 'c') {
                            $status = 'c';
                            $countC++;
                        }
                    } else {
                        $countA++;
                    }
                    
                    $presensiData[$currentDate->format('d')] = $status;
                    $currentDate->addDay();
                }
                
                $dataKaryawan[] = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'presensi' => $presensiData,
                    'h' => $countH,
                    'i' => $countI,
                    's' => $countS,
                    'c' => $countC,
                    'a' => $countA,
                    'terlambat' => $countTerlambat,
                    'uang_makan' => $totalUangMakanKaryawan,
                ];
                
                $totalUangMakan += $totalUangMakanKaryawan;
            }
            
            $data = [
                'tanggal_mulai' => $tanggalMulai->format('d-m-Y'),
                'tanggal_akhir' => $tanggalAkhir->format('d-m-Y'),
                'bulan' => $tanggalMulai->format('F'),
                'cabang' => $cabangId ? Cabang::where('kode_cabang', $cabangId)->first() : null,
                'departemen' => $departemenId ? Department::where('kode_dept', $departemenId)->first() : null,
                'data_karyawan' => $dataKaryawan,
                'tanggal_range' => $this->generateDateRange($tanggalMulai, $tanggalAkhir),
                'total_uang_makan' => $totalUangMakan,
            ];
            
            $pdf = PDF::loadView('exports.rekap-presensi', $data);
            
            return response()->streamDownload(
                fn () => print($pdf->output()),
                "rekap_presensi_{$tanggalMulai->format('Ymd')}_{$tanggalAkhir->format('Ymd')}.pdf"
            );
            
        } catch (Halt $exception) {
            return null;
        }
    }

    public function exportExcel()
    {
        try {
            $this->form->validate();
            
            $tanggalMulai = Carbon::parse($this->data['tanggal_mulai']);
            $tanggalAkhir = Carbon::parse($this->data['tanggal_akhir']);
            $cabangId = $this->data['cabang_id'] ?? null;
            $departemenId = $this->data['departemen_id'] ?? null;
            
            // Validasi rentang tanggal
            if ($tanggalAkhir->diffInDays($tanggalMulai) > 31) {
                $this->addError('data.tanggal_akhir', 'Rentang tanggal tidak boleh lebih dari 31 hari');
                return null;
            }
            
            // Query karyawan berdasarkan filter
            $karyawanQuery = Karyawan::with(['department', 'cabang']);
            
            if ($cabangId) {
                $karyawanQuery->where('kode_cabang', $cabangId);
            }
            
            if ($departemenId) {
                $karyawanQuery->where('kode_dept', $departemenId);
            }
            
            $karyawans = $karyawanQuery->get();
            
            // Siapkan data untuk laporan
            $dataKaryawan = [];
            $uangMakanPerHari = 20000;
            
            foreach ($karyawans as $karyawan) {
                $presensiData = [];
                $countH = 0;
                $countI = 0;
                $countS = 0;
                $countC = 0;
                $countA = 0;
                $countTerlambat = 0;
                $totalUangMakanKaryawan = 0;
                
                // Loop melalui setiap hari dalam rentang tanggal
                $currentDate = clone $tanggalMulai;
                while ($currentDate <= $tanggalAkhir) {
                    $tanggal = $currentDate->format('Y-m-d');
                    $presensi = Presensi::where('karyawan_id', $karyawan->id)
                        ->whereDate('tgl_presensi', $tanggal)
                        ->first();
                    
                    $status = '-';
                    $uangMakanHarian = 0;
                    
                    if ($presensi) {
                        if ($presensi->status == 'h') {
                            $countH++;
                            
                            // Cek apakah terlambat
                            if ($presensi->status_presensi_in == 2) {
                                $status = 't'; // t = terlambat (warna merah)
                                $countTerlambat++;
                                // TIDAK dapat uang makan jika terlambat
                            } else {
                                $status = 'h'; // h = hadir tepat waktu (warna hijau)
                                // Berikan uang makan HANYA untuk yang hadir tepat waktu
                                $uangMakanHarian = $uangMakanPerHari;
                                $totalUangMakanKaryawan += $uangMakanHarian;
                            }
                        } else if ($presensi->status == 'i') {
                            $status = 'i';
                            $countI++;
                        } else if ($presensi->status == 's') {
                            $status = 's';
                            $countS++;
                        } else if ($presensi->status == 'c') {
                            $status = 'c';
                            $countC++;
                        }
                    } else {
                        $countA++;
                    }
                    
                    $presensiData[$currentDate->format('d')] = $status;
                    $currentDate->addDay();
                }
                
                $row = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'bulan' => $tanggalMulai->format('F'),
                ];
                
                // Tambahkan data presensi harian
                foreach ($this->generateDateRange($tanggalMulai, $tanggalAkhir) as $day) {
                    $row[$day] = $presensiData[$day] ?? '-';
                }
                
                // Tambahkan data ringkasan
                $row['h'] = $countH;
                $row['i'] = $countI;
                $row['s'] = $countS;
                $row['c'] = $countC;
                $row['a'] = $countA;
                $row['terlambat'] = $countTerlambat;
                $row['uang_makan'] = $totalUangMakanKaryawan;
                
                $dataKaryawan[] = $row;
            }
            
            $dateRange = $this->generateDateRange($tanggalMulai, $tanggalAkhir);
            
            return Excel::download(
                new RekapPresensiHarianExport($dataKaryawan, $dateRange), 
                "rekap_presensi_{$tanggalMulai->format('Ymd')}_{$tanggalAkhir->format('Ymd')}.xlsx"
            );
            
        } catch (Halt $exception) {
            return null;
        }
    }
    
    private function generateDateRange(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = clone $start;
        
        while ($current <= $end) {
            $dates[] = $current->format('d');
            $current->addDay();
        }
        
        return $dates;
    }
}