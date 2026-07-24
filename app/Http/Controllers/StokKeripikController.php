<?php

namespace App\Http\Controllers;

use App\Models\JenisKeripik;
use App\Models\StokeKeripik;
use App\Models\StokKeripik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokKeripikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index()
    {
        // Ambil semua stok keripik dengan relasi jenis keripik
        $stokKeripik = StokeKeripik::with('jenisKeripik')
            ->orderBy('tanggal_update', 'desc')
            ->paginate(10);

        return view('stok-keripik.index', compact('stokKeripik'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        // $users = User::orderBy('name')->get();
        return view('stok-keripik.create', compact('jenisKeripik'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_keripik_id' => 'required|exists:jenis_keripik,id',
            'jumlah_stok' => 'required|integer|min:0',
            'jumlah_masuk' => 'required|integer|min:0',
            'jumlah_keluar' => 'required|integer|min:0',
            'tanggal_update' => 'required|date',
            // 'user_id' => 'required|exists:users,id',
        ]);

        // Hitung stok: stok = stok + masuk - keluar
        $jumlahStok = $request->jumlah_stok + $request->jumlah_masuk - $request->jumlah_keluar;

        StokeKeripik::create([
            'jenis_keripik_id' => $request->jenis_keripik_id,
            'jumlah_stok' => $jumlahStok,
            'jumlah_masuk' => $request->jumlah_masuk,
            'jumlah_keluar' => $request->jumlah_keluar,
            'tanggal_update' => $request->tanggal_update,
        ]);

        return redirect()->route('stok-keripik.index')
            ->with('success', 'Data stok keripik berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StokKeripik $stokKeripik)
    {
        $stokKeripik->load(['jenisKeripik', 'user']);
        return view('stok-keripik.show', compact('stokKeripik'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StokeKeripik $stokKeripik)
    {
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        // $users = User::orderBy('name')->get();
        return view('stok-keripik.edit', compact('stokKeripik', 'jenisKeripik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StokeKeripik $stokKeripik)
    {
        $request->validate([
            'jenis_keripik_id' => 'required|exists:jenis_keripik,id',
            'jumlah_stok' => 'required|integer|min:0',
            'jumlah_masuk' => 'required|integer|min:0',
            'jumlah_keluar' => 'required|integer|min:0',
            'tanggal_update' => 'required|date',
        ]);

        // Hitung stok: stok = stok + masuk - keluar
        $jumlahStok = $request->jumlah_stok + $request->jumlah_masuk - $request->jumlah_keluar;

        $stokKeripik->update([
            'jenis_keripik_id' => $request->jenis_keripik_id,
            'jumlah_stok' => $jumlahStok,
            'jumlah_masuk' => $request->jumlah_masuk,
            'jumlah_keluar' => $request->jumlah_keluar,
            'tanggal_update' => $request->tanggal_update,
        ]);

        return redirect()->route('stok-keripik.index')
            ->with('success', 'Data stok keripik berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokeKeripik $stokKeripik)
    {
        $stokKeripik->delete();

        return redirect()->route('stok-keripik.index')
            ->with('success', 'Data stok keripik berhasil dihapus!');
    }

    /**
     * API untuk mendapatkan stok berdasarkan jenis keripik
     */
    public function getStokByJenis($jenisKeripikId)
    {
        $stok = StokKeripik::where('jenis_keripik_id', $jenisKeripikId)
            ->latest('tanggal_update')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stok
        ]);
    }

    /**
     * Laporan stok
     */
    public function laporan(Request $request)
    {
        $query = StokKeripik::with(['jenisKeripik', 'user']);

        if ($request->filled('jenis_keripik_id')) {
            $query->where('jenis_keripik_id', $request->jenis_keripik_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_update', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_update', '<=', $request->end_date);
        }

        $stokKeripik = $query->latest('tanggal_update')->get();
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();

        return view('backend.stok-keripik.laporan', compact('stokKeripik', 'jenisKeripik'));
    }
}