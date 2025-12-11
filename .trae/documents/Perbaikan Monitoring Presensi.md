# Perbaikan Monitoring Presensi

## Overview
Perbaikan pada menu Monitoring Presensi agar data absen kehadiran karyawan dapat muncul dengan benar dan tampilan lebih informatif.

## Lokasi Menu
**Presensi → Monitoring Presensi**

## Perbaikan yang Dilakukan

### 1. Perbaikan Kolom Tabel
**Sebelumnya:**
- Kolom tidak terstruktur dengan baik
- Data relasi tidak muncul
- Status tidak jelas

**Sekarang:**
- ✅ **Tanggal**: Format d/m/Y, sortable
- ✅ **NIK**: Searchable dan sortable
- ✅ **Nama Karyawan**: Searchable dan sortable
- ✅ **Cabang**: Dari relasi karyawan.cabang
- ✅ **Departemen**: Dari relasi karyawan.department
- ✅ **Jadwal Kerja**: Dari relasi jamKerja
- ✅ **Jam Masuk**: Format H:i:s dengan color coding
- ✅ **Foto Masuk**: Circular image dengan default
- ✅ **Jam Pulang**: Format H:i:s
- ✅ **Foto Pulang**: Circular image dengan default
- ✅ **Status Kehadiran**: Badge dengan warna dan text yang jelas
- ✅ **Lokasi**: Button untuk buka map
- ✅ **Keterangan**: Dari pengajuan izin jika ada

### 2. Color Coding Status
- 🟢 **Hijau**: Hadir Tepat Waktu
- 🔴 **Merah**: Terlambat
- 🟡 **Kuning**: Sakit
- 🔵 **Biru**: Izin
- 🟣 **Ungu**: Cuti
- ⚪ **Abu**: Alpha/Tidak Diketahui

### 3. Perbaikan Query
**Sebelumnya:**
- Query basic tanpa eager loading
- Relasi tidak di-load

**Sekarang:**
- ✅ Eager loading relasi: `karyawan.cabang`, `karyawan.department`, `jamKerja`
- ✅ Default order by tanggal desc, jam masuk asc
- ✅ Default filter tanggal hari ini

### 4. Filter yang Ditambahkan
- 📅 **Filter Tanggal**: DatePicker dengan default hari ini
- 📊 **Filter Status**: Dropdown (Hadir, Sakit, Izin, Cuti)
- 👤 **Filter Karyawan**: Searchable dropdown semua karyawan

### 5. Header Actions
- 🔄 **Refresh Data**: Button untuk refresh tabel
- ✅ **Notifikasi**: Konfirmasi setelah refresh

### 6. Empty State
- 📋 **Icon**: Clipboard document list
- 📝 **Heading**: "Tidak ada data presensi"
- 💡 **Description**: Panduan untuk user

### 7. Jam Masuk Color Coding
- 🟢 **Hijau**: Jam masuk tepat waktu
- 🔴 **Merah**: Jam masuk terlambat
- ⚪ **Abu**: Belum absen masuk

## Logika Status Terlambat

```php
// Cek terlambat berdasarkan jam kerja
$jamKerja = $record->jamKerja;
if ($jamKerja && $record->jam_in && Carbon::parse($record->jam_in)->gt(Carbon::parse($jamKerja->jam_masuk))) {
    return 'Terlambat'; // Warna merah
}
return 'Hadir Tepat Waktu'; // Warna hijau
```

## Fitur Lokasi
- 📍 **Button Lokasi**: Klik untuk buka modal map
- 🗺️ **Interactive Map**: Menggunakan Leaflet.js
- 📌 **Marker**: Menunjukkan lokasi absen dengan nama karyawan

## Cara Penggunaan

### Melihat Data Presensi:
1. Buka menu **Presensi → Monitoring Presensi**
2. Default menampilkan data hari ini
3. Lihat status kehadiran dengan color coding
4. Klik icon lokasi untuk melihat map

### Filter Data:
1. **Filter Tanggal**: Pilih tanggal yang diinginkan
2. **Filter Status**: Pilih status tertentu (Hadir, Sakit, dll)
3. **Filter Karyawan**: Cari karyawan spesifik
4. Klik **Apply** untuk menerapkan filter

### Refresh Data:
1. Klik tombol **"Refresh Data"** di pojok kanan atas
2. Data akan di-reload ulang
3. Muncul notifikasi konfirmasi

## File yang Dimodifikasi

### 1. MonitoringPresensiResource.php
- Update kolom tabel dengan format yang lebih baik
- Tambah color coding untuk status dan jam masuk
- Perbaiki query dengan eager loading
- Tambah filter status dan karyawan

### 2. ListMonitoringPresensis.php
- Tambah header action untuk refresh
- Tambah empty state yang informatif
- Tambah notifikasi setelah refresh

## Keuntungan Perbaikan

✅ **Data Muncul**: Relasi karyawan, cabang, departemen tampil
✅ **Visual Jelas**: Color coding untuk status dan keterlambatan
✅ **Filter Lengkap**: Tanggal, status, dan karyawan
✅ **User Friendly**: Empty state dan notifikasi yang jelas
✅ **Performance**: Eager loading untuk mengurangi N+1 query
✅ **Responsive**: Tabel responsive dengan pagination

## Troubleshooting

### Data Tidak Muncul:
1. Pastikan ada data presensi di database
2. Cek filter tanggal (default hari ini)
3. Pastikan relasi karyawan sudah benar
4. Klik refresh data

### Status Tidak Sesuai:
1. Cek data `status` di tabel presensi
2. Pastikan `jam_in` dan `kode_jam_kerja` terisi
3. Verifikasi relasi ke tabel `jam_kerja`

### Foto Tidak Muncul:
1. Pastikan path foto benar di `foto_in` dan `foto_out`
2. Cek file foto ada di storage
3. Pastikan storage link sudah dibuat: `php artisan storage:link`

## Notes
- Default menampilkan data hari ini untuk performance
- Eager loading digunakan untuk optimasi query
- Color coding membantu identifikasi cepat status karyawan
- Filter memudahkan pencarian data spesifik