<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokeKeripik extends Model
{
    use HasFactory;

    protected $table = 'stok_keripik';

    protected $fillable = [
        'jenis_keripik_id',
        'jumlah_stok',
        'jumlah_masuk',
        'jumlah_keluar',
        'kode_keripik',
        'tanggal_update',
        // 'user_id',
    ];

    protected $casts = [
        'tanggal_update' => 'datetime',
        'jumlah_stok' => 'integer',
        'jumlah_masuk' => 'integer',
        'jumlah_keluar' => 'integer',
    ];

    // Relasi ke JenisKeripik
   

    public function jenisKeripik()
    {
        return $this->belongsTo(JenisKeripik::class);
    }

    // Relasi ke User
   

    // Accessor untuk format tanggal
    public function getTanggalUpdateFormattedAttribute()
    {
        return $this->tanggal_update->format('d/m/Y H:i');
    }

    // Accessor untuk stok dengan format
    public function getJumlahStokFormattedAttribute()
    {
        return number_format($this->jumlah_stok, 0, ',', '.');
    }

    // Scope untuk filter berdasarkan jenis keripik
    public function scopeByJenisKeripik($query, $jenisKeripikId)
    {
        return $query->where('jenis_keripik_id', $jenisKeripikId);
    }

    // Scope untuk filter tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_update', [$startDate, $endDate]);
    }

     public function kurangiStok($jumlah, $keterangan = null)
    {
        // Validasi stok (integer)
        if ($this->jumlah_stok < $jumlah) {
            throw new \Exception("Stok tidak mencukupi! Stok tersedia: {$this->jumlah_stok}");
        }

        // Catat stok sebelum
        $stokSebelum = $this->jumlah_stok;
        
        // Kurangi stok (integer)
        $this->jumlah_stok -= $jumlah;
        $this->jumlah_keluar += $jumlah;
        $this->tanggal_update = now();
        $this->save();

        return $this;
    }

    public static function generateKode($jenisKeripikId, $berat)
    {
        $prefix = 'KRK';
        $idPad = str_pad($jenisKeripikId, 3, '0', STR_PAD_LEFT);
        $beratPad = str_pad($berat, 3, '0', STR_PAD_LEFT);
        return $prefix . $idPad . $beratPad;
    }
}