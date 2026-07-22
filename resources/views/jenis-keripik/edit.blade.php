{{-- resources/views/jenis-keripik/edit.blade.php --}}
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
                    <label for="nama_jenis" class="form-label">Nama Jenis Keripik <span class="text-danger">*</span></label>
                    <select name="nama_jenis" 
                            id="nama_jenis" 
                            class="form-select @error('nama_jenis') is-invalid @enderror" 
                            required>
                        <option value="">-- Pilih Jenis Keripik --</option>
                        <option value="Keripik Keladi Original" {{ old('nama_jenis', $jenisKeripik->nama_jenis) == 'Keripik Keladi Original' ? 'selected' : '' }}>
                            Keripik Keladi Original
                        </option>
                        <option value="Keripik Keladi Pedas Manis" {{ old('nama_jenis', $jenisKeripik->nama_jenis) == 'Keripik Keladi Pedas Manis' ? 'selected' : '' }}>
                            Keripik Keladi Pedas Manis
                        </option>
                        <option value="Keripik Keladi Asin Gurih" {{ old('nama_jenis', $jenisKeripik->nama_jenis) == 'Keripik Keladi Asin Gurih' ? 'selected' : '' }}>
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
                                 class="img-thumbnail" 
                                 style="max-height: 100px;">
                            <br>
                            <small class="text-muted">Gambar saat ini</small>
                        </div>
                    @endif
                    <input type="file" 
                           name="gambar" 
                           id="gambar" 
                           class="form-control @error('gambar') is-invalid @enderror"
                           accept="image/*">
                    <small class="text-muted">Format: jpeg, png, jpg, gif | Max: 2MB. Kosongkan jika tidak ingin mengubah gambar</small>
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
                               value="{{ old('berat', $jenisKeripik->berat) }}"
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

                {{-- Informasi Stok Saat Ini (Readonly) --}}
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Stok Saat Ini</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               value="{{ number_format($jenisKeripik->stok->jumlah_stok ?? 0, 2) }} {{ $jenisKeripik->satuan }}"
                               disabled>
                        <span class="input-group-text bg-light">
                            <i data-feather="info" class="text-info"></i>
                        </span>
                    </div>
                    <small class="text-muted">Stok tidak dapat diubah melalui form ini. Gunakan fitur tambah stok.</small>
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
                    <a href="{{ route('jenis-keripik.index') }}" class="btn btn-secondary">
                        <i data-feather="x-circle"></i> Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection