@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Stok Bahan Baku</h3>
    <div>
        <a href="{{ route('stok-bahan-baku.dashboard') }}" class="btn btn-info btn-sm">
            <i data-feather="bar-chart-2"></i> Dashboard
        </a>
        <a href="{{ route('stok-bahan-baku.laporan') }}" class="btn btn-success btn-sm">
            <i data-feather="file-text"></i> Laporan
        </a>
        <a href="{{ route('stok-bahan-baku.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Tambah Stok
        </a>
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
        {{-- <form action="{{ route('stok-bahan-baku.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Bahan Baku</label>
                    <select name="bahan_baku_id" class="form-select">
                        <option value="">Semua Bahan</option>
                        @foreach($bahanBaku as $item)
                            <option value="{{ $item->id }}" {{ request('bahan_baku_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_bahan }}
                            </option>
                        @endforeach
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
                <div class="col-md-2">
                    <label class="form-label">Stok Kritis</label>
                    <input type="number" name="stok_kritis" class="form-control" value="{{ request('stok_kritis') }}" placeholder="Minimal stok">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i data-feather="filter"></i> Filter
                    </button>
                    <a href="{{ route('stok-bahan-baku.index') }}" class="btn btn-secondary w-100">
                        <i data-feather="refresh-ccw"></i> Reset
                    </a>
                </div>
            </div>
        </form> --}}

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bahan Baku</th>
                        <th>Satuan</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Masuk</th>
                        <th>Stok Keluar</th>
                        <th>Tanggal Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokBahanBaku as $key => $item)
                        <tr>
                            <td>{{ $stokBahanBaku->firstItem() + $key }}</td>
                            <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $item->bahanBaku->satuan ?? '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $stok = $item->jumlah_stok;
                                    $warna = $stok > 10 ? 'success' : ($stok > 5 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $warna }}" style="font-size: 14px;">
                                    {{ number_format($stok, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ number_format($item->jumlah_masuk, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ number_format($item->jumlah_keluar, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>{{ $item->tanggal_update->format('d/m/Y H:i') }}</td>
                            <td>
                                {{-- <a href="{{ route('stok-bahan-baku.show', $item->id) }}" class="btn btn-info btn-sm">
                                    <i data-feather="eye"></i>
                                </a> --}}
                                <a href="{{ route('stok-bahan-baku.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i data-feather="edit-2"></i>
                                </a>
                                <form action="{{ route('stok-bahan-baku.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data stok ini?')">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data stok bahan baku</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $stokBahanBaku->links() }}
        </div>
    </div>
</div>

@endsection