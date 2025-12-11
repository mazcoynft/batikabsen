<?php

namespace App\Filament\Resources\LaporanPiketResource\Pages;

use App\Filament\Resources\LaporanPiketResource;
use App\Models\Karyawan;
use App\Models\PengajuanPiket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Blade;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GenerateLaporanPiket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = LaporanPiketResource::class;

    protected static string $view = 'filament.resources.laporan-piket-resource.pages.generate-laporan-piket';
    
    protected static ?string $title = 'Generate Laporan Piket';

    public ?array $data = [];
    public $piketData = null;
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

        // Get piket data for selected month
        $this->piketData = PengajuanPiket::where('nik', $nik)
            ->where('status', 'approved')
            ->whereYear('tanggal_awal_piket', $tahun)
            ->whereMonth('tanggal_awal_piket', $bulan)
            ->orderBy('tanggal_awal_piket')
            ->get();
    }

    public function downloadPdf()
    {
        $data = $this->form->getState();
        
        $nik = $data['nik'];
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];

        $karyawan = Karyawan::where('nik', $nik)->first();
        
        $piketData = PengajuanPiket::where('nik', $nik)
            ->where('status', 'approved')
            ->whereYear('tanggal_awal_piket', $tahun)
            ->whereMonth('tanggal_awal_piket', $bulan)
            ->orderBy('tanggal_awal_piket')
            ->get();

        $bulanNama = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $pdf = Pdf::loadView('pdf.laporan-piket', [
            'karyawan' => $karyawan,
            'piketData' => $piketData,
            'bulan' => $bulanNama[$bulan],
            'tahun' => $tahun,
            'tanggalCetak' => now()->format('d F Y'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Piket-' . $karyawan->nama . '-' . $bulan . '-' . $tahun . '.pdf');
    }
    
    public function downloadExcel()
    {
        $data = $this->form->getState();
        
        $nik = $data['nik'];
        $bulan = $data['bulan'];
        $tahun = $data['tahun'];

        $karyawan = Karyawan::where('nik', $nik)->first();
        
        $piketData = PengajuanPiket::where('nik', $nik)
            ->where('status', 'approved')
            ->whereYear('tanggal_awal_piket', $tahun)
            ->whereMonth('tanggal_awal_piket', $bulan)
            ->orderBy('tanggal_awal_piket')
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
            ->setTitle('Laporan Piket')
            ->setSubject('Laporan Piket ' . $karyawan->nama);
        
        // Header
        $sheet->setCellValue('A1', 'LAPORAN PIKET');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Periode: ' . $bulanNama[$bulan] . ' ' . $tahun);
        $sheet->mergeCells('A2:H2');
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
        $sheet->setCellValue('A' . $row, 'RINCIAN PERHITUNGAN PETUGAS');
        $sheet->mergeCells('A' . $row . ':H' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $headers = ['No.', 'Tanggal', 'Jenis Piket', 'Lembaga', 'QTY Hari', 'Satuan (Rp.)', 'Total (Rp.)', 'Ket.'];
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
        $totalNominal = 0;
        $totalHari = 0;
        
        foreach ($piketData as $index => $piket) {
            $totalNominal += $piket->nominal_piket;
            $totalHari += $piket->jumlah_hari;
            $satuan = $piket->jumlah_hari > 0 ? $piket->nominal_piket / $piket->jumlah_hari : 0;
            
            $tanggal = $piket->tanggal_awal_piket->format('d/m/Y');
            if ($piket->tanggal_awal_piket->format('Y-m-d') != $piket->tanggal_akhir_piket->format('Y-m-d')) {
                $tanggal .= ' - ' . $piket->tanggal_akhir_piket->format('d/m/Y');
            }
            
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $tanggal);
            $sheet->setCellValue('C' . $row, $piket->jenis_piket);
            $sheet->setCellValue('D' . $row, $piket->nama_lembaga ?? '-');
            $sheet->setCellValue('E' . $row, $piket->jumlah_hari);
            $sheet->setCellValue('F' . $row, $satuan);
            $sheet->setCellValue('G' . $row, $piket->nominal_piket);
            $sheet->setCellValue('H' . $row, $piket->keterangan ?? '-');
            
            // Center align for number columns
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Number format
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
            
            $row++;
        }
        
        // Total Row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $sheet->setCellValue('E' . $row, $totalHari);
        $sheet->setCellValue('G' . $row, $totalNominal);
        
        $sheet->getStyle('A' . $row . ':H' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D0E8FF');
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        // Apply borders to table
        $tableRange = 'A10:H' . $row;
        $sheet->getStyle($tableRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(30);
        
        // Signature
        $row += 3;
        $sheet->setCellValue('G' . $row, 'Pekalongan, ' . now()->format('d F Y'));
        $row += 4;
        $sheet->setCellValue('G' . $row, 'Zaki Muttaqien');
        $sheet->getStyle('G' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('G' . $row, 'PT. USSI BATIK');
        
        // Create Excel file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan-Piket-' . $karyawan->nama . '-' . $bulan . '-' . $tahun . '.xlsx';
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
