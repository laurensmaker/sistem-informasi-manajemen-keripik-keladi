<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';

    protected $fillable = [
        'nama_bahan',
        'satuan',
        'harga_satuan',
        'supplier',
    ];

    // Accessor untuk format harga
    public function getHargaSatuanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    // Mutator untuk menyimpan harga tanpa format
    public function setHargaSatuanAttribute($value)
    {
        $this->attributes['harga_satuan'] = str_replace(',', '', $value);
    }
}