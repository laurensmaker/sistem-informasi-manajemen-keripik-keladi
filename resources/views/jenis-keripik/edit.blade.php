@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Edit Jenis Keripik</h3>
    <a href="{{ route('jenis-keripik.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('jenis-keripik.update', $jenisKeripik->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label for="nama_jenis" class="form-label">Nama Jenis <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="nama_jenis" 
                           id="nama_jenis" 
                           class="form-control @error('nama_jenis') is-invalid @enderror" 
                           value="{{ old('nama_jenis', $jenisKeripik->nama_jenis) }}"
                           placeholder="Masukkan nama jenis keripik" 
                           required>
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
                           value="{{ old('satuan', $jenisKeripik->satuan) }}"
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
                           value="{{ old('harga_jual', $jenisKeripik->harga_jual) }}"
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
                    @if($jenisKeripik->gambar)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $jenisKeripik->gambar) }}" 
                                alt="{{ $jenisKeripik->nama_jenis }}" 
                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                            <br>
                            <small class="text-muted">Gambar saat ini</small>
                        </div>
                    @endif
                    <input type="file" 
                        name="gambar" 
                        id="gambar" 
                        class="form-control @error('gambar') is-invalid @enderror"
                        accept="image/*">
                    <small class="text-muted">Format: jpeg, png, jpg, gif | Max: 2MB | Kosongkan jika tidak ingin mengganti gambar</small>
                    @error('gambar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" 
                              id="deskripsi" 
                              class="form-control @error('deskripsi') is-invalid @enderror" 
                              rows="4"
                              placeholder="Masukkan deskripsi jenis keripik">{{ old('deskripsi', $jenisKeripik->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Jenis Keripik
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