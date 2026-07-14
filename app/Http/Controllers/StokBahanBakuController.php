<?php

namespace App\Http\Controllers;

use App\Models\StokBahanBaku;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokBahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = StokBahanBaku::with(['bahanBaku']);

        // Filter berdasarkan bahan baku
        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_update', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_update', '<=', $request->end_date);
        }

        // Filter stok kritis
        if ($request->filled('stok_kritis')) {
            $query->where('jumlah_stok', '<', $request->stok_kritis);
        }

        $stokBahanBaku = $query->latest('tanggal_update')->paginate(10);
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();

        return view('stok-bahan-baku.index', compact('stokBahanBaku', 'bahanBaku'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        return view('stok-bahan-baku.create', compact('bahanBaku'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'jumlah_masuk' => 'required|numeric|min:0',
            'jumlah_keluar' => 'required|numeric|min:0',
            'tanggal_update' => 'required|date',
        ]);

        // Hitung stok: stok = stok + masuk - keluar
        $jumlahStok = $request->jumlah_stok + $request->jumlah_masuk - $request->jumlah_keluar;

        // Cek apakah stok tidak negatif
        if ($jumlahStok < 0) {
            return back()->with('error', 'Stok akhir tidak boleh negatif!');
        }

        StokBahanBaku::create([
            'bahan_baku_id' => $request->bahan_baku_id,
            'jumlah_stok' => $jumlahStok,
            'jumlah_masuk' => $request->jumlah_masuk,
            'jumlah_keluar' => $request->jumlah_keluar,
            'tanggal_update' => $request->tanggal_update,
        ]);

        return redirect()->route('stok-bahan-baku.index')
            ->with('success', 'Data stok bahan baku berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StokBahanBaku $stokBahanBaku)
    {
        $stokBahanBaku->load(['bahanBaku']);
        return view('stok-bahan-baku.show', compact('stokBahanBaku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokBahanBaku $stokBahanBaku)
    {
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        return view('stok-bahan-baku.edit', compact('stokBahanBaku', 'bahanBaku'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StokBahanBaku $stokBahanBaku)
    {
        $request->validate([
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'jumlah_masuk' => 'required|numeric|min:0',
            'jumlah_keluar' => 'required|numeric|min:0',
            'tanggal_update' => 'required|date',
        ]);

        // Hitung stok: stok = stok + masuk - keluar
        $jumlahStok = $request->jumlah_stok + $request->jumlah_masuk - $request->jumlah_keluar;

        // Cek apakah stok tidak negatif
        if ($jumlahStok < 0) {
            return back()->with('error', 'Stok akhir tidak boleh negatif!');
        }

        $stokBahanBaku->update([
            'bahan_baku_id' => $request->bahan_baku_id,
            'jumlah_stok' => $jumlahStok,
            'jumlah_masuk' => $request->jumlah_masuk,
            'jumlah_keluar' => $request->jumlah_keluar,
            'tanggal_update' => $request->tanggal_update,
        ]);

        return redirect()->route('stok-bahan-baku.index')
            ->with('success', 'Data stok bahan baku berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokBahanBaku $stokBahanBaku)
    {
        $stokBahanBaku->delete();

        return redirect()->route('stok-bahan-baku.index')
            ->with('success', 'Data stok bahan baku berhasil dihapus!');
    }

    /**
     * API untuk mendapatkan stok berdasarkan bahan baku
     */
    public function getStokByBahan($bahanBakuId)
    {
        $stok = StokBahanBaku::where('bahan_baku_id', $bahanBakuId)
            ->latest('tanggal_update')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stok
        ]);
    }

    /**
     * Laporan stok bahan baku
     */
    public function laporan(Request $request)
    {
        $query = StokBahanBaku::with(['bahanBaku']);

        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_update', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_update', '<=', $request->end_date);
        }

        $stokBahanBaku = $query->latest('tanggal_update')->get();
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();

        return view('stok-bahan-baku.laporan', compact('stokBahanBaku', 'bahanBaku'));
    }

    /**
     * Dashboard stok bahan baku
     */
    public function dashboard()
    {
        // Total stok per bahan baku
        $stokPerBahan = StokBahanBaku::with('bahanBaku')
            ->select('bahan_baku_id', DB::raw('SUM(jumlah_stok) as total_stok'))
            ->groupBy('bahan_baku_id')
            ->get();

        // Stok kritis (kurang dari 10)
        $stokKritis = StokBahanBaku::with('bahanBaku')
            ->where('jumlah_stok', '<', 10)
            ->get();

        // Total semua stok
        $totalStok = StokBahanBaku::sum('jumlah_stok');

        return view('stok-bahan-baku.dashboard', compact('stokPerBahan', 'stokKritis', 'totalStok'));
    }
}