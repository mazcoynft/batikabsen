<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Lembur - {{ $karyawan->nama }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-decoration: underline;
        }
        
        .header p {
            font-size: 12pt;
            margin: 5px 0;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .info-table td {
            padding: 3px 0;
        }
        
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .info-table td:nth-child(2) {
            width: 10px;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        
        .data-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        
        .data-table td {
            vertical-align: top;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #d0e8ff;
            font-weight: bold;
        }
        
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .company-name {
            font-size: 9pt;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN LEMBUR</h1>
        <p>Periode: {{ $bulan }} {{ $tahun }}</p>
    </div>

    <!-- Info Karyawan -->
    <table class="info-table">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $karyawan->nama }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $karyawan->nik }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $karyawan->jabatan }}</td>
        </tr>
        <tr>
            <td>Department</td>
            <td>:</td>
            <td>{{ $karyawan->department->nama_dept ?? '-' }}</td>
        </tr>
    </table>

    <!-- Section Title -->
    <div class="section-title">RINCIAN LEMBUR</div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 15%;">Tanggal Awal</th>
                <th style="width: 15%;">Tanggal Akhir</th>
                <th style="width: 20%;">Lembaga</th>
                <th style="width: 35%;">Keterangan</th>
                <th style="width: 10%;">Jumlah<br>Hari</th>
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
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $lembur->tanggal_awal_lembur->format('d/m/Y') }}</td>
                    <td>{{ $lembur->tanggal_akhir_lembur->format('d/m/Y') }}</td>
                    <td>{{ $lembur->nama_lembaga }}</td>
                    <td>{{ $lembur->keterangan }}</td>
                    <td class="text-center">{{ $jumlahHari }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="text-center">TOTAL</td>
                <td class="text-center">{{ $totalHari }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signature -->
    <div class="signature">
        <div class="signature-box">
            <p>Pekalongan, {{ $tanggalCetak }}</p>
            <div class="signature-line">
                Zaki Muttaqien
            </div>
            <div class="company-name">PT. USSI BATIK</div>
        </div>
    </div>
</body>
</html>
