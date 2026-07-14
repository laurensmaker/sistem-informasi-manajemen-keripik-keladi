<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBahanBaku extends Model
{
    use HasFactory;

    protected $table = 'stok_bahan_baku';

    protected $fillable = [
        'bahan_baku_id',
        'jumlah_stok',
        'jumlah_masuk',
        'jumlah_keluar',
        'tanggal_update',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'jumlah_stok' => 'decimal:2',
        'jumlah_masuk' => 'decimal:2',
        'jumlah_keluar' => 'decimal:2',
    ];

    // Relasi ke BahanBaku
    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_baku_id');
    }

    // Accessor untuk format tanggal
    public function getTanggalUpdateFormattedAttribute()
    {
        return $this->tanggal_update->format('d/m/Y H:i');
    }

    // Accessor untuk stok dengan format
    public function getJumlahStokFormattedAttribute()
    {
        return number_format($this->jumlah_stok, 2, ',', '.');
    }

    public function getJumlahMasukFormattedAttribute()
    {
        return number_format($this->jumlah_masuk, 2, ',', '.');
    }

    public function getJumlahKeluarFormattedAttribute()
    {
        return number_format($this->jumlah_keluar, 2, ',', '.');
    }

    // Scope untuk filter berdasarkan bahan baku
    public function scopeByBahanBaku($query, $bahanBakuId)
    {
        return $query->where('bahan_baku_id', $bahanBakuId);
    }

    // Scope untuk filter tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_update', [$startDate, $endDate]);
    }

    // Scope untuk stok kritis (kurang dari minimum)
    public function scopeStokKritis($query, $minimum = 10)
    {
        return $query->where('jumlah_stok', '<', $minimum);
    }
}