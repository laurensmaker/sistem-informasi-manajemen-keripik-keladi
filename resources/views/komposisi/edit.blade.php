@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Edit Komposisi Keripik</h3>
    <a href="{{ route('komposisi.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('komposisi.update', $komposisi->id) }}" method="POST">
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
                            <option value="{{ $item->id }}" {{ old('jenis_keripik_id', $komposisi->jenis_keripik_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_keripik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="bahan_baku_id" class="form-label">Bahan Baku <span class="text-danger">*</span></label>
                    <select name="bahan_baku_id" 
                            id="bahan_baku_id" 
                            class="form-select @error('bahan_baku_id') is-invalid @enderror" 
                            required>
                        <option value="">Pilih Bahan Baku</option>
                        @foreach($bahanBaku as $item)
                            <option value="{{ $item->id }}" 
                                    data-satuan="{{ $item->satuan }}"
                                    data-harga="{{ $item->harga_satuan }}"
                                    {{ old('bahan_baku_id', $komposisi->bahan_baku_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_bahan }} ({{ $item->satuan }}) - Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('bahan_baku_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="jumlah_dibutuhkan" class="form-label">Jumlah Dibutuhkan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="jumlah_dibutuhkan" 
                               id="jumlah_dibutuhkan" 
                               class="form-control @error('jumlah_dibutuhkan') is-invalid @enderror" 
                               value="{{ old('jumlah_dibutuhkan', $komposisi->jumlah_dibutuhkan) }}"
                               placeholder="0" 
                               min="0.01"
                               step="0.01"
                               required>
                        <span class="input-group-text" id="satuan_label">{{ $komposisi->bahanBaku->satuan ?? '-' }}</span>
                    </div>
                    @error('jumlah_dibutuhkan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label class="form-label">Total Biaya</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" 
                               id="total_biaya" 
                               class="form-control" 
                               value="{{ number_format(($komposisi->bahanBaku->harga_satuan ?? 0) * $komposisi->jumlah_dibutuhkan, 0, ',', '.') }}" 
                               readonly>
                    </div>
                    <small class="text-muted">Total biaya = Jumlah dibutuhkan × Harga satuan bahan</small>
                </div>

                <div class="col-lg-12 mb-3">
                    <div class="alert alert-info">
                        <i data-feather="info"></i>
                        <strong>Catatan:</strong>
                        <ul class="mb-0">
                            <li>Satu komposisi hanya boleh 1 jenis keripik dan 1 bahan baku</li>
                            <li>Kombinasi jenis keripik dan bahan baku tidak boleh duplikat</li>
                            <li>Total biaya akan dihitung otomatis</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Komposisi
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
    // Update satuan dan hitung total biaya saat bahan baku dipilih
    document.getElementById('bahan_baku_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const satuan = selectedOption.dataset.satuan || '-';
        const harga = parseFloat(selectedOption.dataset.harga) || 0;
        
        document.getElementById('satuan_label').textContent = satuan;
        hitungTotalBiaya(harga);
    });

    // Hitung total biaya saat jumlah diubah
    document.getElementById('jumlah_dibutuhkan').addEventListener('input', function() {
        const selectedOption = document.getElementById('bahan_baku_id').options[document.getElementById('bahan_baku_id').selectedIndex];
        const harga = parseFloat(selectedOption.dataset.harga) || 0;
        hitungTotalBiaya(harga);
    });

    function hitungTotalBiaya(harga) {
        const jumlah = parseFloat(document.getElementById('jumlah_dibutuhkan').value) || 0;
        const total = jumlah * harga;
        document.getElementById('total_biaya').value = total.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    // Trigger perubahan awal
    document.getElementById('bahan_baku_id').dispatchEvent(new Event('change'));
</script>
@endpush