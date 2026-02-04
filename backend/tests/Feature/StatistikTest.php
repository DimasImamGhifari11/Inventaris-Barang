<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * =====================================================
 * WHITE BOX TESTING - STATISTIK & DASHBOARD
 * =====================================================
 */
class StatistikTest extends TestCase
{
    use RefreshDatabase;

    /**
     * WB-ST-P01: Menampilkan statistik Total Aset
     */
    public function test_menampilkan_statistik_total_aset(): void
    {
        Barang::factory()->count(5)->create();

        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'total_aset' => 5,
                     ],
                 ]);
    }

    /**
     * WB-ST-P02: Menampilkan statistik Total Unit Barang (sum jumlah)
     */
    public function test_menampilkan_statistik_total_unit_barang(): void
    {
        Barang::factory()->create(['jumlah' => 10]);
        Barang::factory()->create(['jumlah' => 5]);
        Barang::factory()->create(['jumlah' => 3]);

        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'total_unit' => 18,
                     ],
                 ]);
    }

    /**
     * WB-ST-P03: Menampilkan statistik Total Aktivitas
     */
    public function test_menampilkan_statistik_total_aktivitas(): void
    {
        // Tambah 3 barang → otomatis 3 record riwayat
        $this->authenticatedPostJson('/api/barang', [
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

        $this->authenticatedPostJson('/api/barang', [
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

        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.total_aktivitas'));
    }

    /**
     * WB-ST-P04: Menampilkan statistik Kondisi Baik
     */
    public function test_menampilkan_statistik_kondisi_baik(): void
    {
        Barang::factory()->count(3)->kondisiBaik()->create();
        Barang::factory()->count(2)->kondisiRusakRingan()->create();
        Barang::factory()->count(1)->kondisiRusakBerat()->create();

        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'kondisi' => [
                             'Baik' => 3,
                             'Rusak Ringan' => 2,
                             'Rusak Berat' => 1,
                         ],
                     ],
                 ]);
    }

    /**
     * WB-ST-P05: Donut chart - struktur data kondisi lengkap
     */
    public function test_statistik_struktur_data_kondisi_lengkap(): void
    {
        // Tanpa data, semua kondisi harus ada dengan nilai 0
        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_aset',
                         'total_unit',
                         'total_aktivitas',
                         'kondisi' => [
                             'Baik',
                             'Rusak Ringan',
                             'Rusak Berat',
                         ],
                         'recent_activity',
                     ],
                 ]);

        // Semua nilai 0 saat tidak ada data
        $this->assertEquals(0, $response->json('data.kondisi.Baik'));
        $this->assertEquals(0, $response->json('data.kondisi.Rusak Ringan'));
        $this->assertEquals(0, $response->json('data.kondisi.Rusak Berat'));
    }

    /**
     * WB-ST-P06: Aktivitas Terbaru menampilkan 5 log terakhir
     */
    public function test_aktivitas_terbaru_menampilkan_5_log_terakhir(): void
    {
        // Tambah 7 barang → 7 record riwayat
        for ($i = 1; $i <= 7; $i++) {
            $this->authenticatedPostJson('/api/barang', [
                'kode_aset' => 'EGOV' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'kode_barang' => "2025.{$i}/EGOV",
                'nama_aset' => "Aset Test {$i}",
                'jenis_aset' => 'Peralatan IT',
                'jumlah' => $i,
                'kondisi' => 'Baik',
                'lokasi_penyimpanan' => 'Ruang Server',
                'penanggung_jawab' => 'John Doe',
                'tahun_perolehan' => 2025,
            ]);
        }

        $response = $this->authenticatedGetJson('/api/statistik');

        $response->assertStatus(200);

        $recentActivity = $response->json('data.recent_activity');
        $this->assertCount(5, $recentActivity);
    }
}
