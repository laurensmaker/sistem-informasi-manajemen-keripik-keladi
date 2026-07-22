{{-- resources/views/bahan-baku/create.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Tambah Bahan Baku</h3>
    <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('bahan-baku.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label for="nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                    <select name="nama_bahan" 
                            id="nama_bahan" 
                            class="form-select @error('nama_bahan') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        <option value="Keladi" {{ old('nama_bahan') == 'Keladi' ? 'selected' : '' }}>Keladi </option>
                        <option value="Minyak Goreng" {{ old('nama_bahan') == 'Minyak Goreng' ? 'selected' : '' }}>Minyak Goreng </option>
                        <option value="Gula Halus" {{ old('nama_bahan') == 'Gula Halus' ? 'selected' : '' }}>Gula Halus </option>
                        <option value="Garam" {{ old('nama_bahan') == 'Garam' ? 'selected' : '' }}>Garam </option>
                        {{-- <option value="Bumbu" {{ old('nama_bahan') == 'Bumbu' ? 'selected' : '' }}>Bumbu (30 kg/bulan)</option> --}}
                        <option value="Minyak Wijen" {{ old('nama_bahan') == 'Minyak Wijen' ? 'selected' : '' }}>Minyak Wijen </option>
                        <option value="Minyak Tanah" {{ old('nama_bahan') == 'Minyak Tanah' ? 'selected' : '' }}>Minyak Tanah </option>
                        <option value="Royco" {{ old('nama_bahan') == 'Royco' ? 'selected' : '' }}>Royco </option>
                        <option value="Micin" {{ old('nama_bahan') == 'Micin' ? 'selected' : '' }}>Micin </option>
                        <option value="Plastik Kemasan" {{ old('nama_bahan') == 'Plastik Kemasan' ? 'selected' : '' }}>Plastik Kemasan </option>
                        <option value="Gas" {{ old('nama_bahan') == 'Gas' ? 'selected' : '' }}>Gas </option>
                    </select>
                    @error('nama_bahan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="satuan" 
                           id="satuan" 
                           class="form-control @error('satuan') is-invalid @enderror" 
                           value="{{ old('satuan') }}"
                           placeholder="Contoh: kg, gram, liter, pcs" 
                           required>
                    @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="harga_satuan" class="form-label">Harga Satuan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" 
                               name="harga_satuan" 
                               id="harga_satuan" 
                               class="form-control @error('harga_satuan') is-invalid @enderror" 
                               value="{{ old('harga_satuan') }}"
                               placeholder="Masukkan harga satuan" 
                               min="0"
                               step="100"
                               required>
                    </div>
                    @error('harga_satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="berat" class="form-label">Berat <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="berat" 
                               id="berat" 
                               class="form-control @error('berat') is-invalid @enderror" 
                               value="{{ old('berat') }}"
                               placeholder="Masukkan berat" 
                               min="0"
                               step="1"
                               required>
                        <span class="input-group-text">Gram</span>
                    </div>
                    @error('berat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" 
                           name="supplier" 
                           id="supplier" 
                           class="form-control @error('supplier') is-invalid @enderror" 
                           value="{{ old('supplier') }}"
                           placeholder="Masukkan nama supplier">
                    @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Field Stok Awal --}}
                <div class="col-lg-6 mb-3">
                    <label for="stok_awal" class="form-label">Stok Awal</label>
                    <div class="input-group">
                        <input type="number" 
                               name="stok_awal" 
                               id="stok_awal" 
                               class="form-control @error('stok_awal') is-invalid @enderror" 
                               value="{{ old('stok_awal') }}"
                               placeholder="Masukkan stok awal" >
                        <span class="input-group-text">Unit</span>
                    </div>
                    <small class="text-muted">Kosongkan atau isi 0 jika tidak ada stok awal</small>
                    @error('stok_awal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Simpan Bahan Baku
                    </button>
                    <button type="reset" class="btn btn-warning">
                        <i data-feather="refresh-ccw"></i> Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection