<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Cuti Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
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
            margin: 0;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .employee-info {
            margin-bottom: 20px;
        }
        .employee-info table {
            width: 100%;
        }
        .employee-info td {
            padding: 3px 0;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .table-data th {
            background-color: #f2f2f2;
        }
        .logo {
            float: left;
            margin-right: 20px;
            width: 80px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN CUTI KARYAWAN TAHUN {{ $tahun }}</h1>
        <p>PT. USSI BAHTERA TEKNOLOGI INFORMATIKA</p>
        <p>Jl. Tentara Pelajar No.12, Kadilangu,Kabupaten Batang, Jawa Tengah 51216</p>
    </div>
    
    <div class="employee-info">
        <table>
            <tr>
                <td width="150">NIK</td>
                <td width="10">:</td>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td>Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan->nama }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
        </table>
    </div>
    
    <table class="table-data">
        <thead>
            <tr>
                <th width="50">No.</th>
                <th width="100">Tanggal</th>
                <th width="100">Jumlah hari</th>
                <th width="100">Sisa Cuti Tahunan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_cuti as $index => $cuti)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $cuti['tanggal'] }}</td>
                    <td>{{ $cuti['jumlah_hari'] }}</td>
                    <td>{{ $cuti['sisa_cuti'] }}</td>
                    <td>{{ $cuti['keterangan'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada data cuti</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>