<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    use HasFactory;

    protected $table = 'komposisi';

    protected $fillable = [
        'jenis_keripik_id',
        'bahan_baku_id',
        'jumlah_dibutuhkan',
    ];

    protected $casts = [
        'jumlah_dibutuhkan' => 'decimal:2',
    ];

    // Relasi ke JenisKeripik
    public function jenisKeripik()
    {
        return $this->belongsTo(JenisKeripik::class, 'jenis_keripik_id');
    }

    // Relasi ke BahanBaku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    // Accessor untuk format jumlah
    public function getJumlahDibutuhkanFormattedAttribute()
    {
        return number_format($this->jumlah_dibutuhkan, 2, ',', '.');
    }

    // Accessor untuk total biaya (jumlah * harga satuan bahan)
    public function getTotalBiayaAttribute()
    {
        if ($this->bahanBaku) {
            return $this->jumlah_dibutuhkan * $this->bahanBaku->harga_satuan;
        }
        return 0;
    }

    public function getTotalBiayaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    // Scope untuk filter berdasarkan jenis keripik
    public function scopeByJenisKeripik($query, $jenisKeripikId)
    {
        return $query->where('jenis_keripik_id', $jenisKeripikId);
    }

    // Scope untuk filter berdasarkan bahan baku
    public function scopeByBahanBaku($query, $bahanBakuId)
    {
        return $query->where('bahan_baku_id', $bahanBakuId);
    }
}