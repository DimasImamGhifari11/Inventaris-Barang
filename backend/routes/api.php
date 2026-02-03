<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\RiwayatController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend API is working!',
        'status' => 'success',
        'timestamp' => now()->toDateTimeString()
    ]);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    // Hapus token lama (optional - untuk single session)
    $user->tokens()->delete();

    // Buat token baru menggunakan Sanctum
    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]
    ]);
});

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil'
    ]);
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'user' => $request->user()
    ]);
})->middleware('auth:sanctum');

// Protected Routes - Memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Statistik
    Route::get('/statistik', [BarangController::class, 'statistik']);

    // Barang Routes
    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::get('/barang/{id}', [BarangController::class, 'show']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
    Route::post('/barang/import', [BarangController::class, 'import']);
    Route::post('/barang/bulk-delete', [BarangController::class, 'bulkDestroy']);

    // Riwayat Routes
    Route::get('/riwayat', [RiwayatController::class, 'index']);
});
