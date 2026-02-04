<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * =====================================================
 * WHITE BOX TESTING - BULK DELETE
 * =====================================================
 */
class BulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * WB-BD-P01: Bulk delete beberapa data sekaligus
     */
    public function test_bulk_delete_beberapa_data(): void
    {
        $barang1 = Barang::factory()->create();
        $barang2 = Barang::factory()->create();
        $barang3 = Barang::factory()->create();

        $response = $this->authenticatedPostJson('/api/barang/bulk-delete', [
            'ids' => [$barang1->id, $barang2->id],
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'count' => 2,
                 ]);

        $this->assertDatabaseMissing('barang', ['id' => $barang1->id]);
        $this->assertDatabaseMissing('barang', ['id' => $barang2->id]);
        // barang3 tetap ada
        $this->assertDatabaseHas('barang', ['id' => $barang3->id]);
    }

    /**
     * WB-BD-P02: Bulk delete tercatat di riwayat
     */
    public function test_bulk_delete_tercatat_di_riwayat(): void
    {
        $barang1 = Barang::factory()->create([
            'kode_barang' => '2025.001/EGOV',
            'nama_aset' => 'Laptop Dell',
            'jumlah' => 5,
        ]);
        $barang2 = Barang::factory()->create([
            'kode_barang' => '2025.002/EGOV',
            'nama_aset' => 'Monitor LG',
            'jumlah' => 3,
        ]);

        $this->authenticatedPostJson('/api/barang/bulk-delete', [
            'ids' => [$barang1->id, $barang2->id],
        ]);

        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.001/EGOV',
            'perubahan' => 'Hapus Data (Bulk)',
            'stok_sebelum' => 5,
            'stok_sesudah' => 0,
        ]);

        $this->assertDatabaseHas('riwayat', [
            'kode_barang' => '2025.002/EGOV',
            'perubahan' => 'Hapus Data (Bulk)',
            'stok_sebelum' => 3,
            'stok_sesudah' => 0,
        ]);
    }

    /**
     * WB-BD-N01: Bulk delete dengan ids kosong
     */
    public function test_bulk_delete_dengan_ids_kosong(): void
    {
        $response = $this->authenticatedPostJson('/api/barang/bulk-delete', [
            'ids' => [],
        ]);

        $response->assertStatus(422);
    }

    /**
     * WB-BD-N02: Bulk delete dengan id tidak valid
     */
    public function test_bulk_delete_dengan_id_tidak_valid(): void
    {
        $response = $this->authenticatedPostJson('/api/barang/bulk-delete', [
            'ids' => [9999, 8888],
        ]);

        $response->assertStatus(422);
    }
}
