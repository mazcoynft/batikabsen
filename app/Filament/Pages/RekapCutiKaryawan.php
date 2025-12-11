<?php

namespace App\Filament\Pages;

use App\Exports\RekapCutiKaryawanExport;
use App\Models\Cabang;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\PengajuanIzin;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RekapCutiKaryawan extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Rekap Cuti Karyawan';
    protected static ?string $title = 'Laporan Rekap Cuti Karyawan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.rekap-cuti-karyawan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tahun' => date('Y'),
            'cabang_id' => null,
            'departemen_id' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tahun')
                    ->label('Tahun')
                    ->options(function () {
                        $years = [];
                        $currentYear = (int) date('Y');
                        for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++) {
                            $years[$i] = (string) $i;
                        }
                        return $years;
                    })
                    ->default(date('Y'))
                    ->required(),
                
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
            
            $tahun = $this->data['tahun'];
            $cabangId = $this->data['cabang_id'];
            $departemenId = $this->data['departemen_id'];
            
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
            $total = [
                'hak_cuti' => 0,
                'cuti_terpakai' => 0,
                'sisa_cuti' => 0,
                'nominal_pengganti' => 0,
            ];
            
            foreach ($karyawans as $karyawan) {
                // Ambil data pengajuan cuti untuk karyawan ini
                $pengajuanCuti = $karyawan->pengajuanIzin()
                    ->where('status', 'approved')
                    ->whereYear('tanggal_awal', $tahun)
                    ->whereHas('cuti', function ($query) {
                        $query->where('nama_cuti', 'like', '%Tahunan%');
                    })
                    ->get();
                
                // Hitung cuti terpakai
                $cutiTerpakai = $pengajuanCuti->sum('jumlah_hari');
                
                // Default hak cuti tahunan
                $hakCuti = 12;
                
                // Hitung sisa cuti
                $sisaCuti = $hakCuti - $cutiTerpakai;
                
                // Nominal pengganti = sisa cuti x Rp100.000
                $nominalPengganti = $sisaCuti * 100000;
                
                $dataKaryawan[] = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'departemen' => $karyawan->department->nama_dept ?? '-',
                    'jabatan' => $karyawan->jabatan,
                    'hak_cuti' => $hakCuti,
                    'cuti_terpakai' => $cutiTerpakai,
                    'sisa_cuti' => $sisaCuti,
                    'nominal_pengganti' => $nominalPengganti,
                    'nominal_pengganti_formatted' => 'Rp' . number_format($nominalPengganti, 0, ',', '.'),
                ];
                
                // Update total
                $total['hak_cuti'] += $hakCuti;
                $total['cuti_terpakai'] += $cutiTerpakai;
                $total['sisa_cuti'] += $sisaCuti;
                $total['nominal_pengganti'] += $nominalPengganti;
            }
            
            $data = [
                'tahun' => $tahun,
                'cabang' => $cabangId ? Cabang::where('kode_cabang', $cabangId)->first() : null,
                'departemen' => $departemenId ? Department::where('kode_dept', $departemenId)->first() : null,
                'data_karyawan' => $dataKaryawan,
                'total' => $total,
            ];
            
            $pdf = PDF::loadView('exports.rekap-cuti-karyawan', $data);
            
            return response()->streamDownload(
                fn () => print($pdf->output()),
                "rekap_cuti_karyawan_{$tahun}.pdf"
            );
            
        } catch (Halt $exception) {
            return null;
        }
    }

    public function exportExcel()
    {
        try {
            $this->form->validate();
            
            $tahun = $this->data['tahun'];
            $cabangId = $this->data['cabang_id'] ?? null;
            $departemenId = $this->data['departemen_id'] ?? null;
            
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
            
            foreach ($karyawans as $karyawan) {
                // Ambil data pengajuan cuti untuk karyawan ini
                $pengajuanCuti = $karyawan->pengajuanIzin()
                    ->where('status', 'approved')
                    ->whereYear('tanggal_awal', $tahun)
                    ->whereHas('cuti', function ($query) {
                        $query->where('nama_cuti', 'like', '%Tahunan%');
                    })
                    ->get();
                
                // Hitung cuti terpakai
                $cutiTerpakai = $pengajuanCuti->sum('jumlah_hari');
                
                // Default hak cuti tahunan
                $hakCuti = 12;
                
                // Hitung sisa cuti
                $sisaCuti = $hakCuti - $cutiTerpakai;
                
                // Nominal pengganti = sisa cuti x Rp100.000
                $nominalPengganti = $sisaCuti * 100000;
                
                $dataKaryawan[] = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'departemen' => $karyawan->department->nama_dept ?? '-',
                    'jabatan' => $karyawan->jabatan,
                    'hak_cuti' => $hakCuti,
                    'cuti_terpakai' => $cutiTerpakai,
                    'sisa_cuti' => $sisaCuti,
                    'nominal_pengganti' => $nominalPengganti,
                ];
            }
            
            return Excel::download(new RekapCutiKaryawanExport($dataKaryawan), "rekap_cuti_karyawan_{$tahun}.xlsx");
            
        } catch (Halt $exception) {
            return null;
        }
    }
}