# Test Cases - Sistem Inventaris Barang
(Didasarkan pada implementasi test aktual)

Catatan: Dokumentasi ini mencerminkan test case yang telah diimplementasikan dalam kode,
bukan semua skenario yang direncanakan. Jumlah test case aktual adalah 70 berdasarkan
hasil php artisan test.

## A. WHITE BOX TESTING

Catatan: Bagian ini mencakup test case yang telah diimplementasikan dalam kode.

---

### 1. CREATE - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-CR-P01 | Tambah data dengan semua field valid | 1. Buka halaman Tambah Data<br>2. Isi semua field dengan data valid<br>3. Klik tombol Simpan | kode_aset: "EGOV01"<br>kode_barang: "2025.001/EGOV"<br>nama_aset: "Laptop Dell"<br>jenis_aset: "Peralatan IT"<br>jumlah: 5<br>kondisi: "Baik"<br>lokasi: "Ruang Server"<br>penanggung_jawab: "John Doe"<br>tahun: 2025 | Data berhasil disimpan, muncul notifikasi sukses | ✅ |
| 2 | WB-CR-P02 | Import data dari Excel | 1. Buka halaman Tambah Data<br>2. Drag & drop file Excel valid<br>3. Klik Import | File Excel dengan data valid | Data berhasil diimport, muncul notifikasi sukses | ✅ |
| 3 | WB-CR-P03 | Tambah data tercatat di Riwayat | 1. Tambah data baru<br>2. Buka halaman Riwayat | Data baru ditambahkan | Riwayat mencatat "Tambah Data" dengan stok sebelum dan sesudah | ✅ |
| 4 | WB-CR-P04 | Tambah data dengan kondisi Rusak Ringan | 1. Buka Tambah Data<br>2. Pilih kondisi "Rusak Ringan"<br>3. Klik Simpan | kondisi: "Rusak Ringan" | Data tersimpan dengan kondisi Rusak Ringan | ✅ |
| 5 | WB-CR-P05 | Tambah data dengan kondisi Rusak Berat | 1. Buka Tambah Data<br>2. Pilih kondisi "Rusak Berat"<br>3. Klik Simpan | kondisi: "Rusak Berat" | Data tersimpan dengan kondisi Rusak Berat | ✅ |

---

### 2. CREATE - Negative Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-CR-N01 | Tambah data dengan kode_aset duplikat (diperbolehkan) | 1. Buka halaman Tambah Data<br>2. Isi kode_aset yang sudah ada<br>3. Klik Simpan | kode_aset: "EGOV01" (sudah ada) | Data berhasil disimpan, id berbeda yang membedakan | ✅ |
| 2 | WB-CR-N02 | Tambah data dengan field kosong | 1. Buka halaman Tambah Data<br>2. Kosongkan field required<br>3. Klik Simpan | nama_aset: "" (kosong) | Form tidak bisa disubmit | ✅ |
| 3 | WB-CR-N03 | Tambah data dengan jumlah 0 | 1. Buka halaman Tambah Data<br>2. Isi jumlah dengan 0<br>3. Klik Simpan | jumlah: 0 | Gagal simpan, validasi error | ✅ |
| 4 | WB-CR-N04 | Tambah data dengan tahun tidak valid | 1. Buka halaman Tambah Data<br>2. Isi tahun dengan 1999<br>3. Klik Simpan | tahun_perolehan: 1999 | Gagal simpan, validasi error | ✅ |
| 5 | WB-CR-N05 | Import Excel dengan format tidak valid | 1. Upload file bukan Excel | File: document.pdf | Gagal import, muncul pesan error | ✅ |

---

### 3. READ - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-RD-P01 | Menampilkan semua data di halaman Home | 1. Login<br>2. Buka halaman Home | - | Semua data ditampilkan dalam tabel dengan pagination | ✅ |
| 2 | WB-RD-P02 | Pencarian data dengan keyword valid | 1. Buka halaman Home<br>2. Ketik keyword di search box | search: "Laptop" | Data yang mengandung "Laptop" ditampilkan | ✅ |
| 3 | WB-RD-P03 | Navigasi pagination | 1. Buka halaman Home<br>2. Klik tombol Next<br>3. Klik tombol Prev | - | Navigasi halaman berfungsi dengan benar | ✅ |
| 4 | WB-RD-P04 | Menampilkan data Riwayat | 1. Buka halaman Riwayat | - | Log aktivitas ditampilkan dengan lengkap | ✅ |
| 5 | WB-RD-P05 | Tetap di halaman saat clear search | 1. Buka page 3<br>2. Search keyword<br>3. Hapus keyword | - | Kembali ke page 3 (bukan page 1) | ✅ |

