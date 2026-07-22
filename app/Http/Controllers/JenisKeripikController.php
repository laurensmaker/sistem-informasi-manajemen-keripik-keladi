<?php

namespace App\Http\Controllers;

use App\Models\JenisKeripik;
use App\Models\StokeKeripik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JenisKeripikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisKeripik = JenisKeripik::latest()->paginate(10);
        return view('jenis-keripik.index', compact('jenisKeripik'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jenis-keripik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'berat' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stok_awal' => 'nullable',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
           $fotoSurveiPath = $request->file('gambar')->store('jenis_keripik/gambar', 'public');
            
            // Simpan path ke database (tanpa 'public/')
            $data['gambar'] = $fotoSurveiPath;
        }

       

        $stokAwal = $request->stok_awal;

        $jenisKeripik = JenisKeripik::create($request->except('stok_awal'));
        $kodeKeripik = '';
        if($request->nama_jenis == 'Keripik Keladi Original'){
            $kodeKeripik = 'KKO';
        } elseif($request->nama_jenis == 'Keripik Keladi Pedas Manis'){
            $kodeKeripik = 'KKPM';
        } elseif($request->nama_jenis == 'Keripik Keladi Asin Gurih'){
            $kodeKeripik = 'KKAG';
        } 
        $stokKeripik = StokeKeripik::all();

        if(!$stokKeripik->contains('kode_keripik', $kodeKeripik)){
            StokeKeripik::create([
                'jenis_keripik_id' => $jenisKeripik->id,
                'jumlah_stok' => $stokAwal,
                'jumlah_masuk' => $stokAwal,
                'jumlah_keluar' => 0,
                'kode_keripik' => $kodeKeripik,
                'tanggal_update' => now(),
            ]);

            return redirect()->route('jenis-keripik.index')
            ->with('success', 'Jenis keripik berhasil ditambahkan!');
        }else if ($stokKeripik->contains('kode_keripik', $kodeKeripik)) {
            // Ambil item dari collection yang memiliki kode_keripik tersebut
            $stokItem = $stokKeripik->firstWhere('kode_keripik', $kodeKeripik);
            
            // Sekarang update data
            $stokSebelum = $stokItem->jumlah_stok;
            $stokItem->jumlah_stok += $stokAwal;
            $stokItem->jumlah_masuk = $stokAwal;
            $stokItem->tanggal_update = now();
            $stokItem->save();

            return redirect()->route('jenis-keripik.index')
            ->with('success', 'Jenis keripik berhasil ditambahkan dan stok diperbarui!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisKeripik $jenisKeripik)
    {
        return view('jenis-keripik.show', compact('jenisKeripik'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisKeripik $jenisKeripik)
    {
        return view('jenis-keripik.edit', compact('jenisKeripik'));
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'berat' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 1. Cari data jenis keripik
        $jenisKeripik = JenisKeripik::findOrFail($id);
        
        // 2. Siapkan data untuk update
        $data = $request->all();
        
        // 3. Handle upload gambar
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($jenisKeripik->gambar) {
                Storage::disk('public')->delete($jenisKeripik->gambar);
            }
            
            $gambarPath = $request->file('gambar')->store('jenis_keripik/gambar', 'public');
            $data['gambar'] = $gambarPath;
        }

        // 4. Simpan nama lama untuk pengecekan
        $namaLama = $jenisKeripik->nama_jenis;
        
        // 5. Update data jenis keripik
        $jenisKeripik->update($data);

        // 6. Jika nama berubah, update kode di stok
        if ($namaLama != $request->nama_jenis) {
            $kodeKeripik = '';
            if($request->nama_jenis == 'Keripik Keladi Original'){
                $kodeKeripik = 'KKO';
            } elseif($request->nama_jenis == 'Keripik Keladi Pedas Manis'){
                $kodeKeripik = 'KKPM';
            } elseif($request->nama_jenis == 'Keripik Keladi Asin Gurih'){
                $kodeKeripik = 'KKAG';
            }
            
            StokeKeripik::where('jenis_keripik_id', $jenisKeripik->id)
                ->update(['kode_keripik' => $kodeKeripik]);
        }

        return redirect()->route('jenis-keripik.index')
                    ->with('success', 'Jenis keripik berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisKeripik $jenisKeripik)
    {
        // Hapus file gambar
        if ($jenisKeripik->gambar) {
            if (Storage::disk('public')->exists($jenisKeripik->gambar)) {
                Storage::disk('public')->delete($jenisKeripik->gambar);
            }
        }

        $jenisKeripik->delete();

        return redirect()->route('jenis-keripik.index')
            ->with('success', 'Jenis keripik berhasil dihapus!');
    }
}