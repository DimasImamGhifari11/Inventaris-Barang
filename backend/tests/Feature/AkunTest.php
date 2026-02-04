<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * =====================================================
 * WHITE BOX TESTING - AKUN (MANAJEMEN AKUN)
 * =====================================================
 */
class AkunTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat user dengan password yang diketahui
     */
    private function createAndAuthUser(string $password = 'password123'): User
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'name' => 'admin',
            'password' => Hash::make($password),
        ]);

        Sanctum::actingAs($user, ['*']);

        return $user;
    }

    /**
     * =====================================================
     * AKUN - POSITIVE TEST CASES
     * =====================================================
     */

    /**
     * WB-AK-P01: Ganti username dengan data valid
     */
    public function test_ganti_username_dengan_data_valid(): void
    {
        $user = $this->createAndAuthUser();

        $response = $this->putJson('/api/akun', [
            'username' => 'admin_baru',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Akun berhasil diperbarui',
                 ]);

        $user->refresh();
        $this->assertEquals('admin_baru', $user->email);
        $this->assertEquals('admin_baru', $user->name);
    }

    /**
     * WB-AK-P02: Ganti password dengan data valid
     */
    public function test_ganti_password_dengan_data_valid(): void
    {
        $user = $this->createAndAuthUser('password123');

        $response = $this->putJson('/api/akun', [
            'password_lama' => 'password123',
            'password_baru' => 'newpassword456',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Akun berhasil diperbarui',
                 ]);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }

    /**
     * WB-AK-P03: Verifikasi password baru tersimpan setelah ganti
     */
    public function test_password_baru_tersimpan_setelah_ganti(): void
    {
        $user = $this->createAndAuthUser('password123');

        // Ganti password
        $this->putJson('/api/akun', [
            'password_lama' => 'password123',
            'password_baru' => 'newpassword456',
        ]);

        // Verifikasi password lama tidak lagi valid
        $user->refresh();
        $this->assertFalse(Hash::check('password123', $user->password));

        // Verifikasi password baru valid
        $this->assertTrue(Hash::check('newpassword456', $user->password));
    }

    /**
     * =====================================================
     * AKUN - NEGATIVE TEST CASES
     * =====================================================
     */

    /**
     * WB-AK-N01: Ganti password dengan password lama salah
     */
    public function test_ganti_password_dengan_password_lama_salah(): void
    {
        $this->createAndAuthUser('password123');

        $response = $this->putJson('/api/akun', [
            'password_lama' => 'salah123',
            'password_baru' => 'newpassword456',
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Password lama salah',
                 ]);
    }

    /**
     * WB-AK-N02: Ganti username kurang dari 3 karakter
     */
    public function test_ganti_username_kurang_dari_3_karakter(): void
    {
        $this->createAndAuthUser();

        $response = $this->putJson('/api/akun', [
            'username' => 'ab',
        ]);

        $response->assertStatus(422);
    }

    /**
     * WB-AK-N03: Ganti password kurang dari 6 karakter
     */
    public function test_ganti_password_kurang_dari_6_karakter(): void
    {
        $this->createAndAuthUser('password123');

        $response = $this->putJson('/api/akun', [
            'password_lama' => 'password123',
            'password_baru' => 'abc',
        ]);

        $response->assertStatus(422);
    }

    /**
     * WB-AK-N04: Akses endpoint akun tanpa autentikasi
     */
    public function test_akses_akun_tanpa_autentikasi(): void
    {
        $response = $this->putJson('/api/akun', [
            'username' => 'admin_baru',
        ]);

        $response->assertStatus(401);
    }
}
