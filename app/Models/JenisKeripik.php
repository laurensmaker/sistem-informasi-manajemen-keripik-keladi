<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class JenisKeripik extends Model
{
    use HasFactory;

    protected $table = 'jenis_keripik';

    protected $fillable = [
        'nama_jenis',
        'deskripsi',
        'harga_jual',
        'satuan',
        'gambar',
    ];

    // Relasi ke StokKeripik (HASIL)
    public function stokKeripik()
    {
        return $this->hasMany(StokeKeripik::class, 'jenis_keripik_id');
    }

    // Relasi ke Komposisi
    public function komposisi()
    {
        return $this->hasMany(Komposisi::class, 'jenis_keripik_id');
    }

    // Relasi ke Detail Penjualan
    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'jenis_keripik_id');
    }

    // Accessor untuk mendapatkan URL gambar
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/' . $this->gambar);
        }
        return asset('images/default-product.png');
    }

    // Accessor untuk format harga
    public function getHargaJualFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_jual, 0, ',', '.');
    }

    // Accessor untuk stok terakhir
    public function getStokTerakhirAttribute()
    {
        $stok = $this->stokKeripik()->latest('tanggal_update')->first();
        return $stok ? $stok->jumlah_stok : 0;
    }
}