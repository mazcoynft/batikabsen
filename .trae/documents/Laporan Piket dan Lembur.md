# Laporan Piket dan Lembur

## Overview
Sistem laporan untuk mencetak dan mengunduh laporan piket dan lembur karyawan dalam format PDF.

## Fitur

### 1. Laporan Piket
**Lokasi Menu:** Laporan → Laporan Piket

**Fitur:**
- Filter berdasarkan karyawan (searchable dropdown)
- Filter berdasarkan bulan dan tahun
- Preview laporan sebelum download
- Generate PDF dengan format profesional

**Format Laporan:**
- Header: Judul laporan dan periode
- Info Karyawan: Nama, NIK, Jabatan, Department
- Tabel Rincian:
  - No
  - Tanggal (awal - akhir)
  - Jenis Piket (Mingguan / Hari Libur)
  - Lembaga
  - QTY Hari
  - Satuan (Rp.)
  - Total (Rp.)
  - Keterangan
- Total keseluruhan
- Tanda tangan dan tanggal cetak

**Jenis Piket & Tarif:**
- **Mingguan**: Rp 25.000/hari
- **Hari Libur**: Rp 50.000/hari

**Data yang Ditampilkan:**
- Hanya menampilkan data piket dengan status "approved"
- Data difilter berdasarkan bulan dan tahun dari tanggal_awal_piket

### 2. Laporan Lembur
**Lokasi Menu:** Laporan → Laporan Lembur

**Fitur:**
- Filter berdasarkan karyawan (searchable dropdown)
- Filter berdasarkan bulan dan tahun
- Preview laporan sebelum download
- Generate PDF dengan format profesional

**Format Laporan:**
- Header: Judul laporan dan periode
- Info Karyawan: Nama, NIK, Jabatan, Department
- Tabel Rincian:
  - No
  - Tanggal Awal
  - Tanggal Akhir
  - Lembaga
  - Keterangan
  - Jumlah Hari
- Total keseluruhan jumlah hari
- Tanda tangan dan tanggal cetak

**Data yang Ditampilkan:**
- Hanya menampilkan data lembur dengan status "approved"
- Data difilter berdasarkan bulan dan tahun dari tanggal_awal_lembur atau tanggal_akhir_lembur

## Cara Penggunaan

### Generate Laporan Piket:
1. Buka menu **Laporan → Laporan Piket**
2. Klik tombol **"Generate Laporan"** di pojok kanan atas
3. Pilih karyawan dari dropdown
4. Pilih bulan dan tahun
5. Klik **"Preview Laporan"** untuk melihat preview
6. Jika sudah sesuai, pilih format download:
   - Klik **"Download PDF"** untuk mengunduh dalam format PDF
   - Klik **"Download Excel"** untuk mengunduh dalam format XLSX

### Generate Laporan Lembur:
1. Buka menu **Laporan → Laporan Lembur**
2. Klik tombol **"Generate Laporan"** di pojok kanan atas
3. Pilih karyawan dari dropdown
4. Pilih bulan dan tahun
5. Klik **"Preview Laporan"** untuk melihat preview
6. Jika sudah sesuai, pilih format download:
   - Klik **"Download PDF"** untuk mengunduh dalam format PDF
   - Klik **"Download Excel"** untuk mengunduh dalam format XLSX

## File yang Dibuat

### Laporan Piket:
- `app/Filament/Resources/LaporanPiketResource.php`
- `app/Filament/Resources/LaporanPiketResource/Pages/ListLaporanPikets.php`
- `app/Filament/Resources/LaporanPiketResource/Pages/GenerateLaporanPiket.php`
- `app/Filament/Resources/LaporanPiketResource/Pages/ViewLaporanPiket.php`
- `resources/views/filament/resources/laporan-piket-resource/pages/generate-laporan-piket.blade.php`
- `resources/views/pdf/laporan-piket.blade.php`

### Laporan Lembur:
- `app/Filament/Resources/LaporanLemburResource.php`
- `app/Filament/Resources/LaporanLemburResource/Pages/ListLaporanLemburs.php`
- `app/Filament/Resources/LaporanLemburResource/Pages/GenerateLaporanLembur.php`
- `app/Filament/Resources/LaporanLemburResource/Pages/ViewLaporanLembur.php`
- `resources/views/filament/resources/laporan-lembur-resource/pages/generate-laporan-lembur.blade.php`
- `resources/views/pdf/laporan-lembur.blade.php`

## Dependencies
- **barryvdh/laravel-dompdf**: Untuk generate PDF (sudah terinstall)
- **phpoffice/phpspreadsheet**: Untuk generate Excel XLSX (sudah terinstall)

## Format File Download

### PDF
- Format profesional dengan header, tabel, dan tanda tangan
- Nama file: `Laporan-Piket-[Nama]-[Bulan]-[Tahun].pdf` atau `Laporan-Lembur-[Nama]-[Bulan]-[Tahun].pdf`

### Excel (XLSX)
- Format spreadsheet dengan styling profesional
- Header dengan merge cells dan bold text
- Tabel dengan borders dan background color
- Number formatting untuk kolom nominal
- Nama file: `Laporan-Piket-[Nama]-[Bulan]-[Tahun].xlsx` atau `Laporan-Lembur-[Nama]-[Bulan]-[Tahun].xlsx`

## Notes
- Laporan hanya menampilkan data dengan status "approved"
- Format PDF dan Excel sudah disesuaikan dengan standar perusahaan
- Tanda tangan menggunakan nama user yang sedang login
- Tanggal cetak otomatis menggunakan tanggal saat generate laporan
- File Excel dapat langsung dibuka dan diedit di Microsoft Excel, Google Sheets, atau LibreOffice Calc
