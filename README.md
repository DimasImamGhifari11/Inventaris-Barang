# Sistem Inventaris Barang

Aplikasi pengelolaan data inventaris barang untuk Dinas Komunikasi Informatika dan Persandian (Diskominfo) Kabupaten Hulu Sungai Utara.

## Tech Stack

**Backend:** Laravel 12, PHP 8.2+, MySQL

**Frontend:** Vue.js 3, Vue Router, Axios, SheetJS

## Fitur

- Login & Autentikasi
- CRUD Data Barang
- Import data dari file Excel (drag & drop)
- Export data ke Excel
- Pencarian real-time
- Pagination
- Generate label barang (PNG) dengan kode barang & tahun pengadaan
- Riwayat aktivitas
- Dark mode / Light mode (tersimpan di localStorage)
- Responsive design (desktop, tablet, mobile)

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
| kode_aset | varchar | Kode aset unik |
| kode_barang | varchar | Kode barang |
| nama_aset | varchar | Nama aset |
| jenis_aset | varchar | Jenis/kategori |
| jumlah | integer | Jumlah unit |
| kondisi | enum | Baik/Rusak Ringan/Rusak Berat |
| lokasi_penyimpanan | varchar | Lokasi |
| penanggung_jawab | varchar | PIC |
| tahun_perolehan | integer | Tahun perolehan |

## API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | /api/login | Login |
| GET | /api/barang | Get semua data (pagination) |
| POST | /api/barang | Tambah data |
| GET | /api/barang/{id} | Get detail |
| PUT | /api/barang/{id} | Update data |
| DELETE | /api/barang/{id} | Hapus data |
| POST | /api/barang/import | Import dari Excel |

**Query Parameters untuk GET /api/barang:**
- `page` - Nomor halaman
- `per_page` - Data per halaman (default: 10)
- `search` - Kata kunci pencarian

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

**White Box Testing (30 tests):**
- CREATE Positive: 5 tests
- CREATE Negative: 5 tests
- READ Positive: 5 tests
- UPDATE Positive: 5 tests
- UPDATE Negative: 5 tests
- DELETE Positive: 5 tests

**Black Box Testing - Boundary Value Analysis (10 tests):**
- Jumlah: minimum valid, below minimum, negatif
- Tahun perolehan: minimum valid, below minimum, maximum valid, above maximum
- Kode aset: empty string
- Nama aset: panjang minimum, panjang maksimum

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
- Buat **Issue**: https://github.com/DimasImamGhifari11/Website-Inventaris-Barang/issues
- Email: **imamghifaridimas@gmail.com**

---

Diskominfo Kabupaten Hulu Sungai Utara
