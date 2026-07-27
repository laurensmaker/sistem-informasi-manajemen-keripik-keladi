<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komposisi extends Model
{
    public $table = 'komposisi';
      protected $fillable = [
        'jenis_keripik_id',
        'bahan_baku_id',
        'jumlah_dibutuhkan',
        'kode_produksi',
        'jumlah_produksi',
        'total_biaya',
        'tanggal_produksi',
        'user_id',
        'status_produksi'
    ];

    protected $casts = [
        'tanggal_produksi' => 'datetime'
    ];

    public function jenisKeripik()
    {
        return $this->belongsTo(JenisKeripik::class);
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Generate kode produksi otomatis
    public static function generateKodeProduksi()
    {
        $prefix = 'PRD';
        $date = date('Ymd');
        $last = self::whereDate('tanggal_produksi', today())
            ->whereNotNull('kode_produksi')
            ->count();
        $number = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $date . $number;
    }

    // Accessor
    public function getTanggalProduksiFormattedAttribute()
    {
        return $this->tanggal_produksi ? $this->tanggal_produksi->format('d/m/Y H:i') : '-';
    }
}