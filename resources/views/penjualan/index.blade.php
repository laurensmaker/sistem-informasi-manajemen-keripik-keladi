@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Penjualan</h3>
    <div>
        <a href="{{ route('penjualan.laporan') }}" class="btn btn-info btn-sm">
            <i data-feather="file-text"></i> Laporan
        </a>
        <a href="{{ route('penjualan.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Transaksi Baru
        </a>
    </div>
</div>

<!-- Statistik -->
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Pendapatan</h6>
                <h4 class="fw-bold text-success">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Transaksi</h6>
                <h4 class="fw-bold">{{ $totalTransaksi }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Pesanan</h6>
                <h4 class="fw-bold text-warning">{{ $totalPesanan }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Diproses</h6>
                <h4 class="fw-bold text-info">{{ $totalProses }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter -->
        {{-- <form action="{{ route('penjualan.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pesan" {{ request('status') == 'pesan' ? 'selected' : '' }}>Pesan</option>
                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="No. Transaksi / Pembeli" value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i data-feather="filter"></i> Filter
                    </button>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-secondary w-100">
                        <i data-feather="refresh-ccw"></i>
                    </a>
                </div>
            </div>
        </form> --}}

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $key => $item)
                        <tr>
                            <td>{{ $penjualan->firstItem() + $key }}</td>
                            <td>
                                <strong>{{ $item->no_transaksi }}</strong>
                            </td>
                            <td>{{ $item->tanggal_formatted }}</td>
                            <td>
                                {{ $item->nama_pembeli }}
                                @if($item->no_hp_pembeli)
                                    <br><small class="text-muted">{{ $item->no_hp_pembeli }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ $item->total_harga_formatted }}</span>
                            </td>
                            <td>
                                <span class="{{ $item->status_badge }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                            <td>{{ $item->user->nama ?? '-' }}</td>
                            <td>
                                <a href="{{ route('penjualan.show', $item->id) }}" class="btn btn-info btn-sm">
                                    <i data-feather="eye"></i>
                                </a>
                                {{-- <a href="{{ route('penjualan.print-struk', $item->id) }}" class="btn btn-success btn-sm" target="_blank">
                                    <i data-feather="printer"></i>
                                </a> --}}
                                @if($item->status != 'selesai' && $item->status != 'batal')
                                    <a href="{{ route('penjualan.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                @endif
                                <form action="{{ route('penjualan.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penjualan ini?')">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data penjualan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $penjualan->links() }}
        </div>
    </div>
</div>

@endsection