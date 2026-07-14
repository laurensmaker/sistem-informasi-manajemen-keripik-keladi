@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Edit Bahan Baku</h3>
    <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('bahan-baku.update', $bahanBaku->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label for="nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="nama_bahan" 
                           id="nama_bahan" 
                           class="form-control @error('nama_bahan') is-invalid @enderror" 
                           value="{{ old('nama_bahan', $bahanBaku->nama_bahan) }}"
                           placeholder="Masukkan nama bahan baku" 
                           required>
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
                           value="{{ old('satuan', $bahanBaku->satuan) }}"
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
                               value="{{ old('harga_satuan', $bahanBaku->harga_satuan) }}"
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
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" 
                           name="supplier" 
                           id="supplier" 
                           class="form-control @error('supplier') is-invalid @enderror" 
                           value="{{ old('supplier', $bahanBaku->supplier) }}"
                           placeholder="Masukkan nama supplier">
                    @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Bahan Baku
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