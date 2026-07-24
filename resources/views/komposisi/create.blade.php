@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Tambah Produksi Keripik</h3>
    <a href="{{ route('komposisi.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('komposisi.store') }}" method="POST">
            @csrf
            
            <div class="row">
                {{-- Jenis Keripik (Single Select) --}}
                <div class="col-lg-6 mb-3">
                    <label for="jenis_keripik_id" class="form-label">Jenis Keripik <span class="text-danger">*</span></label>
                    <select name="jenis_keripik_id" 
                            id="jenis_keripik_id" 
                            class="form-select @error('jenis_keripik_id') is-invalid @enderror" 
                            required>
                        <option value="">Pilih Jenis Keripik</option>
                        @foreach($jenisKeripik as $item)
                            <option value="{{ $item->id }}" {{ old('jenis_keripik_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_jenis }}-{{ $item->berat }} Gram
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_keripik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="stok_awal" class="form-label">Stok Jumlah Prduksi</label>
                    <div class="input-group">
                        <input type="number" 
                               name="stok_awal" 
                               id="stok_awal" 
                               class="form-control @error('stok_awal') is-invalid @enderror" 
                               value="{{ old('stok_awal') }}"
                               placeholder="Masukkan stok jumlah Produksi" >
                    </div>
                    @error('stok_awal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Bahan Baku (Checkbox Multiple) --}}
                <div class="col-lg-12 mb-3">
                    <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                    <div class="card border @error('bahan_baku_id') border-danger @enderror">
                        <div class="card-body">
                            <div class="row">
                                @foreach($bahanBaku as $item)
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <div class="form-check border rounded p-3 @if(in_array($item->id, old('bahan_baku_id', []))) border-primary bg-primary-light @endif">
                                        <input class="form-check-input bahan-checkbox" 
                                               type="checkbox" 
                                               name="bahan_baku_id[]" 
                                               id="bahan_{{ $item->id }}" 
                                               value="{{ $item->id }}"
                                               data-satuan="{{ $item->satuan }}"
                                               data-harga="{{ $item->harga_satuan }}"
                                               data-nama="{{ $item->nama_bahan }}"
                                               {{ in_array($item->id, old('bahan_baku_id', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bahan_{{ $item->id }}">
                                            <strong>{{ $item->nama_bahan }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Satuan: {{ $item->satuan }} | 
                                                Harga: Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                            </small>
                                            <br>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('bahan_baku_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('bahan_baku_id.*')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Pilih satu atau lebih bahan baku yang digunakan</small>
                </div>

                {{-- Jumlah Dibutuhkan per Bahan --}}
                <div class="col-lg-12 mb-3" id="jumlah_container" style="display: none;">
                    <label class="form-label">Jumlah Dibutuhkan per Bahan <span class="text-danger">*</span></label>
                    <div class="row" id="jumlah_fields">
                        <!-- Akan diisi oleh JavaScript -->
                    </div>
                </div>

                {{-- Total Biaya --}}
                <div class="col-lg-6 mb-3">
                    <label class="form-label">Total Biaya</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" 
                               id="total_biaya" 
                               class="form-control" 
                               value="0" 
                               readonly>
                    </div>
                    <small class="text-muted">Total biaya = Jumlah dibutuhkan × Harga satuan bahan</small>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class="alert alert-info">
                        <i data-feather="info"></i>
                        <strong>Catatan:</strong>
                        <ul class="mb-0">
                            <li>Satu komposisi hanya boleh 1 jenis keripik</li>
                            <li>Bahan baku bisa lebih dari 1 (pilih dengan checkbox)</li>
                            <li>Kombinasi jenis keripik dan bahan baku tidak boleh duplikat</li>
                            <li>Total biaya akan dihitung otomatis</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Simpan Komposisi
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener untuk checkbox
        var checkboxes = document.querySelectorAll('.bahan-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var checked = document.querySelectorAll('.bahan-checkbox:checked');
                var container = document.getElementById('jumlah_container');
                var fieldsContainer = document.getElementById('jumlah_fields');
                
                if (checked.length === 0) {
                    container.style.display = 'none';
                    document.getElementById('total_biaya').value = '0';
                    return;
                }

                container.style.display = 'block';
                fieldsContainer.innerHTML = '';

                checked.forEach(function(cb) {
                    var id = cb.value;
                    var nama = cb.getAttribute('data-nama');
                    var satuan = cb.getAttribute('data-satuan');
                    var harga = cb.getAttribute('data-harga');
                    
                    var div = document.createElement('div');
                    div.className = 'col-lg-4 col-md-6 mb-3';
                    div.innerHTML = '<div class="card"><div class="card-body">' +
                        '<label class="form-label fw-bold">' + nama + '</label>' +
                        '<div class="input-group">' +
                        '<input type="number" name="jumlah[' + id + ']" class="form-control jumlah-input" placeholder="Jumlah" min="0.01" step="0.01" data-id="' + id + '" data-harga="' + harga + '" required>' +
                        '<span class="input-group-text">' + satuan + '</span>' +
                        '</div>' +
                        '<small class="text-muted">Subtotal: Rp <span id="subtotal_' + id + '">0</span></small>' +
                        '</div></div>';
                    fieldsContainer.appendChild(div);
                });

                // Event listener untuk input jumlah
                document.querySelectorAll('.jumlah-input').forEach(function(input) {
                    input.addEventListener('input', function() {
                        var id = this.getAttribute('data-id');
                        var harga = parseFloat(this.getAttribute('data-harga')) || 0;
                        var jumlah = parseFloat(this.value) || 0;
                        var subtotal = jumlah * harga;
                        document.getElementById('subtotal_' + id).textContent = subtotal.toLocaleString('id-ID');
                        
                        // Hitung total
                        var total = 0;
                        document.querySelectorAll('.jumlah-input').forEach(function(inp) {
                            var h = parseFloat(inp.getAttribute('data-harga')) || 0;
                            var j = parseFloat(inp.value) || 0;
                            total += j * h;
                        });
                        document.getElementById('total_biaya').value = total.toLocaleString('id-ID');
                    });
                });
            });
        });

        // Trigger untuk checkbox yang sudah checked
        document.querySelectorAll('.bahan-checkbox:checked').forEach(function(cb) {
            cb.dispatchEvent(new Event('change'));
        });
    });
</script>
@endpush