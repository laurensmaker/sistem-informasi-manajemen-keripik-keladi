{{-- resources/views/laporan/penjualan.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Laporan Penjualan</h3>
    <div>
        <form action="{{ route('laporan.penjualan.download') }}" method="POST" target="_blank" class="d-inline" id="formDownloadAll">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
                <i data-feather="download"></i> Download Semua
            </button>
        </form>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Form Filter --}}
<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3"><i data-feather="filter"></i> Filter Laporan</h5>
        <form action="{{ route('laporan.penjualan.filter') }}" method="GET" id="formFilter">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" id="dari_tanggal" class="form-control" 
                           value="{{ request('dari_tanggal') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" id="sampai_tanggal" class="form-control" 
                           value="{{ request('sampai_tanggal') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="filter"></i> Tampilkan
                        </button>
                        <a href="{{ route('laporan.penjualan') }}" class="btn btn-secondary">
                            <i data-feather="refresh-ccw"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i data-feather="info"></i>
                        <strong>Catatan:</strong> 
                        <ul class="mb-0">
                            <li>Kosongkan tanggal untuk menampilkan <strong>SEMUA</strong> data penjualan</li>
                            <li>Isi tanggal untuk filter berdasarkan periode tertentu</li>
                            <li>Download semua data penjualan tanpa filter</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Penjualan --}}
@if(isset($penjualan) && $penjualan->count() > 0)
<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold">
                <i data-feather="shopping-cart"></i> Data Penjualan
                <span class="badge bg-secondary ms-2">{{ $penjualan->total() }} Transaksi</span>
                @if(request('dari_tanggal') && request('sampai_tanggal'))
                    <span class="badge bg-info ms-2">
                        {{ date('d/m/Y', strtotime(request('dari_tanggal'))) }} - {{ date('d/m/Y', strtotime(request('sampai_tanggal'))) }}
                    </span>
                @else
                    <span class="badge bg-success ms-2">Semua Data</span>
                @endif
            </h5>
            <div>
                @if(request('dari_tanggal') && request('sampai_tanggal'))
                    <form action="{{ route('laporan.penjualan.download') }}" method="POST" target="_blank" class="d-inline" id="formDownloadFilter">
                        @csrf
                        <input type="hidden" name="dari_tanggal" id="download_dari_tanggal" value="{{ request('dari_tanggal', date('Y-m-01')) }}">
                        <input type="hidden" name="sampai_tanggal" id="download_sampai_tanggal" value="{{ request('sampai_tanggal', date('Y-m-d')) }}">
                        <input type="hidden" name="status" id="download_status" value="{{ request('status', '') }}">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i data-feather="download"></i> Download Hasil Filter
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total Transaksi</h6>
                        <h3>{{ $penjualan->total() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>Total Pendapatan</h6>
                        <h3>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h6>Total Item Terjual</h6>
                        <h3>{{ number_format($totalItems) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h6>Rata-rata Transaksi</h6>
                        <h3>Rp {{ number_format($penjualan->avg('total_harga') ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Data --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tablePenjualan">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>No HP</th>
                        <th>Jumlah Item</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        {{-- <th>Kasir</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $key => $item)
                    <tr>
                        <td>{{ $penjualan->firstItem() + $key }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->no_transaksi }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $item->nama_pembeli }}</strong>
                        </td>
                        <td>{{ $item->no_hp_pembeli ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $item->details->sum('jumlah') }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">
                                Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = $item->status == 'selesai' ? 'success' : ($item->status == 'pending' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        {{-- <td>{{ $item->user->name ?? '-' }}</td> --}}
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i data-feather="inbox" class="text-muted" style="width: 40px; height: 40px;"></i>
                            <p class="mt-2 text-muted">Tidak ada data penjualan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">Total Keseluruhan</td>
                        <td class="text-center">{{ number_format($totalItems) }}</td>
                        <td>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $penjualan->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@elseif(isset($penjualan))
<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4 text-center py-5">
        <i data-feather="inbox" class="text-muted" style="width: 48px; height: 48px;"></i>
        <h5 class="mt-3 text-muted">Belum ada data penjualan</h5>
        <p class="text-muted">Silakan tambahkan transaksi penjualan terlebih dahulu</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Download semua data
        const btnDownloadAll = document.getElementById('btnDownloadAll');
        if (btnDownloadAll) {
            btnDownloadAll.addEventListener('click', function(e) {
                e.preventDefault();
                window.open(this.href, '_blank');
            });
        }

        // Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush