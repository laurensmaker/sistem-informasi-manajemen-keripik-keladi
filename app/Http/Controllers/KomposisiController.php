<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\JenisKeripik;
use App\Models\Komposisi;
use App\Models\StokBahanBaku;
use App\Models\StokeKeripik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KomposisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil data produksi yang dikelompokkan berdasarkan kode_produksi
        $query = Komposisi::with(['jenisKeripik.stok', 'user'])
            ->whereNotNull('kode_produksi')
            ->select(
                'kode_produksi',
                'jenis_keripik_id',
                'jumlah_produksi',
                'total_biaya',
                'tanggal_produksi',
                'user_id',
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(updated_at) as updated_at')
            )
            ->groupBy('kode_produksi', 'jenis_keripik_id', 'jumlah_produksi', 'total_biaya', 'tanggal_produksi', 'user_id');

        // Filter tanggal
        if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal_produksi', [
                $request->dari_tanggal . ' 00:00:00',
                $request->sampai_tanggal . ' 23:59:59'
            ]);
        }

        // Filter jenis keripik
        if ($request->filled('jenis_keripik_id')) {
            $query->where('jenis_keripik_id', $request->jenis_keripik_id);
        }

        $komposisi = $query->orderBy('tanggal_produksi', 'desc')->paginate(10);

        // Ambil daftar jenis keripik untuk filter
        $jenisKeripikList = JenisKeripik::all();

        return view('komposisi.index', compact('komposisi', 'jenisKeripikList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisKeripik = JenisKeripik::all();
        
        // Ambil bahan baku dari tabel stok bahan baku yang memiliki stok > 0
        $bahanBaku = StokBahanBaku::with('bahanBaku')
            ->where('jumlah_stok', '>', 0)
            ->get()
            ->map(function($item) {
                return (object) [
                    'id' => $item->bahanBaku->id,
                    'nama_bahan' => $item->bahanBaku->nama_bahan,
                    'satuan' => $item->bahanBaku->satuan,
                    'harga_satuan' => $item->bahanBaku->harga_satuan,
                    'stok' => $item->jumlah_stok
                ];
            });

        return view('komposisi.create', compact('jenisKeripik', 'bahanBaku'));
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        try {
            $request->validate([
                'jenis_keripik_id' => 'required|exists:jenis_keripik,id',
                'bahan_baku_id' => 'required|array|min:1',
                'bahan_baku_id.*' => 'exists:bahan_baku,id',
                'jumlah' => 'required|array',
                'jumlah.*' => 'required|numeric|min:0.01',
                'stok_awal' => 'nullable|integer|min:0'
            ]);

            \DB::beginTransaction();
            try {
                // Ambil data jenis keripik
                $jenisKeripik = JenisKeripik::find($request->jenis_keripik_id);
                
                if (!$jenisKeripik) {
                    throw new \Exception("Jenis keripik tidak ditemukan!");
                }

                $stokAwal = $request->stok_awal ?? 0;
                $totalBiaya = 0;
                $detailKebutuhan = [];

                // 1. Hitung total biaya dan validasi stok bahan baku
                foreach ($request->bahan_baku_id as $bahanId) {
                    $jumlahDibutuhkan = $request->jumlah[$bahanId];
                    
                    // Ambil data bahan baku
                    $bahanBaku = BahanBaku::find($bahanId);
                    
                    if (!$bahanBaku) {
                        throw new \Exception("Bahan baku ID {$bahanId} tidak ditemukan!");
                    }

                    $stokBahan = StokBahanBaku::where('bahan_baku_id', $bahanId)->first();
                    
                    if (!$stokBahan) {
                        throw new \Exception("Stok untuk bahan baku {$bahanBaku->nama_bahan} tidak ditemukan!");
                    }

                    if ($stokBahan->jumlah_stok < $jumlahDibutuhkan) {
                        throw new \Exception(
                            "Stok tidak mencukupi! " .
                            "Bahan: {$bahanBaku->nama_bahan}, " .
                            "Stok tersedia: {$stokBahan->jumlah_stok}, " .
                            "Dibutuhkan: {$jumlahDibutuhkan}"
                        );
                    }

                    // Hitung biaya per bahan
                    $biayaBahan = $bahanBaku->harga_satuan * $jumlahDibutuhkan;
                    $totalBiaya += $biayaBahan;

                    $detailKebutuhan[] = [
                        'bahan_baku_id' => $bahanId,
                        'jumlah_dibutuhkan' => $jumlahDibutuhkan,
                        'biaya' => $biayaBahan
                    ];
                }

                // 2. Generate kode produksi
                $kodeProduksi = Komposisi::generateKodeProduksi();

                // 3. Simpan komposisi dan kurangi stok bahan baku
                foreach ($detailKebutuhan as $item) {
                    // Simpan komposisi dengan field lengkap
                    $komposisi = Komposisi::create([
                        'jenis_keripik_id' => $request->jenis_keripik_id,
                        'bahan_baku_id' => $item['bahan_baku_id'],
                        'jumlah_dibutuhkan' => $item['jumlah_dibutuhkan'],
                        'kode_produksi' => $kodeProduksi,
                        'jumlah_produksi' => $stokAwal,
                        'total_biaya' => $totalBiaya,
                        'tanggal_produksi' => now(),
                        'user_id' => auth()->id(),
                        'status_produksi' => 'selesai'
                    ]);

                    if (!$komposisi) {
                        throw new \Exception("Gagal menyimpan komposisi untuk bahan {$item['bahan_baku_id']}");
                    }

                    // Update stok bahan baku
                    $stokBahan = StokBahanBaku::where('bahan_baku_id', $item['bahan_baku_id'])->first();
                    $stokBahan->jumlah_stok -= $item['jumlah_dibutuhkan'];
                    $stokBahan->jumlah_keluar += $item['jumlah_dibutuhkan'];
                    $stokBahan->tanggal_update = now();
                    $stokBahan->save();
                }

                // 4. Tambah stok keripik
                if ($stokAwal > 0) {
                    $stokKeripik = StokeKeripik::where('jenis_keripik_id', $request->jenis_keripik_id)->first();
                    
                    if (!$stokKeripik) {
                        // Buat stok keripik baru
                        $kodeKeripik = StokeKeripik::generateKode(
                            $request->jenis_keripik_id, 
                            $jenisKeripik->berat
                        );
                        
                        $stokKeripik = StokeKeripik::create([
                            'jenis_keripik_id' => $request->jenis_keripik_id,
                            'kode_keripik' => $kodeKeripik,
                            'jumlah_stok' => $stokAwal,
                            'jumlah_masuk' => $stokAwal,
                            'jumlah_keluar' => 0,
                            'tanggal_update' => now()
                        ]);
                    } else {
                        // Tambah stok yang sudah ada
                        $stokKeripik->jumlah_stok += $stokAwal;
                        $stokKeripik->jumlah_masuk += $stokAwal;
                        $stokKeripik->tanggal_update = now();
                        $stokKeripik->save();
                    }
                }

                \DB::commit();

                return redirect()->route('komposisi.index')
                    ->with('success', "Komposisi berhasil ditambahkan! Kode Produksi: {$kodeProduksi}, Stok bertambah: {$stokAwal} pcs.");

            } catch (\Exception $e) {
                \DB::rollback();
                
                // Log error untuk debugging
                \Log::error('Error store komposisi: ' . $e->getMessage());
                \Log::error($e->getTraceAsString());
                
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan komposisi: ' . $e->getMessage())
                    ->withInput();
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
     public function show($kodeProduksi)
    {
        // Ambil detail produksi berdasarkan kode_produksi
        $produksi = Komposisi::with(['jenisKeripik', 'bahanBaku', 'user'])
            ->where('kode_produksi', $kodeProduksi)
            ->get();

        if ($produksi->isEmpty()) {
            return redirect()->route('komposisi.index')
                ->with('error', 'Data produksi tidak ditemukan!');
        }

        // Ambil data utama dari produksi pertama
        $detail = $produksi->first();

        return view('komposisi.show', compact('produksi', 'detail'));
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
     public function destroy($kodeProduksi)
    {
        DB::beginTransaction();
        try {
            // Ambil semua komposisi dengan kode_produksi yang sama
            $komposisiProduksi = Komposisi::where('kode_produksi', $kodeProduksi)->get();
            
            if ($komposisiProduksi->isEmpty()) {
                throw new \Exception("Data produksi tidak ditemukan!");
            }

            $detail = $komposisiProduksi->first();
            $jumlahProduksi = $detail->jumlah_produksi;
            $jenisKeripikId = $detail->jenis_keripik_id;

            // Kembalikan stok keripik
            $stokKeripik = StokeKeripik::where('jenis_keripik_id', $jenisKeripikId)->first();
            if ($stokKeripik) {
                $stokKeripik->jumlah_stok -= $jumlahProduksi;
                $stokKeripik->jumlah_keluar += $jumlahProduksi;
                $stokKeripik->tanggal_update = now();
                $stokKeripik->save();
            }

            // Hapus semua komposisi dengan kode_produksi tersebut
            Komposisi::where('kode_produksi', $kodeProduksi)->delete();

            DB::commit();

            return redirect()->route('komposisi.index')
                ->with('success', 'Data produksi berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menghapus data produksi: ' . $e->getMessage());
        }
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