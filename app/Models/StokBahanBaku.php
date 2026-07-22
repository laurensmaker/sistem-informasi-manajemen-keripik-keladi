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
        'kode_bahan',
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


    // Method untuk tambah stok
    public function tambahStok($jumlah, $keterangan = null, $userId = null)
    {
        $this->jumlah_stok += $jumlah;
        $this->jumlah_masuk += $jumlah;
        $this->tanggal_update = now();
        $this->save();

        // Catat transaksi
        StokTransaksi::create([
            'bahan_baku_id' => $this->bahan_baku_id,
            'jenis_transaksi' => 'masuk',
            'jumlah' => $jumlah,
            'stok_sebelum' => $this->jumlah_stok - $jumlah,
            'stok_sesudah' => $this->jumlah_stok,
            'keterangan' => $keterangan,
            'user_id' => $userId ?? auth()->id(),
            'tanggal_transaksi' => now()
        ]);

        return $this;
    }

    // Method untuk kurangi stok
    public function kurangiStok($jumlah, $keterangan = null, $userId = null)
    {
        if ($this->jumlah_stok < $jumlah) {
            throw new \Exception("Stok tidak mencukupi! Stok tersedia: {$this->jumlah_stok}, Dibutuhkan: {$jumlah}");
        }

        $this->jumlah_stok -= $jumlah;
        $this->jumlah_keluar += $jumlah;
        $this->tanggal_update = now();
        $this->save();

        // Catat transaksi
        StokTransaksi::create([
            'bahan_baku_id' => $this->bahan_baku_id,
            'jenis_transaksi' => 'keluar',
            'jumlah' => $jumlah,
            'stok_sebelum' => $this->jumlah_stok + $jumlah,
            'stok_sesudah' => $this->jumlah_stok,
            'keterangan' => $keterangan,
            'user_id' => $userId ?? auth()->id(),
            'tanggal_transaksi' => now()
        ]);

        return $this;
    }

    // Cek ketersediaan stok
    public function cekStokTersedia($jumlah)
    {
        return $this->jumlah_stok >= $jumlah;
    }
}