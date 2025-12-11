# Fitur Generate Hari Libur Otomatis

## Overview
Fitur untuk mengambil data hari libur resmi Indonesia secara otomatis dari API dan menyimpannya ke database.

## Lokasi Menu
**Settings → Hari Libur**

## Fitur Utama

### 1. Tombol "Generate Hari Libur"
- Icon: Arrow Path (refresh)
- Warna: Success (hijau)
- Lokasi: Header actions (pojok kanan atas)

### 2. Konfirmasi Modal
Sebelum generate, akan muncul modal konfirmasi dengan informasi:
- **Heading**: "Generate Hari Libur"
- **Description**: "Apakah Anda yakin ingin mengambil data hari libur resmi Indonesia untuk tahun ini? Data yang sudah ada akan tetap dipertahankan."
- **Submit Button**: "Ya, Generate"

### 3. Proses Generate
1. Mengambil data dari API: `https://api-harilibur.vercel.app/api?year={tahun}`
2. Tahun yang diambil: Tahun berjalan (current year)
3. Cek duplikasi: Hanya insert data yang belum ada
4. Data yang sudah ada akan di-skip (tidak di-update)

### 4. Notifikasi Hasil
**Berhasil:**
- Title: "Berhasil!"
- Body: "Berhasil menambahkan {X} hari libur. {Y} data sudah ada sebelumnya."
- Type: Success (hijau)

**Gagal:**
- Title: "Gagal mengambil data" atau "Error"
- Body: Pesan error detail
- Type: Danger (merah)

## API yang Digunakan

### Endpoint
```
https://dayoffapi.vercel.app/api
```

### Response Format
```json
[
  {
    "tanggal": "2025-01-01",
    "keterangan": "Tahun Baru Masehi"
  },
  {
    "tanggal": "2025-01-29",
    "keterangan": "Tahun Baru Imlek 2576 Kongzili"
  },
  {
    "tanggal": "2025-03-29",
    "keterangan": "Hari Suci Nyepi Tahun Baru Saka 1947"
  }
]
```

### Data yang Disimpan
- **tanggal**: `tanggal` (format: YYYY-MM-DD)
- **keterangan**: `keterangan` (nama hari libur)

### Catatan
- API mengembalikan semua hari libur dari berbagai tahun
- Sistem akan otomatis filter hanya untuk tahun berjalan
- Data yang sudah ada tidak akan di-update atau dihapus

## Hari Libur Resmi Indonesia

API ini menyediakan data hari libur resmi Indonesia yang meliputi:
- Tahun Baru Masehi
- Tahun Baru Imlek
- Hari Raya Nyepi
- Wafat Isa Al Masih
- Hari Buruh Internasional
- Kenaikan Isa Al Masih
- Hari Raya Waisak
- Hari Lahir Pancasila
- Hari Raya Idul Fitri
- Hari Raya Idul Adha
- Tahun Baru Islam
- Hari Kemerdekaan RI
- Maulid Nabi Muhammad SAW
- Hari Raya Natal
- Dan hari libur nasional lainnya

## Cara Penggunaan

1. Buka menu **Settings → Hari Libur**
2. Klik tombol **"Generate Hari Libur"** di pojok kanan atas
3. Baca konfirmasi modal
4. Klik **"Ya, Generate"**
5. Tunggu proses selesai
6. Lihat notifikasi hasil
7. Data hari libur akan muncul di tabel

## Keuntungan

✅ **Otomatis**: Tidak perlu input manual satu per satu
✅ **Akurat**: Data dari sumber resmi
✅ **Update**: Selalu mendapatkan data terbaru
✅ **Aman**: Tidak menimpa data yang sudah ada
✅ **Cepat**: Proses hanya beberapa detik
✅ **Lengkap**: Semua hari libur nasional Indonesia

## File yang Dimodifikasi

- `app/Filament/Resources/HariLiburResource/Pages/ManageHariLiburs.php`
  - Tambah action "Generate Hari Libur"
  - Tambah method `generateHariLibur()`

## Dependencies

- **Illuminate\Support\Facades\Http**: Untuk HTTP request ke API
- **API Hari Libur**: https://dayoffapi.vercel.app/

## Notes

- Data yang sudah ada di database tidak akan di-update atau dihapus
- Hanya data baru yang akan ditambahkan
- Jika API tidak dapat diakses, akan muncul notifikasi error
- Tahun yang di-generate adalah tahun berjalan saat tombol diklik
- Untuk tahun lain, bisa generate ulang di tahun tersebut atau input manual

## Error Handling

1. **API tidak dapat diakses**: Notifikasi "Gagal mengambil data"
2. **Response tidak valid**: Notifikasi error dengan detail
3. **Database error**: Notifikasi error dengan pesan exception
