<?php

namespace App\Filament\Pages;

use App\Exports\CutiTahunanExport;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanCutiTahunan extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Cuti Karyawan';
    protected static ?string $title = 'Laporan Cuti Karyawan';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.laporan-cuti-tahunan';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'tahun' => date('Y'),
            'karyawan_id' => null,
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
                
                Select::make('karyawan_id')
                    ->label('Pilih Karyawan')
                    ->options(Karyawan::query()->pluck('nama', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->statePath('data');
    }

    public function cetakPDF()
    {
        try {
            $this->form->validate();
            
            $karyawan = Karyawan::with(['department', 'cabang'])->find($this->data['karyawan_id']);
            $tahun = $this->data['tahun'];
            
            // Ambil data pengajuan cuti untuk karyawan ini
            $pengajuanCuti = $karyawan->pengajuanIzin()
                ->where('status', 'approved')
                ->whereYear('tanggal_awal', $tahun)
                ->whereHas('cuti', function ($query) {
                    $query->where('nama_cuti', 'like', '%Tahunan%');
                })
                ->orderBy('tanggal_awal')
                ->get();
            
            // Hitung sisa cuti
            $sisaCuti = 12; // Default hak cuti tahunan
            
            // Data untuk laporan
            $dataCuti = [];
            foreach ($pengajuanCuti as $cuti) {
                $sisaCuti -= $cuti->jumlah_hari;
                $dataCuti[] = [
                    'tanggal' => Carbon::parse($cuti->tanggal_awal)->format('d-m-Y'),
                    'jumlah_hari' => $cuti->jumlah_hari,
                    'sisa_cuti' => $sisaCuti,
                    'keterangan' => $cuti->keterangan,
                ];
            }
            
            $data = [
                'karyawan' => $karyawan,
                'tahun' => $tahun,
                'data_cuti' => $dataCuti,
                'sisa_cuti' => $sisaCuti,
            ];
            
            $pdf = PDF::loadView('exports.laporan-cuti-tahunan', $data);
            
            return response()->streamDownload(
                fn () => print($pdf->output()),
                "laporan_cuti_{$karyawan->nik}_{$tahun}.pdf"
            );
            
        } catch (Halt $exception) {
            return null;
        }
    }

    public function exportExcel()
    {
        try {
            $this->form->validate();
            
            $karyawan = Karyawan::with(['department', 'cabang'])->find($this->data['karyawan_id']);
            $tahun = $this->data['tahun'];
            
            // Ambil data pengajuan cuti untuk karyawan ini
            $pengajuanCuti = $karyawan->pengajuanIzin()
                ->where('status', 'approved')
                ->whereYear('tanggal_awal', $tahun)
                ->whereHas('cuti', function ($query) {
                    $query->where('nama_cuti', 'like', '%Tahunan%');
                })
                ->orderBy('tanggal_awal')
                ->get();
            
            // Hitung sisa cuti
            $sisaCuti = 12; // Default hak cuti tahunan
            
            // Data untuk laporan
            $dataCuti = [];
            foreach ($pengajuanCuti as $cuti) {
                $sisaCuti -= $cuti->jumlah_hari;
                $dataCuti[] = [
                    'nik' => $karyawan->nik,
                    'nama' => $karyawan->nama,
                    'departemen' => $karyawan->department->nama_dept ?? '-',
                    'jabatan' => $karyawan->jabatan,
                    'tanggal' => Carbon::parse($cuti->tanggal_awal)->format('d-m-Y'),
                    'jumlah_hari' => $cuti->jumlah_hari,
                    'sisa_cuti' => $sisaCuti,
                    'keterangan' => $cuti->keterangan,
                ];
            }
            
            return Excel::download(new CutiTahunanExport($dataCuti), "laporan_cuti_{$karyawan->nik}_{$tahun}.xlsx");
            
        } catch (Halt $exception) {
            return null;
        }
    }
}