---

### 4. UPDATE - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-UP-P01 | Update data dengan data valid | 1. Buka Update Data<br>2. Klik edit<br>3. Ubah data<br>4. Klik Simpan | nama_aset: "Laptop Asus ROG" | Data berhasil diupdate, notifikasi sukses | ✅ |
| 2 | WB-UP-P02 | Update jumlah (stok bertambah) | 1. Buka form update<br>2. Ubah jumlah dari 5 ke 10<br>3. Klik Simpan | jumlah: 5 → 10 | Riwayat mencatat "Edit Stok (+5)" | ✅ |
| 3 | WB-UP-P03 | Update jumlah (stok berkurang) | 1. Buka form update<br>2. Ubah jumlah dari 10 ke 7<br>3. Klik Simpan | jumlah: 10 → 7 | Riwayat mencatat "Edit Stok (-3)" | ✅ |
| 4 | WB-UP-P04 | Update dengan keterangan | 1. Ubah data<br>2. Isi keterangan<br>3. Klik Simpan | keterangan: "Perbaikan unit" | Keterangan tercatat di riwayat | ✅ |
| 5 | WB-UP-P05 | Tetap di halaman setelah update | 1. Buka page 3<br>2. Edit data<br>3. Simpan | - | Kembali ke page 3 (bukan page 1) | ✅ |

---

### 5. UPDATE - Negative Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-UP-N01 | Update tanpa perubahan data | 1. Buka form update<br>2. Tidak mengubah apapun<br>3. Klik Simpan | Tidak ada perubahan | Muncul notifikasi "Tidak ada perubahan data" | ✅ |
| 2 | WB-UP-N02 | Update dengan kode_aset duplikat (diperbolehkan) | 1. Ubah kode_aset ke yang sudah ada<br>2. Klik Simpan | kode_aset: "EGOV02" (milik data lain) | Data berhasil diupdate, kode_aset sama diperbolehkan | ✅ |
| 3 | WB-UP-N03 | Update dengan jumlah 0 | 1. Ubah jumlah ke 0<br>2. Klik Simpan | jumlah: 0 | Gagal simpan, validasi error | ✅ |
| 4 | WB-UP-N04 | Update dengan field kosong | 1. Kosongkan nama_aset<br>2. Klik Simpan | nama_aset: "" | Form tidak bisa disubmit | ✅ |
| 5 | WB-UP-N05 | Update data yang tidak ada | 1. Akses URL /update-data/9999 | id: 9999 (tidak ada) | Halaman menampilkan "Data tidak ditemukan" | ✅ |

---

### 6. DELETE - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-DL-P01 | Hapus single data | 1. Buka Hapus Data<br>2. Klik icon hapus<br>3. Konfirmasi "Ya, Hapus" | id: 1 | Data terhapus, notifikasi sukses | ✅ |
| 2 | WB-DL-P02 | Hapus multiple data (bulk delete) | 1. Checklist 3 data<br>2. Klik "Hapus Terpilih"<br>3. Konfirmasi | ids: [1, 2, 3] | 3 data terhapus | ✅ |
| 3 | WB-DL-P03 | Batalkan hapus data | 1. Klik icon hapus<br>2. Klik "Batal" di modal | - | Modal tertutup, data tidak terhapus | ✅ |
| 4 | WB-DL-P04 | Select all checkbox | 1. Klik checkbox di header tabel | - | Semua data terselect | ✅ |
| 5 | WB-DL-P05 | Hapus data tercatat di Riwayat | 1. Hapus satu data<br>2. Buka Riwayat | - | Riwayat mencatat "Hapus Data" | ✅ |

---

### 7. STATISTIK & DASHBOARD - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-ST-P01 | Menampilkan statistik Total Aset | 1. Login<br>2. Buka halaman Home | - | Stat card Total Aset menampilkan jumlah record barang | ✅ |
| 2 | WB-ST-P02 | Menampilkan statistik Total Unit Barang | 1. Buka halaman Home | - | Stat card Total Unit menampilkan sum dari kolom jumlah | ✅ |
| 3 | WB-ST-P03 | Menampilkan statistik Total Aktivitas | 1. Buka halaman Home | - | Stat card Total Aktivitas menampilkan jumlah record riwayat | ✅ |
| 4 | WB-ST-P04 | Menampilkan statistik Kondisi Baik | 1. Buka halaman Home | - | Stat card Kondisi Baik menampilkan jumlah aset dengan kondisi "Baik" | ✅ |
| 5 | WB-ST-P05 | Donut chart menampilkan data kondisi | 1. Buka halaman Home | - | Donut chart menampilkan proporsi Baik, Rusak Ringan, Rusak Berat dengan animasi fill | ✅ |
| 6 | WB-ST-P06 | Aktivitas Terbaru menampilkan 5 log terakhir | 1. Buka halaman Home | - | Daftar 5 aktivitas terbaru ditampilkan dengan badge dan waktu | ✅ |

