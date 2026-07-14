@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Edit Stok Keripik</h3>
    <a href="{{ route('stok-keripik.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('stok-keripik.update', $stokKeripik->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label for="jenis_keripik_id" class="form-label">Jenis Keripik <span class="text-danger">*</span></label>
                    <select name="jenis_keripik_id" 
                            id="jenis_keripik_id" 
                            class="form-select @error('jenis_keripik_id') is-invalid @enderror" 
                            required>
                        <option value="">Pilih Jenis Keripik</option>
                        @foreach($jenisKeripik as $item)
                            <option value="{{ $item->id }}" {{ old('jenis_keripik_id', $stokKeripik->jenis_keripik_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_keripik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- <div class="col-lg-6 mb-3">
                    <label for="user_id" class="form-label">Petugas <span class="text-danger">*</span></label>
                    <select name="user_id" 
                            id="user_id" 
                            class="form-select @error('user_id') is-invalid @enderror" 
                            required>
                        <option value="">Pilih Petugas</option>
                        @foreach($users as $item)
                            <option value="{{ $item->id }}" {{ old('user_id', $stokKeripik->user_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="col-lg-6 mb-3">
                    <label for="jumlah_stok" class="form-label">Stok Saat Ini <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="jumlah_stok" 
                           id="jumlah_stok" 
                           class="form-control @error('jumlah_stok') is-invalid @enderror" 
                           value="{{ old('jumlah_stok', $stokKeripik->jumlah_stok) }}"
                           placeholder="Masukkan jumlah stok saat ini" 
                           min="0"
                           required>
                    @error('jumlah_stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="jumlah_masuk" class="form-label">Stok Masuk <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="jumlah_masuk" 
                           id="jumlah_masuk" 
                           class="form-control @error('jumlah_masuk') is-invalid @enderror" 
                           value="{{ old('jumlah_masuk', $stokKeripik->jumlah_masuk) }}"
                           placeholder="Masukkan jumlah stok masuk" 
                           min="0"
                           required>
                    @error('jumlah_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="jumlah_keluar" class="form-label">Stok Keluar <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="jumlah_keluar" 
                           id="jumlah_keluar" 
                           class="form-control @error('jumlah_keluar') is-invalid @enderror" 
                           value="{{ old('jumlah_keluar', $stokKeripik->jumlah_keluar) }}"
                           placeholder="Masukkan jumlah stok keluar" 
                           min="0"
                           required>
                    @error('jumlah_keluar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="tanggal_update" class="form-label">Tanggal Update <span class="text-danger">*</span></label>
                    <input type="datetime-local" 
                           name="tanggal_update" 
                           id="tanggal_update" 
                           class="form-control @error('tanggal_update') is-invalid @enderror" 
                           value="{{ old('tanggal_update', $stokKeripik->tanggal_update->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('tanggal_update')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mt-3">
                    <div class="alert alert-info">
                        <i data-feather="info"></i>
                        <strong>Catatan:</strong> Stok akhir akan dihitung otomatis = Stok Saat Ini + Stok Masuk - Stok Keluar
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Stok
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