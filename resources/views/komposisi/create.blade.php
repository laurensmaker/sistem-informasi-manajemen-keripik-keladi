{{-- resources/views/komposisi/create.blade.php --}}
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
        {{-- TAMPILKAN ERROR --}}
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('komposisi.store') }}" method="POST" id="formKomposisi">
            @csrf
            
            <div class="row">
                {{-- Jenis Keripik --}}
                <div class="col-lg-6 mb-3">
                    <label for="jenis_keripik_id" class="form-label">Jenis Keripik <span class="text-danger">*</span></label>
                    <select name="jenis_keripik_id" 
                            id="jenis_keripik_id" 
                            class="form-select @error('jenis_keripik_id') is-invalid @enderror" 
                            required>
                        <option value="">Pilih Jenis Keripik</option>
                        @foreach($jenisKeripik as $item)
                            <option value="{{ $item->id }}" {{ old('jenis_keripik_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_jenis }} - {{ $item->berat }} Gram
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_keripik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Jumlah Produksi --}}
                <div class="col-lg-6 mb-3">
                    <label for="stok_awal" class="form-label">Jumlah Produksi</label>
                    <div class="input-group">
                        <input type="number" 
                            name="stok_awal" 
                            id="stok_awal" 
                            class="form-control @error('stok_awal') is-invalid @enderror" 
                            value="{{ old('stok_awal', 0) }}"
                            placeholder="Masukkan jumlah produksi" 
                            min="0"
                            step="1">
                        <span class="input-group-text">Pcs</span>
                    </div>
                    <small class="text-muted">Isi jumlah produksi untuk menambah stok keripik</small>
                    @error('stok_awal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Bahan Baku --}}
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
                                            <small class="text-muted">
                                                Stok: {{ number_format($item->stok->jumlah_stok ?? 0, 2) }}
                                            </small>
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
                <div class="col-lg-12 mb-3" id="jumlah_container" style="{{ old('bahan_baku_id') ? 'display:block' : 'display:none' }}">
                    <label class="form-label">Jumlah Dibutuhkan per Bahan <span class="text-danger">*</span></label>
                    <div class="row" id="jumlah_fields">
                        @if(old('bahan_baku_id'))
                            @foreach(old('bahan_baku_id') as $bahanId)
                                @php
                                    $bahan = $bahanBaku->firstWhere('id', $bahanId);
                                @endphp
                                @if($bahan)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <label class="form-label fw-bold">{{ $bahan->nama_bahan }}</label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       name="jumlah[{{ $bahanId }}]" 
                                                       class="form-control jumlah-input" 
                                                       placeholder="Jumlah" 
                                                       min="0.01" 
                                                       step="0.01"
                                                       data-id="{{ $bahanId }}"
                                                       data-harga="{{ $bahan->harga_satuan }}"
                                                       value="{{ old('jumlah.'.$bahanId) }}"
                                                       required>
                                                <span class="input-group-text">{{ $bahan->satuan }}</span>
                                            </div>
                                            <small class="text-muted">Subtotal: Rp <span id="subtotal_{{ $bahanId }}">0</span></small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
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
                               value="{{ old('total_biaya', 0) }}" 
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
                            <li>Total biaya akan dihitung otomatis</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i data-feather="save"></i> Simpan Komposisi
                    </button>
                    <button type="reset" class="btn btn-warning" id="btnReset">
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
        // Ambil old values dari PHP ke JavaScript
        var oldJumlah = @json(old('jumlah', []));
        var oldBahanBaku = @json(old('bahan_baku_id', []));

        // Event listener untuk checkbox
        var checkboxes = document.querySelectorAll('.bahan-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateJumlahFields();
            });
        });

        function updateJumlahFields() {
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
                var oldValue = oldJumlah[id] || '';
                
                var div = document.createElement('div');
                div.className = 'col-lg-4 col-md-6 mb-3';
                div.innerHTML = '<div class="card"><div class="card-body">' +
                    '<label class="form-label fw-bold">' + nama + '</label>' +
                    '<div class="input-group">' +
                    '<input type="number" name="jumlah[' + id + ']" class="form-control jumlah-input" placeholder="Jumlah" min="0.01" step="0.01" data-id="' + id + '" data-harga="' + harga + '" value="' + oldValue + '" required>' +
                    '<span class="input-group-text">' + satuan + '</span>' +
                    '</div>' +
                    '<small class="text-muted">Subtotal: Rp <span id="subtotal_' + id + '">0</span></small>' +
                    '</div></div>';
                fieldsContainer.appendChild(div);
            });

            // Event listener untuk input jumlah
            document.querySelectorAll('.jumlah-input').forEach(function(input) {
                input.addEventListener('input', function() {
                    hitungSubtotal(this);
                });
                // Trigger untuk menghitung subtotal awal
                hitungSubtotal(input);
            });

            // Hitung total awal
            hitungTotal();
        }

        function hitungSubtotal(input) {
            var id = input.getAttribute('data-id');
            var harga = parseFloat(input.getAttribute('data-harga')) || 0;
            var jumlah = parseFloat(input.value) || 0;
            var subtotal = jumlah * harga;
            
            var subtotalEl = document.getElementById('subtotal_' + id);
            if (subtotalEl) {
                subtotalEl.textContent = subtotal.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            }
            
            hitungTotal();
        }

        function hitungTotal() {
            var total = 0;
            document.querySelectorAll('.jumlah-input').forEach(function(inp) {
                var h = parseFloat(inp.getAttribute('data-harga')) || 0;
                var j = parseFloat(inp.value) || 0;
                total += j * h;
            });
            document.getElementById('total_biaya').value = total.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Trigger untuk checkbox yang sudah checked saat load
        document.querySelectorAll('.bahan-checkbox:checked').forEach(function(cb) {
            cb.dispatchEvent(new Event('change'));
        });

        // Event untuk form submit
        document.getElementById('formKomposisi').addEventListener('submit', function(e) {
            var checked = document.querySelectorAll('.bahan-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 bahan baku!');
                return false;
            }

            var jumlahInputs = document.querySelectorAll('.jumlah-input');
            var valid = true;
            jumlahInputs.forEach(function(input) {
                if (parseFloat(input.value) <= 0) {
                    valid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Masukkan jumlah untuk semua bahan baku yang dipilih!');
                return false;
            }
        });

        // Reset form
        document.getElementById('btnReset').addEventListener('click', function() {
            document.getElementById('formKomposisi').reset();
            document.getElementById('jumlah_container').style.display = 'none';
            document.getElementById('total_biaya').value = '0';
            document.querySelectorAll('.jumlah-input').forEach(function(input) {
                input.value = '';
            });
            document.querySelectorAll('.subtotal-produk').forEach(function(el) {
                el.textContent = '0';
            });
        });
    });
</script>
@endpush