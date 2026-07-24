<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\JenisKeripik;
use App\Models\Komposisi;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;


class LaporanController extends Controller
{
   public function index()
    {
        return view('laporan.index');
    }

    // ============= LAPORAN PENJUALAN =============
     public function laporanPenjualan(Request $request)
    {
        // Default filter: bulan ini
        $dariTanggal = $request->dari_tanggal;
        $sampaiTanggal = $request->sampai_tanggal;
        $status = $request->status;

        $query = Penjualan::with(['details.jenisKeripik', 'user']);

        // Filter tanggal
        if ($dariTanggal && $sampaiTanggal) {
            $query->whereBetween('tanggal', [
                $dariTanggal . ' 00:00:00',
                $sampaiTanggal . ' 23:59:59'
            ]);
        }

        // Filter status
        if ($status) {
            $query->where('status', $status);
        }

        $penjualan = $query->orderBy('tanggal', 'desc')->paginate(10);

        $totalPenjualan = $query->get()->sum('total_harga');
        $totalItems = $query->get()->sum(function($item) {
            return $item->details->sum('jumlah');
        });

        return view('laporan.penjualan', compact('penjualan', 'totalPenjualan', 'totalItems'));
    }

   public function downloadPenjualanPDF(Request $request)
    {
        // Tidak ada default filter
        $query = Penjualan::with(['details.jenisKeripik', 'user']);

        // Filter tanggal (hanya jika ada parameter)
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal', [
                $request->dari_tanggal . ' 00:00:00',
                $request->sampai_tanggal . ' 23:59:59'
            ]);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penjualan = $query->orderBy('tanggal', 'desc')->get();

        // Jika tidak ada data, redirect dengan pesan
        if ($penjualan->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada data penjualan pada periode yang dipilih!');
        }

        $totalPenjualan = $penjualan->sum('total_harga');
        $totalItems = $penjualan->sum(function($item) {
            return $item->details->sum('jumlah');
        });

        // Tentukan subtitle berdasarkan filter
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $subtitle = 'Periode: ' . date('d/m/Y', strtotime($request->dari_tanggal)) . 
                        ' - ' . date('d/m/Y', strtotime($request->sampai_tanggal));
        } else {
            $subtitle = 'Semua Data Penjualan';
        }

        $data = [
            'title' => 'LAPORAN PENJUALAN',
            'subtitle' => $subtitle,
            'penjualan' => $penjualan,
            'totalPenjualan' => $totalPenjualan,
            'totalItems' => $totalItems,
            'totalTransaksi' => $penjualan->count(),
            'tanggal_cetak' => date('d/m/Y H:i:s'),
            'dari_tanggal' => $request->dari_tanggal,
            'sampai_tanggal' => $request->sampai_tanggal,
            'status' => $request->status,
        ];

        $pdf = Pdf::loadView('laporan.pdf.penjualan', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        
        return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
    }

    // ============= LAPORAN JENIS KERIPIK DENGAN STOK =============
    public function laporanJenisKeripik()
    {
        $jenisKeripik = JenisKeripik::with(['stok', 'komposisi.bahanBaku'])->get();
        return view('laporan.jenis-keripik', compact('jenisKeripik'));
    }

    public function downloadJenisKeripikPDF()
{
    $jenisKeripik = JenisKeripik::with(['stok', 'komposisi.bahanBaku'])->get();

    // DEBUG: Cek data
    // dd($jenisKeripik->first()->stok); // Lihat apakah stok ada

    $totalStok = 0;
    foreach ($jenisKeripik as $item) {
        // Cek apakah relasi stok ada
        if ($item->stok) {
            $totalStok += $item->stok->jumlah_stok;
        }
    }

    $totalJenis = $jenisKeripik->count();

    $data = [
        'title' => 'LAPORAN JENIS KERIPIK & STOK',
        'subtitle' => 'Data Seluruh Jenis Keripik dan Stok Tersedia',
        'jenisKeripik' => $jenisKeripik,
        'totalStok' => $totalStok,
        'totalJenis' => $totalJenis,
        'tanggal_cetak' => date('d/m/Y H:i:s'),
    ];

    $pdf = Pdf::loadView('laporan.pdf.jenis-keripik', $data);
    $pdf->setPaper('A4', 'landscape');
    $pdf->setOptions([
        'defaultFont' => 'sans-serif',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
    ]);
    
    return $pdf->download('laporan-jenis-keripik-' . date('Y-m-d') . '.pdf');
}

    // ============= LAPORAN BAHAN BAKU DENGAN STOK =============
    public function laporanBahanBaku()
    {
        $bahanBaku = BahanBaku::with('stok')->get();
        return view('laporan.bahan-baku', compact('bahanBaku'));
    }

    public function downloadBahanBakuPDF()
    {
        $bahanBaku = BahanBaku::with('stok')->get();

        $totalStok = $bahanBaku->sum(function($item) {
            return $item->stok->jumlah_stok ?? 0;
        });

        $totalMasuk = $bahanBaku->sum(function($item) {
            return $item->stok->jumlah_masuk ?? 0;
        });

        $totalKeluar = $bahanBaku->sum(function($item) {
            return $item->stok->jumlah_keluar ?? 0;
        });

        $totalBahan = $bahanBaku->count();
        $totalNilaiStok = $bahanBaku->sum(function($item) {
            return ($item->stok->jumlah_stok ?? 0) * $item->harga_satuan;
        });

        $data = [
            'title' => 'LAPORAN BAHAN BAKU & STOK',
            'subtitle' => 'Data Seluruh Bahan Baku dan Stok Tersedia',
            'bahanBaku' => $bahanBaku,
            'totalStok' => $totalStok,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'totalBahan' => $totalBahan,
            'totalNilaiStok' => $totalNilaiStok,
            'tanggal_cetak' => date('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('laporan.pdf.bahan-baku', $data);
        $pdf->setPaper('A4', 'landscape'); // Landscape agar lebih luas
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
            'defaultPaperSize' => 'a4',
        ]);
        
        return $pdf->download('laporan-bahan-baku-' . date('Y-m-d') . '.pdf');
    }


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
     * Display the financial dashboard.
     *
     * @return \Illuminate\Http\Response
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


}