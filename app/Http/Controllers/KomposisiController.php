<?php

namespace App\Http\Controllers;

use App\Models\Komposisi;
use App\Models\JenisKeripik;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KomposisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Komposisi::with(['jenisKeripik', 'bahanBaku']);

        // Filter berdasarkan jenis keripik
        if ($request->filled('jenis_keripik_id')) {
            $query->where('jenis_keripik_id', $request->jenis_keripik_id);
        }

        // Filter berdasarkan bahan baku
        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        $komposisi = $query->latest()->paginate(10);
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();

        return view('komposisi.index', compact('komposisi', 'jenisKeripik', 'bahanBaku'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        return view('komposisi.create', compact('jenisKeripik', 'bahanBaku'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_keripik_id' => 'required|exists:jenis_keripik,id',
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'jumlah_dibutuhkan' => 'required|numeric|min:0.01',
        ]);

        // Cek duplikasi kombinasi jenis keripik dan bahan baku
        $exists = Komposisi::where('jenis_keripik_id', $request->jenis_keripik_id)
            ->where('bahan_baku_id', $request->bahan_baku_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Komposisi untuk jenis keripik dan bahan baku ini sudah ada!');
        }

        Komposisi::create($request->all());

        return redirect()->route('komposisi.index')
            ->with('success', 'Komposisi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Komposisi $komposisi)
    {
        $komposisi->load(['jenisKeripik', 'bahanBaku']);
        return view('komposisi.show', compact('komposisi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Komposisi $komposisi)
    {
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        return view('komposisi.edit', compact('komposisi', 'jenisKeripik', 'bahanBaku'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Komposisi $komposisi)
    {
        $request->validate([
            'jenis_keripik_id' => 'required|exists:jenis_keripik,id',
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'jumlah_dibutuhkan' => 'required|numeric|min:0.01',
        ]);

        // Cek duplikasi kombinasi jenis keripik dan bahan baku (kecuali dirinya sendiri)
        $exists = Komposisi::where('jenis_keripik_id', $request->jenis_keripik_id)
            ->where('bahan_baku_id', $request->bahan_baku_id)
            ->where('id', '!=', $komposisi->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Komposisi untuk jenis keripik dan bahan baku ini sudah ada!');
        }

        $komposisi->update($request->all());

        return redirect()->route('komposisi.index')
            ->with('success', 'Komposisi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Komposisi $komposisi)
    {
        $komposisi->delete();

        return redirect()->route('komposisi.index')
            ->with('success', 'Komposisi berhasil dihapus!');
    }

    /**
     * Get bahan baku by jenis keripik (for API)
     */
    public function getBahanByJenis($jenisKeripikId)
    {
        $komposisi = Komposisi::with('bahanBaku')
            ->where('jenis_keripik_id', $jenisKeripikId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $komposisi
        ]);
    }

    /**
     * Laporan komposisi
     */
    public function laporan(Request $request)
    {
        $query = Komposisi::with(['jenisKeripik', 'bahanBaku']);

        if ($request->filled('jenis_keripik_id')) {
            $query->where('jenis_keripik_id', $request->jenis_keripik_id);
        }

        $komposisi = $query->latest()->get();
        $jenisKeripik = JenisKeripik::orderBy('nama_jenis')->get();

        return view('komposisi.laporan', compact('komposisi', 'jenisKeripik'));
    }

    /**
     * Hitung biaya produksi per jenis keripik
     */
    public function biayaProduksi()
    {

    dd('baiaya produksi');
        $jenisKeripik = JenisKeripik::with(['komposisi.bahanBaku'])->get();
        
        $data = [];
        foreach ($jenisKeripik as $jk) {
            $totalBiaya = 0;
            foreach ($jk->komposisi as $kom) {
                $totalBiaya += $kom->jumlah_dibutuhkan * $kom->bahanBaku->harga_satuan;
            }
            $data[] = [
                'jenis_keripik' => $jk->nama_jenis,
                'total_biaya' => $totalBiaya,
                'total_biaya_formatted' => 'Rp ' . number_format($totalBiaya, 0, ',', '.'),
                'komposisi' => $jk->komposisi
            ];
        }

        return view('komposisi.biaya-produksi', compact('data'));
    }
}