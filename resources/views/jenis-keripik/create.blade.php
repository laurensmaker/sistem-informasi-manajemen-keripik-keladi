@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Tambah Jenis Keripik</h3>
    <a href="{{ route('jenis-keripik.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('jenis-keripik.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
               <div class="col-lg-6 mb-3">
                    <label for="nama_jenis" class="form-label">Nama Jenis Keripik <span class="text-danger">*</span></label>
                    <select name="nama_jenis" 
                            id="nama_jenis" 
                            class="form-select @error('nama_jenis') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Jenis Keripik --</option>
                        <option value="Keripik Keladi Original" {{ old('nama_jenis') == 'Keripik Keladi Original' ? 'selected' : '' }}>
                            Keripik Keladi Original
                        </option>
                        <option value="Keripik Keladi Pedas Manis" {{ old('nama_jenis') == 'Keripik Keladi Pedas Manis' ? 'selected' : '' }}>
                            Keripik Keladi Pedas Manis
                        </option>
                        <option value="Keripik Keladi Asin Gurih" {{ old('nama_jenis') == 'Keripik Keladi Asin Gurih' ? 'selected' : '' }}>
                            Keripik Keladi Asin Gurih
                        </option>
                    </select>
                    @error('nama_jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="satuan" 
                           id="satuan" 
                           class="form-control @error('satuan') is-invalid @enderror" 
                           value="{{ old('satuan', 'pcs') }}"
                           placeholder="Contoh: pcs, kg, gram" 
                           required>
                    @error('satuan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="harga_jual" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="harga_jual" 
                           id="harga_jual" 
                           class="form-control @error('harga_jual') is-invalid @enderror" 
                           value="{{ old('harga_jual') }}"
                           placeholder="Masukkan harga jual" 
                           min="0"
                           step="1000"
                           required>
                    @error('harga_jual')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="gambar" class="form-label">Gambar</label>
                    <input type="file" 
                           name="gambar" 
                           id="gambar" 
                           class="form-control @error('gambar') is-invalid @enderror"
                           accept="image/*">
                    <small class="text-muted">Format: jpeg, png, jpg, gif | Max: 2MB</small>
                    @error('gambar')
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

                <div class="col-lg-12 mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" 
                              id="deskripsi" 
                              class="form-control @error('deskripsi') is-invalid @enderror" 
                              rows="4"
                              placeholder="Masukkan deskripsi jenis keripik">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

              

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Simpan Jenis Keripik
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