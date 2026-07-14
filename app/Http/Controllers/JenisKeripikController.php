<?php

namespace App\Http\Controllers;

use App\Models\JenisKeripik;
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
            'nama_jenis' => 'required|string|max:50|unique:jenis_keripik,nama_jenis',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
           $fotoSurveiPath = $request->file('gambar')->store('jenis_keripik/gambar', 'public');
            
            // Simpan path ke database (tanpa 'public/')
            $data['gambar'] = $fotoSurveiPath;
        }

        JenisKeripik::create($data);

        return redirect()->route('jenis-keripik.index')
            ->with('success', 'Jenis keripik berhasil ditambahkan!');
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
    public function update(Request $request, JenisKeripik $jenisKeripik)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:50|unique:jenis_keripik,nama_jenis,' . $jenisKeripik->id,
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Upload gambar baru jika ada
        if ($request->hasFile('gambar')) {
            // 1. HAPUS FILE LAMA
            if ($jenisKeripik->gambar) {
                // Cek apakah file lama masih ada di storage
                if (Storage::disk('public')->exists($jenisKeripik->gambar)) {
                    // Hapus file lama
                    Storage::disk('public')->delete($jenisKeripik->gambar);
                }
            }

            // 2. UPLOAD FILE BARU
            $fotoSurveiPath = $request->file('gambar')->store('jenis_keripik/gambar', 'public');
            
            // 3. SIMPAN PATH BARU KE DATABASE
            $data['gambar'] = $fotoSurveiPath;
        }

        $jenisKeripik->update($data);

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