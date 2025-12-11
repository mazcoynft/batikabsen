# Update Sistem Piket - Desember 2025

## Perubahan yang Dilakukan

### 1. Perubahan Jenis Piket
**Sebelumnya:**
- Piket Mingguan
- Piket Khusus
- Hari Libur

**Sekarang:**
- **Mingguan** - Rp 25.000/hari
- **Hari Libur** - Rp 50.000/hari

### 2. Penghapusan Field
❌ **Dihapus:**
- Field `jumlah_hari_libur` (tidak diperlukan lagi)
- Jenis piket "Khusus"

✅ **Ditambahkan:**
- Field `nama_lembaga` (untuk mencatat lembaga tempat piket)

### 3. Perhitungan Nominal Otomatis
**Formula Baru:**
- Jika jenis piket = "Mingguan": `nominal = jumlah_hari × 25.000`
- Jika jenis piket = "Hari Libur": `nominal = jumlah_hari × 50.000`

**Contoh:**
- Piket Mingguan 5 hari = 5 × 25.000 = Rp 125.000
- Piket Hari Libur 2 hari = 2 × 50.000 = Rp 100.000

### 4. Perubahan Form Frontend
**Urutan Input Baru:**
1. Jenis Piket (dropdown: Mingguan / Hari Libur)
2. Tanggal Awal Piket
3. Tanggal Akhir Piket (otomatis hitung jumlah hari)
4. Nama Lembaga
5. Keterangan
6. Nominal Piket (otomatis dihitung, read-only)

### 5. Perubahan Backend
**PengajuanPiketResource:**
- Update form view untuk menampilkan field baru
- Update tabel untuk menampilkan kolom lembaga
- Update filter jenis piket
- Update badge color untuk jenis piket baru

### 6. Perubahan Laporan
**Laporan Piket:**
- Hapus kolom "QTY H.Libur"
- Tambah kolom "Lembaga"
- Update header tabel
- Update perhitungan total

**Format Tabel Laporan:**
| No | Tanggal | Jenis Piket | Lembaga | QTY Hari | Satuan | Total | Ket |
|----|---------|-------------|---------|----------|--------|-------|-----|

## File yang Diubah

### Database
- `database/migrations/2025_12_09_160210_update_pengajuan_pikets_table_remove_jumlah_hari_libur.php`
  - Hapus kolom `jumlah_hari_libur`
  - Tambah kolom `nama_lembaga`

### Model
- `app/Models/PengajuanPiket.php`
  - Update fillable fields

### Frontend
- `resources/views/frontend/piket/index.blade.php`
  - Update form input
  - Update JavaScript perhitungan nominal
  - Hapus field jumlah_hari_libur
  - Tambah field nama_lembaga

### Controller
- `app/Http/Controllers/PiketController.php`
  - Update validation rules
  - Update perhitungan nominal
  - Update Telegram notification message

### Backend Resource
- `app/Filament/Resources/PengajuanPiketResource.php`
  - Update form schema
  - Update table columns
  - Update filters
  - Update notification messages

### Laporan
- `app/Filament/Resources/LaporanPiketResource.php`
  - Update table columns
  - Hapus kolom jumlah_hari_libur
  - Tambah kolom nama_lembaga

- `resources/views/filament/resources/laporan-piket-resource/pages/generate-laporan-piket.blade.php`
  - Update preview tabel
  - Update perhitungan total

- `resources/views/pdf/laporan-piket.blade.php`
  - Update PDF template
  - Update header tabel
  - Update body tabel

## Cara Penggunaan Baru

### Pengajuan Piket (Frontend):
1. Buka menu **Piket** dari dashboard
2. Pilih **Jenis Piket** (Mingguan atau Hari Libur)
3. Pilih **Tanggal Awal** dan **Tanggal Akhir**
4. Sistem otomatis menghitung jumlah hari
5. Masukkan **Nama Lembaga**
6. Masukkan **Keterangan**
7. Sistem otomatis menghitung nominal berdasarkan jenis piket
8. Klik **Submit Pengajuan**

### Approval Piket (Backend):
1. Buka menu **Presensi → Pengajuan Piket**
2. Lihat detail pengajuan
3. Klik **Approve** atau **Reject**
4. Notifikasi Telegram otomatis terkirim

### Generate Laporan:
1. Buka menu **Laporan → Laporan Piket**
2. Klik **Generate Laporan**
3. Pilih karyawan, bulan, dan tahun
4. Klik **Preview Laporan**
5. Jika sudah sesuai, klik **Download PDF**

## Migration
Jalankan migration untuk update database:
```bash
php artisan migrate
```

## Testing
Setelah update, pastikan untuk:
1. ✅ Test form pengajuan piket di frontend
2. ✅ Test perhitungan nominal otomatis
3. ✅ Test approval/reject di backend
4. ✅ Test notifikasi Telegram
5. ✅ Test generate laporan PDF
6. ✅ Verifikasi data lama masih bisa ditampilkan

## Notes
- Data piket lama yang memiliki field `jumlah_hari_libur` akan tetap tersimpan di database (kolom sudah dihapus)
- Untuk data lama, kolom `nama_lembaga` akan bernilai NULL
- Sistem sudah handle NULL value dengan menampilkan "-" di laporan
