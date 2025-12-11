<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Cuti Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 12px;
            margin: 3px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Memastikan lebar kolom konsisten */
        }
        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left; /* Semua data rata kiri */
            word-wrap: break-word; /* Memastikan teks panjang tidak overflow */
        }
        .table-data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            white-space: nowrap;
        }
        .table-footer {
            margin-top: 20px;
            text-align: right;
        }
        .currency {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP CUTI KARYAWAN TAHUN {{ $tahun }}</h1>
        <p>PT. USSI BAHTERA TEKNOLOGI INFORMATIKA</p>
        <p>Jl. Tentara Pelajar No.12, Kadilangu, Kabupaten Batang, Jawa Tengah 51216</p>
    </div>
    
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="12%">NIK</th>
                <th width="28%">Nama Karyawan</th>
                <th width="12%">Cuti Terpakai</th>
                <th width="12%">Sisa Cuti</th>
                <th width="31%">Nominal Pengganti</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_karyawan as $index => $karyawan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $karyawan['nik'] }}</td>
                    <td>{{ $karyawan['nama'] }}</td>
                    <td>{{ $karyawan['cuti_terpakai'] }}</td>
                    <td>{{ $karyawan['sisa_cuti'] }}</td>
                    <td class="currency">Rp{{ number_format($karyawan['nominal_pengganti'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: center; font-weight: bold;">Total Nominal Pengganti </td>
                <td class="currency" style="font-weight: bold;">Rp{{ number_format($total['nominal_pengganti'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="table-footer">
        <p>Dicetak pada: {{ date('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>