<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use App\Models\JenisKeripik;
use App\Models\Penjualan;
use App\Models\StokeKeripik;
use App\Models\StokKeripik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $query = Penjualan::with(['user', 'details.jenisKeripik']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter berdasarkan user (jika bukan owner)
        if (!auth()->user()->isOwner()) {
            $query->where('user_id', auth()->id());
        }

        $penjualan = $query->latest('tanggal')->paginate(10);
        
        // Statistik
        $totalPenjualan = Penjualan::where('status', 'selesai')->sum('total_harga');
        $totalTransaksi = Penjualan::count();
        $totalPesanan = Penjualan::where('status', 'pesan')->count();
        $totalProses = Penjualan::where('status', 'proses')->count();

        return view('penjualan.index', compact('penjualan', 'totalPenjualan', 'totalTransaksi', 'totalPesanan', 'totalProses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate no transaksi
        $lastPenjualan = Penjualan::orderBy('id', 'desc')->first();
        $noTransaksi = 'TRX-' . date('Ymd') . '-' . str_pad(($lastPenjualan ? $lastPenjualan->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        // Ambil jenis keripik dengan stok menggunakan with (eager loading)
        $jenisKeripik = JenisKeripik::with('stok')->get();

        return view('penjualan.create', compact('jenisKeripik', 'noTransaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date',
                'nama_pembeli' => 'required|string|max:50',
                'no_hp_pembeli' => 'nullable|string|max:15',
                'items' => 'required|array|min:1',
                'items.*.jenis_keripik_id' => 'required|exists:jenis_keripik,id',
                'items.*.jumlah' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $totalHarga = 0;
            $details = [];

            foreach ($request->items as $item) {
                $jenisKeripik = JenisKeripik::find($item['jenis_keripik_id']);
                
                if (!$jenisKeripik) {
                    throw new \Exception("Produk tidak ditemukan!");
                }

                // Cek dan kurangi stok
                $stok = StokeKeripik::where('jenis_keripik_id', $item['jenis_keripik_id'])->first();

                if (!$stok) {
                    throw new \Exception("Stok untuk {$jenisKeripik->nama_jenis} belum tersedia!");
                }

                // Validasi stok (integer)
                if ($stok->jumlah_stok < $item['jumlah']) {
                    throw new \Exception(
                        "Stok {$jenisKeripik->nama_jenis} tidak mencukupi! " .
                        "Stok tersedia: {$stok->jumlah_stok}, " .
                        "Diminta: {$item['jumlah']}"
                    );
                }

                // Gunakan method dari model untuk kurangi stok
                $stok->kurangiStok(
                    $item['jumlah'], 
                    "Penjualan - Pembeli: {$request->nama_pembeli}"
                );

                $subtotal = $jenisKeripik->harga_jual * $item['jumlah'];
                $totalHarga += $subtotal;

                $details[] = [
                    'jenis_keripik_id' => $item['jenis_keripik_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $jenisKeripik->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            // Generate no_transaksi
            $no_transaksi = 'TRX-' . date('Ymd') . '-' . str_pad(Penjualan::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create penjualan
            $penjualan = Penjualan::create([
                'no_transaksi' => $no_transaksi,
                'tanggal' => $request->tanggal,
                'nama_pembeli' => $request->nama_pembeli,
                'no_hp_pembeli' => $request->no_hp_pembeli,
                'total_harga' => $totalHarga,
                'status' => 'selesai',
                'user_id' => auth()->id(),
            ]);

            // Create detail penjualan
            foreach ($details as $detail) {
                $penjualan->details()->create($detail);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil disimpan!',
                'data' => [
                    'penjualan' => $penjualan,
                    'no_transaksi' => $no_transaksi,
                    'total_harga' => $totalHarga
                ],
                'redirect' => route('penjualan.show', $penjualan->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'details.jenisKeripik']);
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penjualan $penjualan)
    {
        // Hanya bisa edit jika status belum selesai
        if ($penjualan->status == 'selesai') {
            return redirect()->route('penjualan.index')
                ->with('error', 'Penjualan yang sudah selesai tidak dapat diubah!');
        }

        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        return view('penjualan.edit', compact('penjualan', 'jenisKeripik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penjualan $penjualan)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'nama_pembeli' => 'required|string|max:50',
            'no_hp_pembeli' => 'nullable|string|max:15',
            'status' => 'required|in:pesan,proses,selesai,batal',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $penjualan->update($request->all());

        return redirect()->route('penjualan.index')
            ->with('success', 'Penjualan berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penjualan $penjualan)
    {
        try {
            DB::beginTransaction();

            // Kembalikan stok jika status selesai
            if ($penjualan->status == 'selesai') {
                foreach ($penjualan->details as $detail) {
                    $stok = StokKeripik::where('jenis_keripik_id', $detail->jenis_keripik_id)
                        ->latest('tanggal_update')
                        ->first();

                    if ($stok) {
                        $stok->update([
                            'jumlah_stok' => $stok->jumlah_stok + $detail->jumlah,
                            'jumlah_masuk' => $stok->jumlah_masuk + $detail->jumlah,
                            'tanggal_update' => now(),
                        ]);
                    }
                }
            }

            $penjualan->details()->delete();
            $penjualan->delete();

            DB::commit();

            return redirect()->route('penjualan.index')
                ->with('success', 'Penjualan berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Update status penjualan
     */
    public function updateStatus(Request $request, Penjualan $penjualan)
    {
        $request->validate([
            'status' => 'required|in:pesan,proses,selesai,batal'
        ]);

        $penjualan->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate!'
        ]);
    }

    /**
     * Laporan penjualan
     */
    public function laporan(Request $request)
    {
        $query = Penjualan::with(['user', 'details.jenisKeripik']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penjualan = $query->latest('tanggal')->get();
        
        // Statistik
        $totalPendapatan = $penjualan->where('status', 'selesai')->sum('total_harga');
        $totalTransaksi = $penjualan->count();
        $totalItem = $penjualan->sum(function($item) {
            return $item->details->sum('jumlah');
        });

        return view('penjualan.laporan', compact('penjualan', 'totalPendapatan', 'totalTransaksi', 'totalItem'));
    }

    /**
     * Get detail produk untuk transaksi
     */
    public function getProduk($id)
    {
        $jenisKeripik = JenisKeripik::with(['stokKeripik' => function($query) {
            $query->latest('tanggal_update');
        }])->find($id);

        if (!$jenisKeripik) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
        }

        $stok = $jenisKeripik->stokKeripik->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jenisKeripik->id,
                'nama_jenis' => $jenisKeripik->nama_jenis,
                'harga_jual' => $jenisKeripik->harga_jual,
                'harga_formatted' => 'Rp ' . number_format($jenisKeripik->harga_jual, 0, ',', '.'),
                'stok' => $stok ? $stok->jumlah_stok : 0,
                'satuan' => $jenisKeripik->satuan ?? 'pcs',
            ]
        ]);
    }

    /**
     * Print struk
     */
    public function printStruk(Penjualan $penjualan)
    {
        $penjualan->load(['user', 'details.jenisKeripik']);
        return view('penjualan.struk', compact('penjualan'));
    }
}