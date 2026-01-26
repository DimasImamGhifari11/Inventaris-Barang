<?php

namespace Tests\Feature;

use App\Models\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * =====================================================
 * BLACK BOX TESTING - BOUNDARY VALUE ANALYSIS
 * =====================================================
 *
 * Pengujian nilai batas untuk memastikan validasi
 * bekerja dengan benar pada batas-batas input.
 */
class BoundaryValueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data dasar untuk testing
     */
    private function getBaseData(): array
    {
        return [
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
    }

    /**
     * BB-BV-01: Jumlah dengan nilai minimum valid (1)
     *
     * Boundary: jumlah minimum = 1
     * Expected: Data berhasil tersimpan
     */
    public function test_jumlah_dengan_nilai_minimum_valid(): void
    {
        $data = $this->getBaseData();
        $data['jumlah'] = 1; // Nilai minimum valid

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil disimpan',
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'jumlah' => 1,
        ]);
    }

    /**
     * BB-BV-02: Jumlah dengan nilai di bawah minimum (0)
     *
     * Boundary: jumlah < 1
     * Expected: Validasi error
     */
    public function test_jumlah_dengan_nilai_dibawah_minimum(): void
    {
        $data = $this->getBaseData();
        $data['jumlah'] = 0; // Di bawah minimum

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['jumlah']);
    }

    /**
     * BB-BV-03: Tahun perolehan dengan nilai minimum valid (2000)
     *
     * Boundary: tahun_perolehan minimum = 2000
     * Expected: Data berhasil tersimpan
     */
    public function test_tahun_perolehan_dengan_nilai_minimum_valid(): void
    {
        $data = $this->getBaseData();
        $data['tahun_perolehan'] = 2000; // Nilai minimum valid

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil disimpan',
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'tahun_perolehan' => 2000,
        ]);
    }

    /**
     * BB-BV-04: Tahun perolehan dengan nilai di bawah minimum (1999)
     *
     * Boundary: tahun_perolehan < 2000
     * Expected: Validasi error
     */
    public function test_tahun_perolehan_dengan_nilai_dibawah_minimum(): void
    {
        $data = $this->getBaseData();
        $data['tahun_perolehan'] = 1999; // Di bawah minimum

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['tahun_perolehan']);
    }

    /**
     * BB-BV-05: Kode aset dengan nilai kosong (empty string)
     *
     * Boundary: kode_aset = "" (empty)
     * Expected: Validasi error (required)
     */
    public function test_kode_aset_dengan_nilai_kosong(): void
    {
        $data = $this->getBaseData();
        $data['kode_aset'] = ''; // Empty string

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['kode_aset']);
    }

    /**
     * BB-BV-06: Jumlah dengan nilai negatif (-1)
     *
     * Boundary: jumlah < 0
     * Expected: Validasi error
     */
    public function test_jumlah_dengan_nilai_negatif(): void
    {
        $data = $this->getBaseData();
        $data['jumlah'] = -1; // Nilai negatif

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['jumlah']);
    }

    /**
     * BB-BV-07: Tahun perolehan dengan nilai maksimum valid (tahun sekarang)
     *
     * Boundary: tahun_perolehan = tahun sekarang
     * Expected: Data berhasil tersimpan
     */
    public function test_tahun_perolehan_dengan_nilai_maksimum_valid(): void
    {
        $data = $this->getBaseData();
        $data['tahun_perolehan'] = (int) date('Y'); // Tahun sekarang

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Data berhasil disimpan',
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'tahun_perolehan' => (int) date('Y'),
        ]);
    }

    /**
     * BB-BV-08: Tahun perolehan dengan nilai di atas maksimum (tahun depan)
     *
     * Boundary: tahun_perolehan > tahun sekarang
     * Expected: Validasi error
     */
    public function test_tahun_perolehan_dengan_nilai_diatas_maksimum(): void
    {
        $data = $this->getBaseData();
        $data['tahun_perolehan'] = (int) date('Y') + 1; // Tahun depan

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                 ])
                 ->assertJsonValidationErrors(['tahun_perolehan']);
    }

    /**
     * BB-BV-09: Nama aset dengan panjang minimum (1 karakter)
     *
     * Boundary: nama_aset length = 1
     * Expected: Data berhasil tersimpan
     */
    public function test_nama_aset_dengan_panjang_minimum(): void
    {
        $data = $this->getBaseData();
        $data['nama_aset'] = 'A'; // 1 karakter

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
            'nama_aset' => 'A',
        ]);
    }

    /**
     * BB-BV-10: Nama aset dengan panjang maksimum (255 karakter)
     *
     * Boundary: nama_aset length = 255 (max varchar)
     * Expected: Data berhasil tersimpan
     */
    public function test_nama_aset_dengan_panjang_maksimum(): void
    {
        $data = $this->getBaseData();
        $data['nama_aset'] = str_repeat('A', 255); // 255 karakter

        $response = $this->postJson('/api/barang', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                 ]);

        $this->assertDatabaseHas('barang', [
            'kode_aset' => 'EGOV01',
        ]);
    }
}
