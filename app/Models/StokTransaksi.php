<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokTransaksi extends Model
{
     protected $fillable = [
        'bahan_baku_id',
        'jenis_transaksi',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'user_id',
        'tanggal_transaksi'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime'
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
