<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Karyawan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 5mm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        
        .company-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .company-address {
            font-size: 6pt;
            margin-bottom: 1px;
        }
        
        .period {
            font-size: 7pt;
            margin-bottom: 6px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 6pt;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 1px 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        th {
            background-color: #e8e8e8;
            font-weight: bold;
            font-size: 5.5pt;
        }
        
        .nik-column {
            width: 28px;
            font-size: 5.5pt;
        }
        
        .name-column {
            text-align: left;
            width: 65px;
            font-size: 6pt;
            padding-left: 3px;
        }
        
        .date-cell {
            width: 12px;
            font-size: 5.5pt;
        }
        
        .summary-cell {
            width: 14px;
            font-size: 5.5pt;
        }
        
        .currency-cell {
            text-align: right;
            width: 45px;
            font-size: 5.5pt;
            padding-right: 3px;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        
        .present {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        
        .late {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }
        
        .izin {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .sakit {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .cuti {
            background-color: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">REKAP PRESENSI KARYAWAN</div>
        <div class="company-address">PT USSI BAHTERA TEKNIK INFORMATIKA</div>
        <div class="company-address">Jl. Hos Cokroaminoto No.67, Landungsari, Kota Pekalongan, Jawa Tengah 51129</div>
        <div class="period">Periode : {{ $tanggal_mulai }} s/d {{ $tanggal_akhir }}</div>
    </div>
    
    
    
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="nik-column">NIK</th>
                <th rowspan="2" class="name-column">Nama Karyawan</th>
                <th colspan="{{ count($tanggal_range) }}">Bulan {{ $bulan }}</th>
                <th colspan="7">Ringkasan</th>
            </tr>
            <tr>
                @foreach($tanggal_range as $day)
                    <th class="date-cell">{{ $day }}</th>
                @endforeach
                <th class="summary-cell">H</th>
                <th class="summary-cell">I</th>
                <th class="summary-cell">S</th>
                <th class="summary-cell">C</th>
                <th class="summary-cell">A</th>
                <th class="summary-cell">T</th>
                <th class="currency-cell">Uang Makan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data_karyawan as $karyawan)
                <tr>
                    <td class="nik-column">{{ $karyawan['nik'] }}</td>
                    <td class="name-column">{{ $karyawan['nama'] }}</td>
                    
                    @foreach($tanggal_range as $day)
                        @php
                            $status = $karyawan['presensi'][$day] ?? '-';
                            $class = '';
                            $display = '';
                            
                            if ($status == 'h') {
                                $class = 'present';
                                $display = 'h';
                            } elseif ($status == 't') {
                                $class = 'late';
                                $display = 't';
                            } elseif ($status == 'i') {
                                $class = 'izin';
                                $display = 'i';
                            } elseif ($status == 's') {
                                $class = 'sakit';
                                $display = 's';
                            } elseif ($status == 'c') {
                                $class = 'cuti';
                                $display = 'c';
                            }
                        @endphp
                        <td class="date-cell {{ $class }}">{{ $display }}</td>
                    @endforeach
                    
                    <td class="summary-cell">{{ $karyawan['h'] }}</td>
                    <td class="summary-cell">{{ $karyawan['i'] }}</td>
                    <td class="summary-cell">{{ $karyawan['s'] }}</td>
                    <td class="summary-cell">{{ $karyawan['c'] }}</td>
                    <td class="summary-cell">{{ $karyawan['a'] }}</td>
                    <td class="summary-cell">{{ $karyawan['terlambat'] }}</td>
                    <td class="currency-cell">Rp{{ number_format($karyawan['uang_makan'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="{{ count($tanggal_range) + 8 }}" style="text-align: center; font-size: 7pt;">Total Uang Makan</td>
                <td class="currency-cell">Rp{{ number_format($total_uang_makan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>