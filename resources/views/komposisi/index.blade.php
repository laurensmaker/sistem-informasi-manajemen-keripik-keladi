{{-- resources/views/produksi/index.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Produksi Keripik</h3>
    <a href="{{ route('komposisi.create') }}" class="btn btn-primary btn-sm">
        <i data-feather="plus"></i> Produksi Baru
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        @if($jenisKeripik->count() > 0)
            <div class="accordion" id="accordionProduksi">
                @foreach($jenisKeripik as $index => $jenis)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse{{ $index }}" 
                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                aria-controls="collapse{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <span>
                                    <i data-feather="package" class="me-2"></i>
                                    <strong>{{ $jenis->nama_jenis }}</strong>
                                    <span class="badge bg-info ms-2">
                                        {{ $jenis->berat }} Gram
                                    </span>
                                </span>
                                <span class="badge bg-secondary me-3">
                                    {{ $jenis->komposisi->count() }} Bahan
                                </span>
                                <span class="badge bg-primary me-3">
                                    Stok: {{ number_format($jenis->stok->jumlah_stok ?? 0) }} Pcs
                                </span>
                                <span class="badge bg-success">
                                    Rp {{ number_format($jenis->komposisi->sum(function($item) {
                                        return ($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan;
                                    }), 0, ',', '.') }}
                                </span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" 
                         class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                         aria-labelledby="heading{{ $index }}" 
                         data-bs-parent="#accordionProduksi">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Nama Bahan</th>
                                            <th>Satuan</th>
                                            <th>Jumlah digunakan</th>
                                            <th>Harga Satuan</th>
                                            <th>Total Biaya</th>
                                            <th width="80">Stok Bahan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($jenis->komposisi as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <strong>{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong>
                                                @if($item->bahanBaku && $item->bahanBaku->supplier)
                                                    <br>
                                                    <small class="text-muted">Supplier: {{ $item->bahanBaku->supplier }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $item->bahanBaku->satuan ?? '-' }}</span>
                                            </td>
                                            <td>{{ number_format($item->jumlah_dibutuhkan, 0, ',', '.') }}</td>
                                            <td>
                                                Rp {{ number_format($item->bahanBaku->harga_satuan ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    Rp {{ number_format(($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $stokTersedia = $item->bahanBaku->stok->jumlah_stok ?? 0;
                                                    $status = $stokTersedia >= $item->jumlah_dibutuhkan ? 'success' : 'danger';
                                                @endphp
                                                <span class="badge bg-{{ $status }}">
                                                    {{ number_format($stokTersedia, 0) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i data-feather="inbox"></i> 
                                                Belum ada komposisi untuk jenis keripik ini
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="5" class="text-end fw-bold">Total Biaya Keseluruhan:</td>
                                            <td colspan="2">
                                                <span class="badge bg-success fs-6">
                                                    Rp {{ number_format($jenis->komposisi->sum(function($item) {
                                                        return ($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan;
                                                    }), 0, ',', '.') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="inbox" class="text-muted" style="width: 48px; height: 48px;"></i>
                <h5 class="mt-3 text-muted">Belum ada data produksi</h5>
                <p class="text-muted">Silakan tambahkan komposisi bahan baku terlebih dahulu</p>
                <a href="{{ route('komposisi.create') }}" class="btn btn-primary">
                    <i data-feather="plus"></i> Tambah Komposisi
                </a>
            </div>
        @endif
    </div>
</div>

@endsection