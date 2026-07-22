{{-- resources/views/bahan-baku/edit.blade.php --}}
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
                    <select name="nama_bahan" 
                            id="nama_bahan" 
                            class="form-select @error('nama_bahan') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        <option value="Keladi" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Keladi' ? 'selected' : '' }}>Keladi</option>
                        <option value="Minyak Goreng" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Minyak Goreng' ? 'selected' : '' }}>Minyak Goreng</option>
                        <option value="Gula Halus" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Gula Halus' ? 'selected' : '' }}>Gula Halus</option>
                        <option value="Garam" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Garam' ? 'selected' : '' }}>Garam</option>
                        <option value="Minyak Wijen" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Minyak Wijen' ? 'selected' : '' }}>Minyak Wijen</option>
                        <option value="Minyak Tanah" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Minyak Tanah' ? 'selected' : '' }}>Minyak Tanah</option>
                        <option value="Royco" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Royco' ? 'selected' : '' }}>Royco</option>
                        <option value="Micin" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Micin' ? 'selected' : '' }}>Micin</option>
                        <option value="Plastik Kemasan" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Plastik Kemasan' ? 'selected' : '' }}>Plastik Kemasan</option>
                        <option value="Gas" {{ old('nama_bahan', $bahanBaku->nama_bahan) == 'Gas' ? 'selected' : '' }}>Gas</option>
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
                    <label for="berat" class="form-label">Berat <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="berat" 
                               id="berat" 
                               class="form-control @error('berat') is-invalid @enderror" 
                               value="{{ old('berat', $bahanBaku->berat) }}"
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
                           value="{{ old('supplier', $bahanBaku->supplier) }}"
                           placeholder="Masukkan nama supplier">
                    @error('supplier')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Informasi Stok Saat Ini --}}
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Stok Saat Ini</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               value="{{ number_format($bahanBaku->stok->jumlah_stok ?? 0, 2) }} {{ $bahanBaku->satuan }}"
                               disabled>
                        <span class="input-group-text bg-light">
                            <i data-feather="info" class="text-info"></i>
                        </span>
                    </div>
                    <small class="text-muted">Stok tidak dapat diubah melalui form ini. Gunakan fitur tambah stok.</small>
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Bahan Baku
                    </button>
                    <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary">
                        <i data-feather="x-circle"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection