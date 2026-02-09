# Sistem Inventaris Barang

Aplikasi pengelolaan data inventaris barang untuk Dinas Komunikasi Informatika dan Persandian (Diskominfo) Kabupaten Hulu Sungai Utara.

## Tech Stack

**Backend:** Laravel 12.47.0, PHP 8.2.12, MySQL, Laravel Sanctum 4.2.4

**Frontend:** Vue.js 3.5.27, Vue Router 4.6.4, Vite 7.2.5, Axios 1.13.2, SheetJS 0.18.5

## Fitur

- Login & Autentikasi (Laravel Sanctum)
- Dashboard Statistik (Total Aset, Total Unit, Total Aktivitas, Kondisi Baik)
- Donut chart kondisi barang dengan animasi fill clockwise
- Animasi counting statistik 
- Aktivitas Terbaru (5 log terakhir di dashboard)
- CRUD Data Barang
- Upload gambar barang (drag & drop, max 2MB, format JPG/PNG/GIF/WebP)
- Pengelolaan gambar (ubah/hapus saat update)
- Preview gambar barang di tabel dengan modal zoom
- Import data dari file Excel (drag & drop)
- Export data ke Excel
- Bulk Delete (hapus banyak data sekaligus)
- Pencarian real-time (kode aset, kode barang, nama aset, penanggung jawab)
- Pagination (10, 25, 50, 100, 250 per halaman)
- Generate label barang (PNG) dengan kode barang & tahun pengadaan
- Riwayat aktivitas
- Manajemen Akun (ganti username & password)
- Dark mode / Light mode dengan transisi smooth (tersimpan di localStorage)
- Responsive design (desktop, tablet, mobile)
- Badge kondisi dengan desain kotak dan warna konsisten


## Instalasi

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### Konfigurasi Database

1. Buat database baru di MySQL:
```sql
CREATE DATABASE inventaris_barang;
```

2. Buka file `.env` di folder backend dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventaris_barang
DB_USERNAME=root
DB_PASSWORD=
```

3. Jalankan migrasi database:
```bash
cd backend
php artisan migrate
```

4. (Opsional) Jalankan seeder untuk data awal:
```bash
php artisan db:seed
```

### Frontend

```bash
cd frontend
npm install
```

## Menjalankan Aplikasi

**Terminal 1 - Backend:**
```bash
cd backend
php artisan serve
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm run dev
```

Akses aplikasi di `http://localhost:5173`

## Struktur Database

### Tabel `barang`

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| id | bigint | Primary key |
| kode_aset | varchar | Kode aset (boleh sama, id yang membedakan) |
| kode_barang | varchar | Kode barang |
| nama_aset | varchar | Nama aset |
| jenis_aset | varchar | Jenis/kategori |
| jumlah | integer | Jumlah unit |
| kondisi | enum | Baik/Rusak Ringan/Rusak Berat |
| lokasi_penyimpanan | varchar | Lokasi |
| penanggung_jawab | varchar | PIC |
| tahun_perolehan | integer | Tahun perolehan |
| gambar | varchar | Nama file gambar (nullable) |
| keterangan | text | Keterangan tambahan (nullable) |

### Tabel `riwayat`

| Kolom | Tipe | Deskripsi |
|-------|------|-----------||
| id | bigint | Primary key |
| barang_id | bigint | Foreign key ke tabel barang (nullable) |
| kode_barang | varchar | Kode barang |
| nama_aset | varchar | Nama aset |
| jenis_perubahan | enum | Tambah/Edit/Hapus/Tambah Gambar/Ubah Gambar/Hapus Gambar |
| stok_sebelum | integer | Stok sebelum perubahan (nullable) |
| stok_sesudah | integer | Stok sesudah perubahan (nullable) |
| keterangan | text | Detail perubahan (nullable) |
| created_at | timestamp | Waktu perubahan |

## Validasi Data

### Validasi File Gambar

**Format yang Didukung:**
- JPG/JPEG (image/jpeg)
- PNG (image/png)
- GIF (image/gif)
- WebP (image/webp)

**Ukuran Maksimal:** 2MB (2048 KB)

**Lokasi Penyimpanan:**
- Path: `backend/storage/app/public/gambar_barang/`
- Format nama file: `{timestamp}_{uniqid}.{ext}`
- Contoh: `1738234567_65a9c3f1b2d4e.jpg`

**Fitur Pengelolaan Gambar:**
- **Tambah Gambar:** Upload gambar baru saat menambah atau mengedit data
- **Ubah Gambar:** Ganti gambar yang sudah ada dengan gambar baru
- **Hapus Gambar:** Hapus gambar dari data barang
- Semua perubahan gambar tercatat di riwayat aktivitas

### Validasi Boundary Values

| Field | Validasi | Keterangan |
|-------|----------|------------|
| Jumlah | Minimum: 1 | Harus bilangan positif |
| Tahun Perolehan | Minimum: 2000<br>Maksimum: Tahun sekarang | Tahun 4 digit yang valid |
| Kode Aset | Boleh duplikat | ID otomatis yang membedakan |
| Nama Aset | 1-255 karakter | Wajib diisi |
| Kode Barang | 1-255 karakter | Wajib diisi |
| Kondisi | Enum | Baik / Rusak Ringan / Rusak Berat |

### Validasi Akun

| Field | Validasi | Keterangan |
|-------|----------|------------|
| Username | Minimum: 3 karakter | Wajib diisi, unik |
| Password | Minimum: 6 karakter | Wajib diisi untuk login |

## Riwayat Aktivitas

### Informasi yang Dicatat

Sistem mencatat semua perubahan data dengan detail lengkap:

**Jenis Perubahan:**
- **Tambah** - Data baru ditambahkan
- **Edit** - Data yang sudah ada diubah
- **Hapus** - Data dihapus
- **Tambah Gambar** - Gambar baru ditambahkan ke barang
- **Ubah Gambar** - Gambar barang diganti
- **Hapus Gambar** - Gambar barang dihapus
- **Edit Stok (+/-jumlah)** - Perubahan jumlah stok dengan keterangan

**Data yang Terekam:**
- Waktu perubahan (timestamp)
- Kode barang dan nama aset
- Jenis perubahan (dengan badge warna)
- Stok sebelum dan sesudah perubahan
- Keterangan detail perubahan

## Desain UI & Animasi

### Dashboard Statistik

**Stat Cards:**
- Animasi counting dari 0 ke nilai akhir
- Gradient berwarna: biru (Total Aset), hijau (Total Unit), oranye (Total Aktivitas), teal (Kondisi Baik)

**Donut Chart:**
- Animasi fill melingkar (clockwise)
- Proporsi kondisi barang dengan warna berbeda
- Transisi smooth saat data berubah

**Badge Kondisi:**
- Desain kotak (bukan pil)
- Warna konsisten:
  - Hijau: Baik
  - Kuning: Rusak Ringan
  - Merah: Rusak Berat

**Tabel Data:**
- Kolom jumlah center-aligned
- Thumbnail gambar 48x48 piksel
- Modal zoom untuk preview gambar besar

**Dark Mode:**
- Toggle switch di sidebar
- Transisi smooth (0.4 detik)
- Preferensi tersimpan di localStorage

## API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | /api/login | Login |
| POST | /api/logout | Logout (hapus token) |
| GET | /api/user | Get profil user |
| PUT | /api/akun | Update username/password |
| GET | /api/statistik | Get statistik dashboard |
| GET | /api/barang | Get semua data (pagination) |
| POST | /api/barang | Tambah data |
| GET | /api/barang/{id} | Get detail |
| PUT | /api/barang/{id} | Update data |
| DELETE | /api/barang/{id} | Hapus data |
| POST | /api/barang/import | Import dari Excel |
| POST | /api/barang/bulk-delete | Hapus banyak data |
| GET | /api/riwayat | Get riwayat aktivitas (pagination) |

**Query Parameters untuk GET /api/barang:**
- `page` - Nomor halaman
- `per_page` - Data per halaman (default: 10)
- `search` - Kata kunci pencarian (kode aset, kode barang, nama aset, penanggung jawab)

## Struktur Project

