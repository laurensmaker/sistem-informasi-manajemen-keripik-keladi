<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
            'nama_bahan' => 'required|string|max:50|unique:bahan_baku,nama_bahan',
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:50',
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('bahan-baku.index')
            ->with('success', 'Bahan baku berhasil ditambahkan!');
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
    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:50|unique:bahan_baku,nama_bahan,' . $bahanBaku->id,
            'satuan' => 'required|string|max:20',
            'harga_satuan' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:50',
        ]);

        $bahanBaku->update($request->all());

        return redirect()->route('bahan-baku.index')
            ->with('success', 'Bahan baku berhasil diupdate!');
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