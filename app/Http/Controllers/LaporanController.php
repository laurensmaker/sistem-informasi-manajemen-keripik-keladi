<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\JenisKeripik;
use App\Models\Komposisi;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Laporan Laba Rugi
     */
    public function labaRugi(Request $request)
    {
        $query = Penjualan::with(['details.jenisKeripik', 'user'])
            ->where('status', 'selesai'); // Hanya transaksi selesai

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $penjualan = $query->latest('tanggal')->get();
        
        // Statistik
        $totalPendapatan = $penjualan->sum('total_harga');
        $totalHpp = $penjualan->sum(function($item) {
            return $item->hpp;
        });
        $totalLaba = $totalPendapatan - $totalHpp;
        $totalTransaksi = $penjualan->count();

        // Data per produk
        $produkTerjual = [];
        foreach ($penjualan as $p) {
            foreach ($p->details as $detail) {
                $namaProduk = $detail->jenisKeripik->nama_jenis ?? 'Unknown';
                if (!isset($produkTerjual[$namaProduk])) {
                    $produkTerjual[$namaProduk] = [
                        'nama' => $namaProduk,
                        'qty' => 0,
                        'total_penjualan' => 0,
                        'hpp' => 0,
                        'laba' => 0,
                    ];
                }
                $produkTerjual[$namaProduk]['qty'] += $detail->jumlah;
                $produkTerjual[$namaProduk]['total_penjualan'] += $detail->subtotal;
                
                // Hitung HPP per produk
                $hppProduk = 0;
                $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                foreach ($komposisi as $kom) {
                    $hargaBahan = $kom->bahanBaku->harga_satuan ?? 0;
                    $hppProduk += $kom->jumlah_dibutuhkan * $hargaBahan;
                }
                $produkTerjual[$namaProduk]['hpp'] += $hppProduk * $detail->jumlah;
                $produkTerjual[$namaProduk]['laba'] += $detail->subtotal - ($hppProduk * $detail->jumlah);
            }
        }

        // Data per bahan baku
        $bahanTerpakai = [];
        foreach ($penjualan as $p) {
            foreach ($p->details as $detail) {
                $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                foreach ($komposisi as $kom) {
                    $namaBahan = $kom->bahanBaku->nama_bahan ?? 'Unknown';
                    if (!isset($bahanTerpakai[$namaBahan])) {
                        $bahanTerpakai[$namaBahan] = [
                            'nama' => $namaBahan,
                            'total_terpakai' => 0,
                            'total_biaya' => 0,
                            'satuan' => $kom->bahanBaku->satuan ?? '-',
                        ];
                    }
                    $jumlahTerpakai = $kom->jumlah_dibutuhkan * $detail->jumlah;
                    $bahanTerpakai[$namaBahan]['total_terpakai'] += $jumlahTerpakai;
                    $bahanTerpakai[$namaBahan]['total_biaya'] += $jumlahTerpakai * ($kom->bahanBaku->harga_satuan ?? 0);
                }
            }
        }

        // Data per jenis keripik (ringkasan)
        $ringkasanProduk = [];
        foreach ($produkTerjual as $key => $value) {
            $ringkasanProduk[] = $value;
        }

        // Data per bahan baku (ringkasan)
        $ringkasanBahan = [];
        foreach ($bahanTerpakai as $key => $value) {
            $ringkasanBahan[] = $value;
        }

        return view('laporan.laba-rugi', compact(
            'penjualan',
            'totalPendapatan',
            'totalHpp',
            'totalLaba',
            'totalTransaksi',
            'ringkasanProduk',
            'ringkasanBahan'
        ));
    }

    /**
     * Laporan Keuntungan per Produk
     */
    public function keuntunganProduk(Request $request)
    {
        $query = Penjualan::with(['details.jenisKeripik'])
            ->where('status', 'selesai');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $penjualan = $query->get();

        $dataProduk = [];
        foreach ($penjualan as $p) {
            foreach ($p->details as $detail) {
                $namaProduk = $detail->jenisKeripik->nama_jenis ?? 'Unknown';
                if (!isset($dataProduk[$namaProduk])) {
                    $dataProduk[$namaProduk] = [
                        'nama' => $namaProduk,
                        'qty' => 0,
                        'pendapatan' => 0,
                        'hpp' => 0,
                        'laba' => 0,
                        'margin' => 0,
                    ];
                }
                
                $qty = $detail->jumlah;
                $pendapatan = $detail->subtotal;
                
                // Hitung HPP produk
                $hppProduk = 0;
                $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                foreach ($komposisi as $kom) {
                    $hargaBahan = $kom->bahanBaku->harga_satuan ?? 0;
                    $hppProduk += $kom->jumlah_dibutuhkan * $hargaBahan;
                }
                $totalHppProduk = $hppProduk * $qty;
                $laba = $pendapatan - $totalHppProduk;
                $margin = $pendapatan > 0 ? ($laba / $pendapatan) * 100 : 0;

                $dataProduk[$namaProduk]['qty'] += $qty;
                $dataProduk[$namaProduk]['pendapatan'] += $pendapatan;
                $dataProduk[$namaProduk]['hpp'] += $totalHppProduk;
                $dataProduk[$namaProduk]['laba'] += $laba;
                $dataProduk[$namaProduk]['margin'] = $dataProduk[$namaProduk]['pendapatan'] > 0 
                    ? ($dataProduk[$namaProduk]['laba'] / $dataProduk[$namaProduk]['pendapatan']) * 100 
                    : 0;
            }
        }

        // Sort by laba tertinggi
        usort($dataProduk, function($a, $b) {
            return $b['laba'] <=> $a['laba'];
        });

        return view('laporan.keuntungan-produk', compact('dataProduk'));
    }

    /**
     * Laporan Keuntungan per Bahan Baku
     */
    public function keuntunganBahan(Request $request)
    {
        $query = Penjualan::with(['details.jenisKeripik'])
            ->where('status', 'selesai');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $penjualan = $query->get();

        $dataBahan = [];
        foreach ($penjualan as $p) {
            foreach ($p->details as $detail) {
                $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                foreach ($komposisi as $kom) {
                    $namaBahan = $kom->bahanBaku->nama_bahan ?? 'Unknown';
                    if (!isset($dataBahan[$namaBahan])) {
                        $dataBahan[$namaBahan] = [
                            'nama' => $namaBahan,
                            'satuan' => $kom->bahanBaku->satuan ?? '-',
                            'total_terpakai' => 0,
                            'total_biaya' => 0,
                            'harga_satuan' => $kom->bahanBaku->harga_satuan ?? 0,
                        ];
                    }
                    $jumlahTerpakai = $kom->jumlah_dibutuhkan * $detail->jumlah;
                    $dataBahan[$namaBahan]['total_terpakai'] += $jumlahTerpakai;
                    $dataBahan[$namaBahan]['total_biaya'] += $jumlahTerpakai * ($kom->bahanBaku->harga_satuan ?? 0);
                }
            }
        }

        // Sort by total biaya tertinggi
        usort($dataBahan, function($a, $b) {
            return $b['total_biaya'] <=> $a['total_biaya'];
        });

        return view('laporan.keuntungan-bahan', compact('dataBahan'));
    }

    /**
     * Dashboard Laporan Keuangan
     */
    public function dashboardKeuangan()
    {
        // Data bulan ini
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Total pendapatan bulan ini
        $pendapatanBulanIni = Penjualan::where('status', 'selesai')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->sum('total_harga');

        // Total HPP bulan ini
        $penjualanBulanIni = Penjualan::with('details')
            ->where('status', 'selesai')
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->get();

        $hppBulanIni = 0;
        foreach ($penjualanBulanIni as $p) {
            foreach ($p->details as $detail) {
                $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                foreach ($komposisi as $kom) {
                    $hargaBahan = $kom->bahanBaku->harga_satuan ?? 0;
                    $hppBulanIni += $kom->jumlah_dibutuhkan * $hargaBahan * $detail->jumlah;
                }
            }
        }

        $labaBulanIni = $pendapatanBulanIni - $hppBulanIni;

        // Data 6 bulan terakhir untuk grafik
        $dataBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = now()->subMonths($i);
            $start = $bulan->copy()->startOfMonth();
            $end = $bulan->copy()->endOfMonth();

            $penjualanBulan = Penjualan::with('details')
                ->where('status', 'selesai')
                ->whereBetween('tanggal', [$start, $end])
                ->get();

            $pendapatan = $penjualanBulan->sum('total_harga');
            $hpp = 0;
            foreach ($penjualanBulan as $p) {
                foreach ($p->details as $detail) {
                    $komposisi = Komposisi::where('jenis_keripik_id', $detail->jenis_keripik_id)->get();
                    foreach ($komposisi as $kom) {
                        $hargaBahan = $kom->bahanBaku->harga_satuan ?? 0;
                        $hpp += $kom->jumlah_dibutuhkan * $hargaBahan * $detail->jumlah;
                    }
                }
            }

            $dataBulanan[] = [
                'bulan' => $bulan->format('M Y'),
                'pendapatan' => $pendapatan,
                'hpp' => $hpp,
                'laba' => $pendapatan - $hpp,
            ];
        }

        return view('dashboard.keuangan', compact(
            'pendapatanBulanIni',
            'hppBulanIni',
            'labaBulanIni',
            'dataBulanan'
        ));
    }

    /**
     * Cetak Laporan Laba Rugi PDF
     */
    public function printLabaRugi(Request $request)
    {
        // Sama seperti method labaRugi tapi untuk print
        $query = Penjualan::with(['details.jenisKeripik', 'user'])
            ->where('status', 'selesai');

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $penjualan = $query->latest('tanggal')->get();
        
        $totalPendapatan = $penjualan->sum('total_harga');
        $totalHpp = $penjualan->sum(function($item) {
            return $item->hpp;
        });
        $totalLaba = $totalPendapatan - $totalHpp;

        return view('laporan.print-laba-rugi', compact(
            'penjualan',
            'totalPendapatan',
            'totalHpp',
            'totalLaba'
        ));
    }
}