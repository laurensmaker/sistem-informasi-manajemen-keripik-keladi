<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'nama_pembeli',
        'no_hp_pembeli',
        'total_harga',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total_harga' => 'decimal:2',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Detail Penjualan
    public function details()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id');
    }

    // Hitung Harga Pokok Penjualan (HPP) per transaksi
    public function getHppAttribute()
    {
        $totalHpp = 0;
        foreach ($this->details as $detail) {
            // Ambil komposisi bahan baku untuk produk ini
            $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
            foreach ($komposisi as $kom) {
                $hargaBahan = $kom->bahanBaku->harga_satuan ?? 0;
                $totalHpp += $kom->jumlah_dibutuhkan * $hargaBahan * $detail->jumlah;
            }
        }
        return $totalHpp;
    }

    // Hitung Laba/Rugi per transaksi
    public function getLabaRugiAttribute()
    {
        return $this->total_harga - $this->hpp;
    }

    // Accessor untuk format HPP
    public function getHppFormattedAttribute()
    {
        return 'Rp ' . number_format($this->hpp, 0, ',', '.');
    }

    // Accessor untuk format Laba/Rugi
    public function getLabaRugiFormattedAttribute()
    {
        $laba = $this->laba_rugi;
        $prefix = $laba >= 0 ? '+' : '-';
        $color = $laba >= 0 ? 'success' : 'danger';
        return [
            'formatted' => $prefix . ' Rp ' . number_format(abs($laba), 0, ',', '.'),
            'color' => $color,
            'value' => $laba
        ];
    }

    // Scope untuk filter tanggal
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    // Scope untuk filter status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Generate nomor transaksi otomatis
    public static function generateNoTransaksi()
    {
        $prefix = 'TRX-';
        $date = date('Ymd');
        $last = self::whereDate('created_at', date('Y-m-d'))->count() + 1;
        return $prefix . $date . str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}