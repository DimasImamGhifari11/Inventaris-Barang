<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * =====================================================
 * WHITE BOX TESTING - PENCARIAN (SEARCH)
 * =====================================================
 */
class SearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed data untuk testing search
        Barang::create([
            'kode_aset' => 'EGOV01',
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell XPS',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 5,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Ruang Server',
            'penanggung_jawab' => 'Ahmad Fauzi',
            'tahun_perolehan' => 2025,
        ]);

        Barang::create([
            'kode_aset' => 'EGOV02',
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 3,
            'kondisi' => 'Rusak Ringan',
            'lokasi_penyimpanan' => 'Ruang Kantor',
            'penanggung_jawab' => 'Budi Santoso',
            'tahun_perolehan' => 2024,
        ]);

        Barang::create([
            'kode_aset' => 'EGOV03',
            'kode_barang' => '2025.003/EGOV',
            'nama_aset' => 'Printer Canon',
            'jenis_aset' => 'Peralatan IT',
            'jumlah' => 2,
            'kondisi' => 'Baik',
            'lokasi_penyimpanan' => 'Gudang',
            'penanggung_jawab' => 'Ahmad Rizki',
            'tahun_perolehan' => 2023,
        ]);
    }

    /**
     * WB-SR-P01: Pencarian berdasarkan kode_aset
     */
    public function test_pencarian_berdasarkan_kode_aset(): void
    {
        $response = $this->authenticatedGetJson('/api/barang?search=EGOV01');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('EGOV01', $data[0]['kode_aset']);
    }

    /**
     * WB-SR-P02: Pencarian berdasarkan nama_aset
     */
    public function test_pencarian_berdasarkan_nama_aset(): void
    {
        $response = $this->authenticatedGetJson('/api/barang?search=Laptop');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Laptop Dell XPS', $data[0]['nama_aset']);
    }

    /**
     * WB-SR-P03: Pencarian berdasarkan penanggung_jawab
     */
    public function test_pencarian_berdasarkan_penanggung_jawab(): void
    {
        $response = $this->authenticatedGetJson('/api/barang?search=Ahmad');

        $response->assertStatus(200);

        $data = $response->json('data');
        // Harus menemukan 2 data (Ahmad Fauzi dan Ahmad Rizki)
        $this->assertCount(2, $data);
    }

    /**
     * WB-SR-P04: Pencarian berdasarkan kode_barang
     */
    public function test_pencarian_berdasarkan_kode_barang(): void
    {
        $response = $this->authenticatedGetJson('/api/barang?search=2025.002');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Monitor LG', $data[0]['nama_aset']);
    }

    /**
     * WB-SR-N01: Pencarian dengan keyword tidak ditemukan
     */
    public function test_pencarian_dengan_keyword_tidak_ditemukan(): void
    {
        $response = $this->authenticatedGetJson('/api/barang?search=TidakAda123');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(0, $data);
    }

    /**
     * WB-SR-P05: Pencarian riwayat berdasarkan nama_aset
     */
    public function test_pencarian_riwayat_berdasarkan_nama_aset(): void
    {
        // Buat riwayat
        Riwayat::create([
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell XPS',
            'perubahan' => 'Tambah Data',
            'stok_sebelum' => 0,
            'stok_sesudah' => 5,
        ]);

        Riwayat::create([
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'perubahan' => 'Tambah Data',
            'stok_sebelum' => 0,
            'stok_sesudah' => 3,
        ]);

        $response = $this->authenticatedGetJson('/api/riwayat?search=Laptop');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Laptop Dell XPS', $data[0]['nama_aset']);
    }
}
