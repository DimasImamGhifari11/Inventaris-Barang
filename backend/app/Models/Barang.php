<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kode_aset',
        'kode_barang',
        'nama_aset',
        'jenis_aset',
        'jumlah',
        'kondisi',
        'lokasi_penyimpanan',
        'penanggung_jawab',
        'tahun_perolehan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tahun_perolehan' => 'integer',
    ];
}