```
Inventaris-Barang/
├─ backend/                          # Laravel 12 (REST API)
│  ├─ app/
│  │  ├─ Http/
│  │  │  └─ Controllers/
│  │  │     ├─ BarangController.php  # CRUD + import barang + log riwayat
│  │  │     └─ RiwayatController.php # List riwayat aktivitas
│  │  ├─ Models/
│  │  │  ├─ Barang.php
│  │  │  ├─ Riwayat.php
│  │  │  └─ User.php
│  │  └─ Providers/
│  ├─ database/
│  │  ├─ migrations/                 # Skema tabel (barang, riwayat, peminjaman, dll)
│  │  ├─ seeders/
│  │  └─ factories/
│  ├─ routes/
│  │  ├─ api.php                     # Endpoint API utama
│  │  ├─ web.php
│  │  └─ console.php
│  ├─ config/
│  ├─ public/
│  ├─ resources/                     # Asset/view Laravel (default)
│  ├─ storage/
│  ├─ tests/                         # Unit/Feature tests + coverage
│  ├─ composer.json
│  └─ .env
│
├─ frontend/                         # Vue 3 + Vite (SPA)
│  ├─ src/
│  │  ├─ views/                      # Halaman (router pages)
│  │  │  ├─ Login.vue
│  │  │  ├─ Dashboard.vue
│  │  │  ├─ Home.vue
│  │  │  ├─ TambahData.vue
│  │  │  ├─ UpdateData.vue
│  │  │  ├─ UpdateForm.vue
│  │  │  ├─ HapusData.vue
│  │  │  ├─ Riwayat.vue
│  │  │  ├─ GenerateLabel.vue
│  │  │  └─ Akun.vue
│  │  ├─ router/
│  │  │  └─ index.js                  # Routing + route guard (cek token)
│  │  ├─ components/
│  │  │  └─ HelloWorld.vue            # Komponen contoh (default)
│  │  ├─ assets/
│  │  ├─ api.js                       # Axios instance (baseURL dari VITE_API_URL)
│  │  ├─ App.vue
│  │  └─ main.js
│  ├─ public/
│  ├─ dist/                           # Hasil build (muncul setelah build)
│  ├─ vite.config.js
│  ├─ package.json
│  ├─ .env
│  ├─ .env.development
│  └─ .env.production
│
├─ README.md
├─ TEST_CASES.md
├─ Manual Book.html
└─ Manual Book.css
```

## Testing

### Menjalankan Unit Test (PHPUnit)

```bash
cd backend
php artisan test
```

Atau untuk menjalankan test dengan detail:
```bash
php artisan test --verbose
```

Menjalankan test spesifik:
```bash
php artisan test --filter=BarangTest
```

### Test Cases

**White Box Testing (60 tests):**
- CREATE Positive: 5 tests
- CREATE Negative: 5 tests
- READ Positive: 5 tests
- UPDATE Positive: 5 tests
- UPDATE Negative: 5 tests
- DELETE Positive: 5 tests
- Statistik & Dashboard: 6 tests
- Akun Positive: 3 tests
- Akun Negative: 4 tests
- Search: 6 tests
- Bulk Delete: 4 tests
- Generate Label: 3 tests
- Image/Foto Barang: 4 tests

**Black Box Testing - Boundary Value Analysis (10 tests):**
- Jumlah: minimum valid, below minimum, negatif
- Tahun perolehan: minimum valid, below minimum, maximum valid, above maximum
- Kode aset: empty string
- Nama aset: panjang minimum, panjang maksimum

**Total: 70 tests, 216 assertions**

Lihat file `TEST_CASES.md` untuk dokumentasi lengkap test cases.

### Code Coverage dengan Xdebug

**Prasyarat:** Pastikan Xdebug sudah terinstall dan dikonfigurasi.

1. Cek apakah Xdebug aktif:
```bash
php -v
```
Harus muncul "with Xdebug" di output.

2. Konfigurasi Xdebug di `php.ini`:
```ini
[xdebug]
zend_extension=xdebug
xdebug.mode=coverage
```

3. Jalankan test dengan code coverage:
```bash
cd backend
php artisan test --coverage
```

4. Generate HTML report:
```bash
XDEBUG_MODE=coverage php artisan test --coverage-html tests/coverage-html
```

5. Buka `tests/coverage-html/index.html` di browser untuk melihat report.

## Format Excel untuk Import

| Kode Aset | Kode Barang | Nama Aset | Jenis Aset | Jumlah | Kondisi | Lokasi | Penanggung Jawab | Tahun |
|-----------|-------------|-----------|------------|--------|---------|--------|------------------|-------|
| EGOV01 | 2025.001/EGOV | Camera Video | Peralatan IT | 1 | Baik | Ruang Server | John Doe | 2025 |

## Contact

Untuk pertanyaan, dukungan, atau permintaan pengembangan:
- Buat **Issue**: https://github.com/DimasImamGhifari/Website-Inventaris-Barang/issues
- Email: **imamghifaridimas@gmail.com**

---

Diskominfo Kabupaten Hulu Sungai Utara