---

### 8. AKUN - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-AK-P01 | Ganti username dengan data valid | 1. Buka halaman Akun<br>2. Isi username baru<br>3. Klik Simpan | username: "admin_baru" | Username berhasil diubah, notifikasi sukses | ✅ |
| 2 | WB-AK-P02 | Ganti password dengan data valid | 1. Buka halaman Akun<br>2. Isi password lama, baru, dan konfirmasi<br>3. Klik Simpan | password_lama: "123456"<br>password_baru: "654321"<br>konfirmasi: "654321" | Password berhasil diubah, notifikasi sukses | ✅ |
| 3 | WB-AK-P03 | Login dengan password baru setelah ganti | 1. Ganti password<br>2. Logout<br>3. Login dengan password baru | password: "654321" | Login berhasil dengan password baru | ✅ |

---

### 9. AKUN - Negative Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-AK-N01 | Ganti password dengan password lama salah | 1. Buka halaman Akun<br>2. Isi password lama yang salah<br>3. Klik Simpan | password_lama: "salah123" | Gagal, muncul pesan "Password lama salah" | ✅ |
| 2 | WB-AK-N02 | Ganti password dengan konfirmasi tidak cocok | 1. Isi password baru dan konfirmasi berbeda<br>2. Klik Simpan | password_baru: "abc123"<br>konfirmasi: "xyz789" | Form tidak bisa disubmit, pesan error konfirmasi | ✅ |
| 3 | WB-AK-N03 | Ganti username kurang dari 3 karakter | 1. Isi username dengan 2 karakter<br>2. Klik Simpan | username: "ab" | Gagal, validasi error minimum 3 karakter | ✅ |
| 4 | WB-AK-N04 | Ganti password kurang dari 6 karakter | 1. Isi password baru kurang dari 6 karakter<br>2. Klik Simpan | password_baru: "abc" | Gagal, validasi error minimum 6 karakter | ✅ |

---

### 10. GENERATE LABEL - Positive Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-GL-P01 | Generate label single data | 1. Buka Generate Label<br>2. Klik tombol Generate pada satu data | id: 1 | Informasi barang berhasil diambil untuk keperluan generate label | ✅ |
| 2 | WB-GL-P02 | Generate label data tidak ditemukan | 1. Akses endpoint dengan ID yang tidak ada | id: 9999 | Respons error 404 dengan pesan "Data tidak ditemukan" | ✅ |
| 3 | WB-GL-P03 | Generate label dengan data valid | 1. Akses endpoint dengan ID valid | id: 1 | Respons sukses dengan informasi barang yang diperlukan untuk generate label | ✅ |

Catatan: Test case untuk fitur Generate Label sekarang telah diimplementasikan
dalam kode test aktual dan berjalan dengan sukses.

---

### 11. IMAGE/FOTO BARANG - Test Cases

| No | Test Case ID | Nama Test Case | Langkah Pengujian | Input Data | Expected Result | Status |
|----|--------------|----------------|-------------------|------------|-----------------|--------|
| 1 | WB-IMG-P01 | Upload gambar saat tambah data barang | 1. Buka halaman Tambah Data<br>2. Isi semua field dengan data valid<br>3. Upload file gambar (jpg/png)<br>4. Klik Simpan | File gambar: test-image.jpg (format valid) | Data berhasil disimpan dengan gambar, file tersimpan di storage/gambar_barang/ | ✅ |
| 2 | WB-IMG-P02 | Update barang dengan upload gambar baru | 1. Buka data barang yang sudah punya gambar<br>2. Upload gambar baru<br>3. Klik Update | File gambar baru: new-image.jpg | Gambar lama terhapus, gambar baru tersimpan, riwayat mencatat "Ubah Gambar" | ✅ |
| 3 | WB-IMG-P03 | Hapus gambar dari barang | 1. Buka data barang yang sudah punya gambar<br>2. Centang opsi "Hapus Gambar"<br>3. Klik Update | Flag hapus_gambar: true | Gambar terhapus dari storage dan database, riwayat mencatat "Hapus Gambar" | ✅ |
| 4 | WB-IMG-N01 | Validasi format file gambar yang tidak valid | 1. Buka halaman Tambah Data<br>2. Isi field dengan data valid<br>3. Upload file non-image (PDF)<br>4. Klik Simpan | File: document.pdf (bukan gambar) | Validasi error muncul, data tidak tersimpan | ✅ |

