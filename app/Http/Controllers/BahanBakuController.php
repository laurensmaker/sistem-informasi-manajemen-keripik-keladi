<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\StokBahanBaku;
use App\Models\StokTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $stok = DB::table('stok_bahan_baku')
        // ->join('bahan_baku', 'stok_bahan_baku.bahan_baku_id', '=', 'bahan_baku.id')
        // ->select(
        //     'stok_bahan_baku.*',
        //     'bahan_baku.nama_bahan',
        //     'bahan_baku.satuan',
        //     'bahan_baku.harga_satuan',
        //     'bahan_baku.supplier'
        // )
        // ->orderBy('bahan_baku.nama_bahan', 'asc')
        // ->get();
        // dd($stok);

        $bahanBaku = BahanBaku::latest()->paginate(10);
        return view('bahan-baku.index', compact('bahanBaku'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('bahan-baku.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       

        $request->validate([
            'nama_bahan' => 'required|string|max:50',
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
            'berat' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:50',
            'stok_awal' => 'nullable|numeric|min:0'
        ]);

       
            // 1. Simpan bahan baku
            $bahanBaku = BahanBaku::create($request->except('stok_awal'));

        $kode = null;
        if($request->nama_bahan == 'Keladi') {
            $kode = 'KLD';
        }else if($request->nama_bahan == 'Gula Halus') {
            $kode = 'GLA';
        }else if($request->nama_bahan == 'Garam') {
            $kode = 'GRM';
        }else if($request->nama_bahan == 'Minyak Goreng') {
            $kode = 'MNG';
        }else if($request->nama_bahan == 'Minyak Wijen') {
            $kode = 'MNW';
        }else if($request->nama_bahan == 'Minyak Tanah') {
            $kode = 'MNT';
        }else if($request->nama_bahan == 'Royco'){
            $kode = 'RYC';
        }else if($request->nama_bahan == 'Micin'){
            $kode = 'MCN';
        }else if($request->nama_bahan == 'Plastik Kemasan'){
            $kode = 'PLK';
        }else if($request->nama_bahan == 'Gas'){
            $kode = 'GAS';
        }

            $stokAwal = $request->stok_awal;

            // 2. Cek apakah bahan baku sudah ada di tabel stok
            $stokBahan = StokBahanBaku::all();

            if (!$stokBahan->contains('kode_bahan', $kode)) {


                // Jika belum ada, buat data stok baru
                $stok = StokBahanBaku::create([
                    'bahan_baku_id' => $bahanBaku->id,
                    'jumlah_stok' => $stokAwal,
                    'jumlah_masuk' => $stokAwal > 0 ? $stokAwal : 0,
                    'jumlah_keluar' => 0,
                    'kode_bahan' => $kode,
                    'tanggal_update' => now()
                ]);
            
                return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil ditambahkan dan stok diperbarui!');

            } else if ($stokBahan->contains('kode_bahan', $kode)) {
                               
                // Ambil item dari collection yang memiliki kode_bahan tersebut
                $stokItem = $stokBahan->firstWhere('kode_bahan', $kode);
                
                // Sekarang update data
                $stokSebelum = $stokItem->jumlah_stok;
                $stokItem->jumlah_stok += $stokAwal;
                $stokItem->jumlah_masuk = $stokAwal;
                $stokItem->tanggal_update = now();
                $stokItem->save();

               

                return redirect()->route('bahan-baku.index')->with('success', 'Bahan baku berhasil ditambahkan dan stok diperbarui!');
            }

    }    
    /**
     * Display the specified resource.
     */
    public function show(BahanBaku $bahanBaku)
    {
        return view('bahan-baku.show', compact('bahanBaku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BahanBaku $bahanBaku)
    {
        return view('bahan-baku.edit', compact('bahanBaku'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:50',
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
            'berat' => 'required|integer|min:0',
            'supplier' => 'nullable|string|max:50'
        ]);

        \DB::beginTransaction();
        try {
            // 1. Cari bahan baku
            $bahanBaku = BahanBaku::findOrFail($id);
            
            // 2. Simpan nama lama untuk pengecekan
            $namaLama = $bahanBaku->nama_bahan;
            
            // 3. Update data bahan baku
            $bahanBaku->update($request->all());

            // 4. Jika nama bahan berubah, update kode di stok
            if ($namaLama != $request->nama_bahan) {
                $kodeBaru = $this->generateKodeBahan($request->nama_bahan);
                
                // Update kode di tabel stok
                StokBahanBaku::where('bahan_baku_id', $bahanBaku->id)
                    ->update(['kode_bahan' => $kodeBaru]);
            }

            \DB::commit();

            return redirect()->route('bahan-baku.index')
                           ->with('success', 'Bahan baku berhasil diupdate!');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                           ->with('error', 'Gagal mengupdate bahan baku: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BahanBaku $bahanBaku)
    {
        $bahanBaku->delete();

        return redirect()->route('bahan-baku.index')
            ->with('success', 'Bahan baku berhasil dihapus!');
    }
}