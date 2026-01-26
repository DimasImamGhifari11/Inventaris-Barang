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

## Format Excel untuk Import

| Kode Aset | Kode Barang | Nama Aset | Jenis Aset | Jumlah | Kondisi | Lokasi | Penanggung Jawab | Tahun |
|-----------|-------------|-----------|------------|--------|---------|--------|------------------|-------|
| EGOV01 | 2025.001/EGOV | Camera Video | Peralatan IT | 1 | Baik | Ruang Server | John Doe | 2025 |

## Lisensi

MIT License

---

Diskominfo Kabupaten Hulu Sungai Utara
