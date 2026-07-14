@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Komposisi Keripik</h3>
    <div>
        {{-- <a href="{{ route('komposisi.biaya-produksi') }}" class="btn btn-info btn-sm">
            <i data-feather="dollar-sign"></i> Biaya Produksi
        </a>
        <a href="{{ route('komposisi.laporan') }}" class="btn btn-success btn-sm">
            <i data-feather="file-text"></i> Laporan
        </a> --}}
        <a href="{{ route('komposisi.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Tambah Komposisi
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
        {{-- <form action="{{ route('komposisi.index') }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Jenis Keripik</label>
                    <select name="jenis_keripik_id" class="form-select">
                        <option value="">Semua Jenis</option>
                        @foreach($jenisKeripik as $item)
                            <option value="{{ $item->id }}" {{ request('jenis_keripik_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i data-feather="filter"></i> Filter
                    </button>
                    <a href="{{ route('komposisi.index') }}" class="btn btn-secondary w-100">
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
                        <th>Jenis Keripik</th>
                        <th>Bahan Baku</th>
                        <th>Satuan</th>
                        <th>Jumlah Dibutuhkan</th>
                        <th>Harga Satuan</th>
                        <th>Total Biaya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($komposisi as $key => $item)
                        <tr>
                            <td>{{ $komposisi->firstItem() + $key }}</td>
                            <td>{{ $item->jenisKeripik->nama_jenis ?? '-' }}</td>
                            <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
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
                                <a href="{{ route('komposisi.show', $item->id) }}" class="btn btn-info btn-sm">
                                    <i data-feather="eye"></i>
                                </a>
                                <a href="{{ route('komposisi.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                    <i data-feather="edit-2"></i>
                                </a>
                                <form action="{{ route('komposisi.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus komposisi ini?')">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data komposisi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $komposisi->links() }}
        </div>
    </div>
</div>

@endsection