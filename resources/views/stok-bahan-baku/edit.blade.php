@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Edit Stok Bahan Baku</h3>
    <a href="{{ route('stok-bahan-baku.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('stok-bahan-baku.update', $stokBahanBaku->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
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
                                    {{ old('bahan_baku_id', $stokBahanBaku->bahan_baku_id) == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_bahan }} ({{ $item->satuan }})
                            </option>
                        @endforeach
                    </select>
                    @error('bahan_baku_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6 mb-3">
                    <label for="tanggal_update" class="form-label">Tanggal Update <span class="text-danger">*</span></label>
                    <input type="datetime-local" 
                           name="tanggal_update" 
                           id="tanggal_update" 
                           class="form-control @error('tanggal_update') is-invalid @enderror" 
                           value="{{ old('tanggal_update', $stokBahanBaku->tanggal_update->format('Y-m-d\TH:i')) }}"
                           required>
                    @error('tanggal_update')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-4 mb-3">
                    <label for="jumlah_stok" class="form-label">Stok Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="jumlah_stok" 
                               id="jumlah_stok" 
                               class="form-control @error('jumlah_stok') is-invalid @enderror" 
                               value="{{ old('jumlah_stok', $stokBahanBaku->jumlah_stok) }}"
                               placeholder="0" 
                               min="0"
                               step="0.01"
                               required>
                        <span class="input-group-text" id="satuan_stok">{{ $stokBahanBaku->bahanBaku->satuan ?? '-' }}</span>
                    </div>
                    @error('jumlah_stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-4 mb-3">
                    <label for="jumlah_masuk" class="form-label">Stok Masuk <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="jumlah_masuk" 
                               id="jumlah_masuk" 
                               class="form-control @error('jumlah_masuk') is-invalid @enderror" 
                               value="{{ old('jumlah_masuk', $stokBahanBaku->jumlah_masuk) }}"
                               placeholder="0" 
                               min="0"
                               step="0.01"
                               required>
                        <span class="input-group-text" id="satuan_masuk">{{ $stokBahanBaku->bahanBaku->satuan ?? '-' }}</span>
                    </div>
                    @error('jumlah_masuk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-4 mb-3">
                    <label for="jumlah_keluar" class="form-label">Stok Keluar <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" 
                               name="jumlah_keluar" 
                               id="jumlah_keluar" 
                               class="form-control @error('jumlah_keluar') is-invalid @enderror" 
                               value="{{ old('jumlah_keluar', $stokBahanBaku->jumlah_keluar) }}"
                               placeholder="0" 
                               min="0"
                               step="0.01"
                               required>
                        <span class="input-group-text" id="satuan_keluar">{{ $stokBahanBaku->bahanBaku->satuan ?? '-' }}</span>
                    </div>
                    @error('jumlah_keluar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-12 mb-3">
                    <div class="alert alert-info">
                        <i data-feather="info"></i>
                        <strong>Catatan:</strong> 
                        <ul class="mb-0">
                            <li>Stok akhir = Stok Saat Ini + Stok Masuk - Stok Keluar</li>
                            <li>Stok akhir tidak boleh bernilai negatif</li>
                            <li>Satuan mengikuti satuan bahan baku yang dipilih</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="alert alert-success" id="hasilStok">
                        <strong>Perhitungan Stok Akhir:</strong> 
                        <span id="stok_akhir">{{ $stokBahanBaku->jumlah_stok + $stokBahanBaku->jumlah_masuk - $stokBahanBaku->jumlah_keluar }}</span>
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

@push('scripts')
<script>
    // Update satuan saat bahan baku dipilih
    document.getElementById('bahan_baku_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const satuan = selectedOption.text.split('(')[1]?.replace(')', '') || '-';
        
        document.getElementById('satuan_stok').textContent = satuan;
        document.getElementById('satuan_masuk').textContent = satuan;
        document.getElementById('satuan_keluar').textContent = satuan;
    });

    // Hitung stok akhir
    function hitungStokAkhir() {
        const stok = parseFloat(document.getElementById('jumlah_stok').value) || 0;
        const masuk = parseFloat(document.getElementById('jumlah_masuk').value) || 0;
        const keluar = parseFloat(document.getElementById('jumlah_keluar').value) || 0;
        const stokAkhir = stok + masuk - keluar;
        
        document.getElementById('stok_akhir').textContent = stokAkhir.toFixed(2);
        
        const hasilStok = document.getElementById('hasilStok');
        if (stokAkhir < 0) {
            hasilStok.className = 'alert alert-danger';
            hasilStok.innerHTML = '<strong>Error:</strong> Stok akhir tidak boleh negatif!';
        } else {
            hasilStok.className = 'alert alert-success';
            hasilStok.innerHTML = '<strong>Perhitungan Stok Akhir:</strong> ' + stokAkhir.toFixed(2);
        }
    }

    document.getElementById('jumlah_stok').addEventListener('input', hitungStokAkhir);
    document.getElementById('jumlah_masuk').addEventListener('input', hitungStokAkhir);
    document.getElementById('jumlah_keluar').addEventListener('input', hitungStokAkhir);
</script>
@endpush