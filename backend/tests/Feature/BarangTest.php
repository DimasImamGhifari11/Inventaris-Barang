<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarangTest extends TestCase
{
    use RefreshDatabase;

    /**
     * =====================================================
     * WHITE BOX TESTING - CREATE POSITIF
     * =====================================================
     */

    /**
     * WB-CR-P01: Tambah data dengan semua field valid
     */
    public function test_tambah_data_dengan_semua_field_valid(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil disimpan',
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'nama_aset' => 'Laptop Dell',
        ]);
    }

    /**
     * WB-CR-P02: Import data dari Excel
     */
    public function test_import_data_dari_excel(): void
    {
        $data = [
            'data' => [
                [
                    'kode_aset' => 'EGOV01',
                    'kode_barang' => '2025.001/EGOV',
                    'nama_aset' => 'Laptop Dell',
                    'jenis_aset' => 'Peralatan IT',
                    'jumlah' => 5,
                    'kondisi' => 'Baik',
                    'lokasi_penyimpanan' => 'Ruang Server',
                    'penanggung_jawab' => 'John Doe',
                    'tahun_perolehan' => 2025,
                ],
                [
                    'kode_aset' => 'EGOV02',
                    'kode_barang' => '2025.002/EGOV',
                    'nama_aset' => 'Monitor LG',
                    'jenis_aset' => 'Peralatan IT',
                    'jumlah' => 3,
                    'kondisi' => 'Baik',
                    'lokasi_penyimpanan' => 'Ruang Server',
                    'penanggung_jawab' => 'Jane Doe',
                    'tahun_perolehan' => 2025,
                ],
            ]
        ];

        $response = $this->authenticatedPostJson('/api/barang/import', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'count' => 2,
                 ]);

        $this->assertDatabaseCount('barang', 2);
    }

    /**
     * WB-CR-P03: Tambah data tercatat di Riwayat
     */
    public function test_tambah_data_tercatat_di_riwayat(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $this->authenticatedPostJson('/api/barang', $data);

        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'perubahan' => 'Tambah Data',
            'stok_sebelum' => 0,
            'stok_sesudah' => 5,
        ]);
    }

    /**
     * WB-CR-P04: Tambah data dengan kondisi Rusak Ringan
     */
    public function test_tambah_data_dengan_kondisi_rusak_ringan(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Printer Canon',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 2,
            'kondisi' => 'Rusak Ringan',
            'lokasi_penyimpanan' => 'Gudang',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2023,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'kondisi' => 'Rusak Ringan',
        ]);
    }

    /**
     * WB-CR-P05: Tambah data dengan kondisi Rusak Berat
     */
    public function test_tambah_data_dengan_kondisi_rusak_berat(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'AC Daikin',
            'jenis_aset' => 'Elektronik',
            'jumlah' => 1,
            'kondisi' => 'Rusak Berat',
            'lokasi_penyimpanan' => 'Gudang',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2020,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'kondisi' => 'Rusak Berat',
        ]);
    }

    /**
     * =====================================================
     * WHITE BOX TESTING - CREATE NEGATIF
     * =====================================================
     */

    /**
     * WB-CR-N01: Tambah data dengan kode_aset duplikat
     */
    public function test_tambah_data_dengan_kode_aset_duplikat(): void
    {
        // Buat data pertama
        Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        // Coba buat dengan kode_aset yang sama
        $data = [
            'kode_aset' => 'EGOV01', // Duplikat
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-CR-N02: Tambah data dengan field kosong
     */
    public function test_tambah_data_dengan_field_kosong(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => '', // Field kosong
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-CR-N03: Tambah data dengan jumlah 0
     */
    public function test_tambah_data_dengan_jumlah_nol(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 0, // Tidak valid: minimal 1
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-CR-N04: Tambah data dengan tahun tidak valid
     */
    public function test_tambah_data_dengan_tahun_tidak_valid(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 1999, // Tidak valid: minimal 2000
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-CR-N05: Tambah data dengan kondisi tidak valid
     */
    public function test_tambah_data_dengan_kondisi_tidak_valid(): void
    {
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Sangat Baik', // Kondisi tidak valid
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPostJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * =====================================================
     * WHITE BOX TESTING - READ POSITIF
     * =====================================================
     */

    /**
     * WB-RD-P01: Menampilkan semua data
     */
    public function test_menampilkan_semua_data(): void
    {
        // Buat data test
        Barang::factory()->count(5)->create();

        $response = $this->authenticatedGetJson('/api/barang');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'pagination' => [
                         'current_page',
                         'last_page',
                         'per_page',
                         'total',
                     ],
                 ]);
    }

    /**
     * WB-RD-P02: Pencarian data dengan keyword valid
     */
    public function test_pencarian_data_dengan_keyword_valid(): void
    {
        Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell XPS',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        Barang::create([
            'kode_aset' => 'EGOV02',
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ]);

        $response = $this->authenticatedGetJson('/api/barang?search=Laptop');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Laptop Dell XPS', $data[0]['nama_aset']);
    }

    /**
     * WB-RD-P03: Navigasi pagination
     */
    public function test_navigasi_pagination(): void
    {
        // Buat 15 item (lebih dari default per_page 10)
        Barang::factory()->count(15)->create();

        // Test halaman 1
        $response1 = $this->authenticatedGetJson('/api/barang?page=1&per_page=10');
        $response1->assertStatus(200);
        $this->assertCount(10, $response1->json('data'));
        $this->assertEquals(1, $response1->json('pagination.current_page'));

        // Test halaman 2
        $response2 = $this->authenticatedGetJson('/api/barang?page=2&per_page=10');
        $response2->assertStatus(200);
        $this->assertCount(5, $response2->json('data'));
        $this->assertEquals(2, $response2->json('pagination.current_page'));
    }

    /**
     * WB-RD-P04: Menampilkan data Riwayat
     */
    public function test_menampilkan_data_riwayat(): void
    {
        // Buat riwayat dengan menambah barang
        $data = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $this->authenticatedPostJson('/api/barang', $data);

        $response = $this->authenticatedGetJson('/api/riwayat');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data',
                     'pagination',
                 ]);

        $this->assertNotEmpty($response->json('data'));
    }

    /**
     * WB-RD-P05: Menampilkan detail barang
     */
    public function test_menampilkan_detail_barang(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $response = $this->authenticatedGetJson("/api/barang/{$barang->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'kode_aset' => 'EGOV01',
                         'nama_aset' => 'Laptop Dell',
                     ],
                 ]);
    }

    /**
     * =====================================================
     * WHITE BOX TESTING - UPDATE POSITIF
     * =====================================================
     */

    /**
     * WB-UP-P01: Update data dengan data valid
     */
    public function test_update_data_dengan_data_valid(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Asus ROG', // Diubah
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil diupdate',
                 ]);

        $this->assertDatabaseHas('barang', [
            'id' => $barang->id,
            'nama_aset' => 'Laptop Asus ROG',
        ]);
    }

    /**
     * WB-UP-P02: Update jumlah stok bertambah
     */
    public function test_update_jumlah_stok_bertambah(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 10, // Bertambah dari 5 ke 10
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(200);

        // Cek riwayat mencatat "Edit Stok (+5)"
        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'perubahan' => 'Edit Stok (+5)',
            'stok_sebelum' => 5,
            'stok_sesudah' => 10,
        ]);
    }

    /**
     * WB-UP-P03: Update jumlah stok berkurang
     */
    public function test_update_jumlah_stok_berkurang(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 10,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 7, // Berkurang dari 10 ke 7
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(200);

        // Cek riwayat mencatat "Edit Stok (-3)"
        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'perubahan' => 'Edit Stok (-3)',
            'stok_sebelum' => 10,
            'stok_sesudah' => 7,
        ]);
    }

    /**
     * WB-UP-P04: Update dengan keterangan
     */
    public function test_update_dengan_keterangan(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell Updated',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
            'keterangan' => 'Perbaikan unit',
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(200);

        // Cek keterangan tercatat di riwayat
        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'keterangan' => 'Perbaikan unit',
        ]);
    }

    /**
     * WB-UP-P05: Update kondisi barang
     */
    public function test_update_kondisi_barang(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Rusak Ringan', // Kondisi diubah
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('barang', [
            'id' => $barang->id,
            'kondisi' => 'Rusak Ringan',
        ]);

        // Cek riwayat mencatat "Edit Data"
        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'perubahan' => 'Edit Data',
        ]);
    }

    /**
     * =====================================================
     * WHITE BOX TESTING - UPDATE NEGATIF
     * =====================================================
     */

    /**
     * WB-UP-N01: Update dengan kode_aset duplikat
     */
    public function test_update_dengan_kode_aset_duplikat(): void
    {
        $barang1 = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $barang2 = Barang::create([
            'kode_aset' => 'EGOV02',
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ]);

        // Coba update barang2 dengan kode_aset milik barang1
        $updateData = [
            'kode_aset' => 'EGOV01', // Duplikat - milik barang1
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang2->id}", $updateData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-UP-N02: Update dengan jumlah 0
     */
    public function test_update_dengan_jumlah_nol(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 0, // Tidak valid
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-UP-N03: Update dengan field kosong
     */
    public function test_update_dengan_field_kosong(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => '', // Field kosong
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * WB-UP-N04: Update data yang tidak ada
     */
    public function test_update_data_yang_tidak_ada(): void
    {
        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ];

        $response = $this->authenticatedPutJson('/api/barang/9999', $updateData);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Data tidak ditemukan',
                 ]);
    }

    /**
     * WB-UP-N05: Update dengan tahun tidak valid
     */
    public function test_update_dengan_tahun_tidak_valid(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $updateData = [
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 1990, // Tidak valid
        ];

        $response = $this->authenticatedPutJson("/api/barang/{$barang->id}", $updateData);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    /**
     * =====================================================
     * WHITE BOX TESTING - DELETE POSITIF
     * =====================================================
     */

    /**
     * WB-DL-P01: Hapus single data
     */
    public function test_hapus_single_data(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $response = $this->authenticatedDeleteJson("/api/barang/{$barang->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil dihapus',
                 ]);

        $this->assertDatabaseMissing('barang', [
            'id' => $barang->id,
        ]);
    }

    /**
     * WB-DL-P02: Hapus multiple data
     */
    public function test_hapus_multiple_data(): void
    {
        $barang1 = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $barang2 = Barang::create([
            'kode_aset' => 'EGOV02',
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ]);

        // Hapus keduanya
        $this->authenticatedDeleteJson("/api/barang/{$barang1->id}");
        $this->authenticatedDeleteJson("/api/barang/{$barang2->id}");

        $this->assertDatabaseMissing('barang', ['id' => $barang1->id]);
        $this->assertDatabaseMissing('barang', ['id' => $barang2->id]);
    }

    /**
     * WB-DL-P03: Hapus data yang tidak ada
     */
    public function test_hapus_data_yang_tidak_ada(): void
    {
        $response = $this->authenticatedDeleteJson('/api/barang/9999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Data tidak ditemukan',
                 ]);
    }

    /**
     * WB-DL-P04: Hapus data tercatat di Riwayat
     */
    public function test_hapus_data_tercatat_di_riwayat(): void
    {
        $barang = Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $this->authenticatedDeleteJson("/api/barang/{$barang->id}");

        // Cek riwayat mencatat "Hapus Data"
        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'perubahan' => 'Hapus Data',
            'stok_sebelum' => 5,
            'stok_sesudah' => 0,
        ]);
    }

    /**
     * WB-DL-P05: Verifikasi jumlah data setelah hapus
     */
    public function test_verifikasi_jumlah_data_setelah_hapus(): void
    {
        Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'John Doe',
            'tahun_perolehan' => 2025,
        ]);

        $barang2 = Barang::create([
            'kode_aset' => 'EGOV02',
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Jane Doe',
            'tahun_perolehan' => 2025,
        ]);

        $this->assertDatabaseCount('barang', 2);

        $this->authenticatedDeleteJson("/api/barang/{$barang2->id}");

        $this->assertDatabaseCount('barang', 1);
    }
}