Catatan: Fitur foto barang memungkinkan preview gambar di semua tabel kecuali riwayat.
Gambar disimpan di storage/gambar_barang/ dengan nama unik (timestamp_uniqid.ext).
Format yang didukung: jpeg, png, jpg, gif, webp dengan maksimal ukuran 2MB.

---

## B. BLACK BOX TESTING

Catatan: Bagian ini mencakup test case yang direncanakan tetapi tidak semuanya
diimplementasikan dalam kode test aktual.

---

### 1. FUNCTIONAL TESTING

| No | Test Case ID | Modul | Nama Test Case | Pre-condition | Langkah Pengujian | Expected Result | Status |
|----|--------------|-------|----------------|---------------|-------------------|-----------------|--------|
| 1 | BB-FN-01 | Login | Login dengan data valid | Di halaman login | 1. Isi username & password valid<br>2. Klik Login | Berhasil masuk ke dashboard | ❌ |
| 2 | BB-FN-02 | Login | Login dengan data tidak valid | Di halaman login | 1. Isi username/password salah<br>2. Klik Login | Gagal login, pesan error muncul | ❌ |
| 3 | BB-FN-03 | Tambah Data | Simpan data baru | Form terisi valid | 1. Isi semua field<br>2. Klik Simpan | Data tersimpan, notifikasi muncul | ❌ |
| 4 | BB-FN-04 | Update Data | Simpan perubahan | Ada perubahan data | 1. Ubah data<br>2. Klik Simpan | Data terupdate, notifikasi muncul | ❌ |
| 5 | BB-FN-05 | Hapus Data | Hapus data | Modal terbuka | 1. Klik "Ya, Hapus" | Data terhapus, notifikasi muncul | ❌ |
| 6 | BB-FN-06 | Riwayat | Menampilkan log aktivitas | Ada aktivitas sebelumnya | 1. Klik menu Riwayat | Log aktivitas ditampilkan dengan detail | ❌ |
| 7 | BB-FN-07 | Statistik | Statistik terupdate setelah tambah data | Data sudah ditambahkan | 1. Tambah data baru<br>2. Buka Home | Angka Total Aset, Total Unit, dan Kondisi terupdate | ❌ |
| 8 | BB-FN-08 | Akun | Ganti username | Login aktif | 1. Buka Akun<br>2. Ganti username<br>3. Simpan | Username berubah, sidebar menampilkan nama baru | ❌ |
| 9 | BB-FN-09 | Akun | Ganti password | Login aktif | 1. Buka Akun<br>2. Isi password lama & baru<br>3. Simpan | Password berubah, bisa login dengan password baru | ❌ |
| 10 | BB-FN-10 | Search | Pencarian berdasarkan penanggung jawab | Data tersedia | 1. Ketik nama penanggung jawab di search<br>2. Lihat hasil | Data dengan penanggung jawab sesuai keyword ditampilkan | ❌ |
| 11 | BB-FN-11 | Generate Label | Generate label aset | Data tersedia | 1. Buka Generate Label<br>2. Klik Generate pada data | Label PNG berhasil di-generate | ❌ |
| 12 | BB-FN-12 | Logout | Logout dari sistem | Sudah login | 1. Klik tombol Logout | Kembali ke halaman login, token dihapus | ❌ |

Catatan: Test case untuk modul Login, Tambah Data, Update Data, Hapus Data,
Riwayat, Akun, Search, Generate Label, dan Logout tercantum dalam dokumentasi
tetapi tidak diimplementasikan dalam kode test aktual.

---

### 2. UI/UX TESTING

