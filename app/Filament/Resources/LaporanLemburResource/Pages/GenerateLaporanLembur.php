<?php

namespace App\Filament\Resources\LaporanLemburResource\Pages;

use App\Filament\Resources\LaporanLemburResource;
use App\Models\Karyawan;
use App\Models\Lembur;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenerateLaporanLembur extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = LaporanLemburResource::class;

    protected static string $view = 'filament.resources.laporan-lembur-resource.pages.generate-laporan-lembur';
    
    protected static ?string $title = 'Generate Laporan Lembur';

    public ?array $data = [];
    public $lemburData = null;
    public $selectedKaryawan = null;
    public $selectedMonth = null;
    public $selectedYear = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Filter Laporan')
                    ->schema([
                        Forms\Components\Select::make('nik')
                            ->label('Pilih Karyawan')
                            ->options(Karyawan::all()->pluck('nama', 'nik'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedKaryawan = Karyawan::where('nik', $state)->first();
                            }),
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->default(now()->format('m'))
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $years = [];
                                $currentYear = now()->year;
                                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
                                    $years[$i] = $i;
                                }
                                return $years;
                            })
                            ->default(now()->year)
                            ->required()
                            ->live(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function generatePreview()
    {
        $data = $this->form->getState();
        
        $this->validate([
            'data.nik' => 'required',
            'data.bulan' => 'required',
            'data.tahun' => 'required',
        ]);

        $nik = $data['nik'];
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];

        $this->selectedKaryawan = Karyawan::where('nik', $nik)->first();
        $this->selectedMonth = $bulan;
        $this->selectedYear = $tahun;

        // Get lembur data for selected month
        $this->lemburData = Lembur::where('nik', $nik)
            ->where('status', 'approved')
            ->where(function($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal_awal_lembur', $tahun)
                      ->whereMonth('tanggal_awal_lembur', $bulan);
            })
            ->orWhere(function($query) use ($nik, $tahun, $bulan) {
                $query->where('nik', $nik)
                      ->where('status', 'approved')
                      ->whereYear('tanggal_akhir_lembur', $tahun)
                      ->whereMonth('tanggal_akhir_lembur', $bulan);
            })
            ->orderBy('tanggal_awal_lembur')
            ->get();
    }

    public function downloadPdf()
    {
        $data = $this->form->getState();
        
        $nik = $data['nik'];
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];

        $karyawan = Karyawan::where('nik', $nik)->first();
        
        $lemburData = Lembur::where('nik', $nik)
            ->where('status', 'approved')
            ->where(function($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal_awal_lembur', $tahun)
                      ->whereMonth('tanggal_awal_lembur', $bulan);
            })
            ->orWhere(function($query) use ($nik, $tahun, $bulan) {
                $query->where('nik', $nik)
                      ->where('status', 'approved')
                      ->whereYear('tanggal_akhir_lembur', $tahun)
                      ->whereMonth('tanggal_akhir_lembur', $bulan);
            })
            ->orderBy('tanggal_awal_lembur')
            ->get();

        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $pdf = Pdf::loadView('pdf.laporan-lembur', [
            'karyawan' => $karyawan,
            'lemburData' => $lemburData,
            'bulan' => $bulanNama[$bulan],
            'tahun' => $tahun,
            'tanggalCetak' => now()->format('d F Y'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Lembur-' . $karyawan->nama . '-' . $bulan . '-' . $tahun . '.pdf');
    }
    
    public function downloadExcel()
    {
        $data = $this->form->getState();
        
        $nik = $data['nik'];
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];

        $karyawan = Karyawan::where('nik', $nik)->first();
        
        $lemburData = Lembur::where('nik', $nik)
            ->where('status', 'approved')
            ->where(function($query) use ($tahun, $bulan) {
                $query->whereYear('tanggal_awal_lembur', $tahun)
                      ->whereMonth('tanggal_awal_lembur', $bulan);
            })
            ->orWhere(function($query) use ($nik, $tahun, $bulan) {
                $query->where('nik', $nik)
                      ->where('status', 'approved')
                      ->whereYear('tanggal_akhir_lembur', $tahun)
                      ->whereMonth('tanggal_akhir_lembur', $bulan);
            })
            ->orderBy('tanggal_awal_lembur')
            ->get();

        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('PT. USSI BATIK')
            ->setTitle('Laporan Lembur')
            ->setSubject('Laporan Lembur ' . $karyawan->nama);
        
        // Header
        $sheet->setCellValue('A1', 'LAPORAN LEMBUR');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . $bulanNama[$bulan] . ' ' . $tahun);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Info Karyawan
        $row = 4;
        $sheet->setCellValue('A' . $row, 'Nama');
        $sheet->setCellValue('B' . $row, ': ' . $karyawan->nama);
        $row++;
        $sheet->setCellValue('A' . $row, 'NIK');
        $sheet->setCellValue('B' . $row, ': ' . $karyawan->nik);
        $row++;
        $sheet->setCellValue('A' . $row, 'Jabatan');
        $sheet->setCellValue('B' . $row, ': ' . $karyawan->jabatan);
        $row++;
        $sheet->setCellValue('A' . $row, 'Department');
        $sheet->setCellValue('B' . $row, ': ' . ($karyawan->department->nama_dept ?? '-'));
        
        // Table Header
        $row = 9;
        $sheet->setCellValue('A' . $row, 'RINCIAN LEMBUR');
        $sheet->mergeCells('A' . $row . ':F' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $headers = ['No.', 'Tanggal Awal', 'Tanggal Akhir', 'Lembaga', 'Keterangan', 'Jumlah Hari'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        // Data
        $row++;
        $startDataRow = $row;
        $totalHari = 0;
        
        foreach ($lemburData as $index => $lembur) {
            $jumlahHari = $lembur->tanggal_awal_lembur->diffInDays($lembur->tanggal_akhir_lembur) + 1;
            $totalHari += $jumlahHari;
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $lembur->tanggal_awal_lembur->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $lembur->tanggal_akhir_lembur->format('d/m/Y'));
            $sheet->setCellValue('D' . $row, $lembur->nama_lembaga);
            $sheet->setCellValue('E' . $row, $lembur->keterangan);
            $sheet->setCellValue('F' . $row, $jumlahHari);
            
            // Center align for number columns
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }
        
        // Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':E' . $row);
        $sheet->setCellValue('F' . $row, $totalHari);
        
        $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':F' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D0E8FF');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Apply borders to table
        $tableRange = 'A10:F' . $row;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(40);
        $sheet->getColumnDimension('F')->setWidth(15);
        
        // Signature
        $row += 3;
        $sheet->setCellValue('E' . $row, 'Pekalongan, ' . now()->format('d F Y'));
        $row += 4;
        $sheet->setCellValue('E' . $row, 'Zaki Muttaqien');
        $sheet->getStyle('E' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('E' . $row, 'PT. USSI BATIK');
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan-Lembur-' . $karyawan->nama . '-' . $bulan . '-' . $tahun . '.xlsx';
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
