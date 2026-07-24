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
        $jenisK = $request->validate([
            'nama_jenis' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'berat' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'stok_awal' => 'nullable',
        ]);

         \DB::beginTransaction();
        try {
            // 1. Siapkan data
            $data = $request->except('gambar');
            
            // 2. Handle upload gambar
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('jenis_keripik', $fileName, 'public');
                $data['gambar'] = $filePath;
            }

            // 3. Simpan jenis keripik
            $jenisKeripik = JenisKeripik::create($data);

            \DB::commit();

            return redirect()->route('jenis-keripik.index')
                ->with('success', 'Jenis keripik berhasil ditambahkan!');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan jenis keripik: ' . $e->getMessage())
                ->withInput();
        }
        // return redirect()->route('jenis-keripik.index')
        //             ->with('success', 'Jenis keripik berhasil ditambahkan!');
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