| No | Test Case ID | Modul | Nama Test Case | Pre-condition | Langkah Pengujian | Expected Result | Status |
|----|--------------|-------|----------------|---------------|-------------------|-----------------|--------|
| 1 | BB-UI-01 | Dashboard | Animasi donut chart | Halaman Home terbuka | 1. Buka halaman Home<br>2. Perhatikan donut chart | Donut chart muncul dengan animasi fill melingkar (clockwise) | ❌ |
| 2 | BB-UI-02 | Dashboard | Animasi counting statistik | Halaman Home terbuka | 1. Buka halaman Home<br>2. Perhatikan angka stat card | Angka stat card beranimasi dari 0 ke nilai akhir | ❌ |
| 3 | BB-UI-03 | Dashboard | Dark mode toggle | Halaman dashboard terbuka | 1. Klik toggle Dark Mode | Tema berubah dengan transisi smooth, tidak ada flicker | ❌ |
| 4 | BB-UI-04 | Dashboard | Responsive mobile view | Browser di resolusi mobile | 1. Resize browser ke 375px<br>2. Cek stat cards dan tabel | Layout menyesuaikan, stat cards 2 kolom, tabel scrollable | ❌ |
| 5 | BB-UI-05 | Semua Tabel | Kolom Jumlah center-aligned | Halaman dengan tabel terbuka | 1. Buka halaman dengan tabel<br>2. Lihat kolom Jumlah | Angka jumlah rata tengah di semua halaman | ❌ |
| 6 | BB-UI-06 | Semua Tabel | Badge kondisi konsisten | Halaman dengan tabel terbuka | 1. Buka semua halaman tabel | Badge kondisi berbentuk kotak (bukan pill) dengan warna konsisten di semua halaman | ❌ |
| 7 | BB-UI-07 | Riwayat | Pagination 250 rows | Halaman Riwayat terbuka | 1. Buka Riwayat<br>2. Pilih 250 di dropdown per page | Tabel menampilkan hingga 250 baris per halaman | ❌ |
| 8 | BB-UI-08 | Dashboard | Aktivitas Terbaru di samping donut | Halaman Home terbuka | 1. Buka halaman Home | Daftar 5 aktivitas terbaru muncul di samping donut chart | ❌ |

Catatan: Test case untuk UI/UX tercantum dalam dokumentasi tetapi tidak
diimplementasikan dalam kode test aktual.

---

### 3. BOUNDARY VALUE ANALYSIS

| No | Test Case ID | Field | Boundary | Test Value | Expected Result | Status |
|----|--------------|-------|----------|------------|-----------------|--------|
| 1 | BB-BV-01 | jumlah | Minimum valid | 1 | Data tersimpan | ✅ |
| 2 | BB-BV-02 | jumlah | Below minimum | 0 | Validasi error | ✅ |
| 3 | BB-BV-03 | tahun_perolehan | Minimum valid | 2000 | Data tersimpan | ✅ |
| 4 | BB-BV-04 | tahun_perolehan | Below minimum | 1999 | Validasi error | ✅ |
| 5 | BB-BV-05 | kode_aset | Empty string | "" | Validasi error (required) | ✅ |
| 6 | BB-BV-06 | jumlah | Negative value | -1 | Validasi error | ✅ |
| 7 | BB-BV-07 | tahun_perolehan | Maximum valid | Tahun sekarang | Data tersimpan | ✅ |
| 8 | BB-BV-08 | tahun_perolehan | Above maximum | Tahun depan | Validasi error | ✅ |
| 9 | BB-BV-09 | nama_aset | Minimum length | 1 karakter | Data tersimpan | ✅ |
| 10 | BB-BV-10 | nama_aset | Maximum length | 255 karakter | Data tersimpan | ✅ |

---

## C. RINGKASAN TEST CASES

| Kategori | Jumlah Test Case |
|----------|------------------|
| **WHITE BOX TESTING** | |
| Create Positive | 5 |
| Create Negative | 5 |
| Read Positive | 5 |
| Update Positive | 5 |
| Update Negative | 5 |
| Delete Positive | 5 |
| Statistik & Dashboard Positive | 6 |
| Akun Positive | 3 |
| Akun Negative | 4 |
| Generate Label | 3 |
| Bulk Delete | 4 |
| Search | 6 |
| Image/Foto Barang | 4 |
| **Subtotal White Box** | **60** |
| **BLACK BOX TESTING** | |
| Boundary Value Analysis | 10 |
| **Subtotal Black Box** | **10** |
| **TOTAL** | **70** |

---

## D. CATATAN PENGUJIAN

**Status:**
- ✅ Pass
- ❌ Fail
- ⏳ Pending

**Fitur yang Diuji:**
- CRUD Barang (Tambah, Update, Hapus)
- Upload, Update, dan Hapus Gambar/Foto Barang
- Import Excel
- Bulk Delete
- Pencarian (kode_aset, kode_barang, nama_aset, penanggung_jawab)
- Pagination
- Statistik Dashboard (Total Aset, Total Unit, Total Aktivitas, Kondisi Baik)
- Manajemen Akun (Ganti Username & Password)
- Generate Label Aset

---

Catatan Tambahan:
Test case sekarang totalnya adalah 70, termasuk 4 test case baru untuk fitur
upload, update, dan hapus foto barang. Code coverage mencapai 89.7%.

*Dokumen ini dibuat untuk keperluan pengujian Sistem Inventaris Barang Diskominfo HSU*
