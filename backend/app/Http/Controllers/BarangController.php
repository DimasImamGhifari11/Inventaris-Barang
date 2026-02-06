<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function statistik()
    {
        $total = Barang::count();
        $totalUnit = Barang::sum('jumlah');
        $totalAktivitas = Riwayat::count();
        $kondisi = [
            'Baik' => Barang::where('kondisi', 'Baik')->count(),
            'Rusak Ringan' => Barang::where('kondisi', 'Rusak Ringan')->count(),
            'Rusak Berat' => Barang::where('kondisi', 'Rusak Berat')->count(),
        ];

        $recentActivity = Riwayat::orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_aset' => $total,
                'total_unit' => (int) $totalUnit,
                'total_aktivitas' => $totalAktivitas,
                'kondisi' => $kondisi,
                'recent_activity' => $recentActivity,
            ]
        ]);
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');

        $query = Barang::orderBy('kode_aset', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_aset', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_aset', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        $barang = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $barang->items(),
            'pagination' => [
                'current_page' => $barang->currentPage(),
                'last_page' => $barang->lastPage(),
                'per_page' => $barang->perPage(),
                'total' => $barang->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_aset' => 'required|string',
            'kode_barang' => 'required|string',
            'nama_aset' => 'required|string',
            'jenis_aset' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi_penyimpanan' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . date('Y'),
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('gambar');

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('gambar_barang', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $barang = Barang::create($data);

        // Log riwayat tambah data
        Riwayat::create([
            'kode_barang' => $barang->kode_barang,
            'nama_aset' => $barang->nama_aset,
            'perubahan' => 'Tambah Data',
            'stok_sebelum' => 0,
            'stok_sesudah' => $barang->jumlah,
            'keterangan' => $barang->keterangan ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            'data' => $barang
        ], 201);
    }

    public function show(string $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function update(Request $request, string $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode_aset' => 'required|string',
            'kode_barang' => 'required|string',
            'nama_aset' => 'required|string',
            'jenis_aset' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi_penyimpanan' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . date('Y'),
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan data sebelum update untuk riwayat
        $stokSebelum = $barang->jumlah;
        $gambarSebelum = $barang->gambar;

        $data = $request->except(['gambar', 'hapus_gambar']);

        // Handle hapus gambar
        if ($request->boolean('hapus_gambar')) {
            if ($gambarSebelum && Storage::disk('public')->exists('gambar_barang/' . $gambarSebelum)) {
                Storage::disk('public')->delete('gambar_barang/' . $gambarSebelum);
            }
            $data['gambar'] = null;
        }

        // Handle upload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($gambarSebelum && Storage::disk('public')->exists('gambar_barang/' . $gambarSebelum)) {
                Storage::disk('public')->delete('gambar_barang/' . $gambarSebelum);
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('gambar_barang', $filename, 'public');
            $data['gambar'] = $filename;
        }

        $barang->update($data);

        // Tentukan jenis perubahan
        $stokSesudah = $barang->jumlah;
        $selisihStok = $stokSesudah - $stokSebelum;
        $perubahanList = [];

        if ($stokSebelum != $stokSesudah) {
            $tanda = $selisihStok > 0 ? '+' : '';
            $perubahanList[] = "Edit Stok ({$tanda}{$selisihStok})";
        }

        // Cek perubahan gambar
        if (!$gambarSebelum && $barang->gambar) {
            $perubahanList[] = 'Tambah Gambar';
        } elseif ($gambarSebelum && !$barang->gambar) {
            $perubahanList[] = 'Hapus Gambar';
        } elseif ($gambarSebelum && $barang->gambar && $gambarSebelum !== $barang->gambar) {
            $perubahanList[] = 'Ubah Gambar';
        }

        if (empty($perubahanList)) {
            $perubahan = 'Edit Data';
        } else {
            $perubahan = implode(', ', $perubahanList);
        }

        // Log riwayat update
        Riwayat::create([
            'kode_barang' => $barang->kode_barang,
            'nama_aset' => $barang->nama_aset,
            'perubahan' => $perubahan,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'keterangan' => $request->keterangan ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate',
            'data' => $barang
        ]);
    }

    public function destroy(string $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Hapus gambar jika ada
        if ($barang->gambar && Storage::disk('public')->exists('gambar_barang/' . $barang->gambar)) {
            Storage::disk('public')->delete('gambar_barang/' . $barang->gambar);
        }

        // Log riwayat hapus data sebelum dihapus
        Riwayat::create([
            'kode_barang' => $barang->kode_barang,
            'nama_aset' => $barang->nama_aset,
            'perubahan' => 'Hapus Data',
            'stok_sebelum' => $barang->jumlah,
            'stok_sesudah' => 0,
            'keterangan' => null,
        ]);

        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data' => 'required|array',
            'data.*.kode_aset' => 'required|string',
            'data.*.kode_barang' => 'required|string',
            'data.*.nama_aset' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $created = 0;
        $errors = [];

        foreach ($request->data as $index => $row) {
            try {
                $kodeAset = $row['kode_aset'] ?? '-';
                $newData = [
                    'kode_aset' => $kodeAset,
                    'kode_barang' => $row['kode_barang'] ?? '',
                    'nama_aset' => $row['nama_aset'] ?? '',
                    'jenis_aset' => $row['jenis_aset'] ?? '',
                    'jumlah' => (int)($row['jumlah'] ?? 1),
                    'kondisi' => $row['kondisi'] ?? 'Baik',
                    'lokasi_penyimpanan' => $row['lokasi_penyimpanan'] ?? '',
                    'penanggung_jawab' => $row['penanggung_jawab'] ?? '',
                    'tahun_perolehan' => (int)($row['tahun_perolehan'] ?? date('Y')),
                ];

                // Selalu buat data baru (kode_aset tidak lagi unique, id yang membedakan)
                $barang = Barang::create($newData);

                Riwayat::create([
                    'kode_barang' => $barang->kode_barang,
                    'nama_aset' => $barang->nama_aset,
                    'perubahan' => 'Tambah Data (Import)',
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $barang->jumlah,
                    'keterangan' => 'Import dari Excel',
                ]);

                $created++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $total = $created;
        $message = "Berhasil import: {$created} data baru";

        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $total,
            'created' => $created,
            'errors' => $errors,
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:barang,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $ids = $request->ids;
        $deletedCount = 0;

        foreach ($ids as $id) {
            $barang = Barang::find($id);
            if ($barang) {
                // Hapus gambar jika ada
                if ($barang->gambar && Storage::disk('public')->exists('gambar_barang/' . $barang->gambar)) {
                    Storage::disk('public')->delete('gambar_barang/' . $barang->gambar);
                }

                // Log riwayat hapus data
                Riwayat::create([
                    'kode_barang' => $barang->kode_barang,
                    'nama_aset' => $barang->nama_aset,
                    'perubahan' => 'Hapus Data (Bulk)',
                    'stok_sebelum' => $barang->jumlah,
                    'stok_sesudah' => 0,
                    'keterangan' => 'Hapus massal',
                ]);

                $barang->delete();
                $deletedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil menghapus $deletedCount data",
            'count' => $deletedCount
        ]);
    }
}
