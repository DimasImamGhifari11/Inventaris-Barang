<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Riwayat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $search = $request->query('search');

        $query = Barang::orderBy('kode_aset', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_aset', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_aset', 'like', "%{$search}%");
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
            'kode_aset' => 'required|string|unique:barang,kode_aset',
            'kode_barang' => 'required|string',
            'nama_aset' => 'required|string',
            'jenis_aset' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi_penyimpanan' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $barang = Barang::create($request->all());

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
            'kode_aset' => 'required|string|unique:barang,kode_aset,' . $id,
            'kode_barang' => 'required|string',
            'nama_aset' => 'required|string',
            'jenis_aset' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi_penyimpanan' => 'required|string',
            'penanggung_jawab' => 'required|string',
            'tahun_perolehan' => 'required|integer|min:2000|max:' . date('Y'),
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
        $namaAsetLama = $barang->nama_aset;
        $kodeBarangLama = $barang->kode_barang;

        $barang->update($request->all());

        // Tentukan jenis perubahan
        $stokSesudah = $barang->jumlah;
        $selisihStok = $stokSesudah - $stokSebelum;

        if ($stokSebelum != $stokSesudah) {
            // Stok berubah
            $tanda = $selisihStok > 0 ? '+' : '';
            $perubahan = "Edit Stok ({$tanda}{$selisihStok})";
        } else {
            $perubahan = 'Edit Data';
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

        $count = 0;
        $errors = [];

        foreach ($request->data as $index => $row) {
            try {
                $barang = Barang::create([
                    'kode_aset' => $row['kode_aset'] ?? '',
                    'kode_barang' => $row['kode_barang'] ?? '',
                    'nama_aset' => $row['nama_aset'] ?? '',
                    'jenis_aset' => $row['jenis_aset'] ?? '',
                    'jumlah' => (int)($row['jumlah'] ?? 1),
                    'kondisi' => $row['kondisi'] ?? 'Baik',
                    'lokasi_penyimpanan' => $row['lokasi_penyimpanan'] ?? '',
                    'penanggung_jawab' => $row['penanggung_jawab'] ?? '',
                    'tahun_perolehan' => (int)($row['tahun_perolehan'] ?? date('Y')),
                ]);

                // Log riwayat import
                Riwayat::create([
                    'kode_barang' => $barang->kode_barang,
                    'nama_aset' => $barang->nama_aset,
                    'perubahan' => 'Tambah Data (Import)',
                    'stok_sebelum' => 0,
                    'stok_sesudah' => $barang->jumlah,
                    'keterangan' => 'Import dari Excel',
                ]);

                $count++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil import $count data",
            'count' => $count,
            'errors' => $errors
        ]);
    }
}
