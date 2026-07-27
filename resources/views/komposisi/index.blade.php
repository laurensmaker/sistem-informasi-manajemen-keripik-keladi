{{-- resources/views/komposisi/index.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Produksi Keripik</h3>
    <div>
        <a href="{{ route('komposisi.create') }}" class="btn btn-primary btn-sm">
            <i data-feather="plus"></i> Produksi Baru
        </a>
    </div>
</div>

{{-- Filter --}}
{{-- <div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('komposisi.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" class="form-control" value="{{ request('dari_tanggal') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" class="form-control" value="{{ request('sampai_tanggal') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis Keripik</label>
                <select name="jenis_keripik_id" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($jenisKeripikList as $item)
                        <option value="{{ $item->id }}" {{ request('jenis_keripik_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_jenis }} ({{ $item->berat }} Gram)
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i data-feather="filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div> --}}

{{-- Data Produksi --}}
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

        @if($komposisi->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            {{-- <th>Kode Produksi</th> --}}
                            <th>Tanggal Produksi</th>
                            <th>Jenis Keripik</th>
                            <th>Berat</th>
                            <th>Jumlah Produksi</th>
                            <th>Total Biaya</th>
                            <th>Bahan Baku</th>
                            <th>Stok Tersedia</th>
                            <th>Operator</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($komposisi as $key => $item)
                        <tr>
                            <td>{{ $komposisi->firstItem() + $key }}</td>
                            {{-- <td>
                                <span class="badge bg-secondary">{{ $item->kode_produksi }}</span>
                            </td> --}}
                            <td>
                                <span class="fw-bold">
                                    {{ \Carbon\Carbon::parse($item->tanggal_produksi)->format('d/m/Y') }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_produksi)->format('H:i') }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $item->jenisKeripik->nama_jenis ?? '-' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $item->jenisKeripik->berat ?? 0 }} Gram
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary" style="font-size: 14px;">
                                    {{ number_format($item->jumlah_produksi, 0, ',', '.') }} Pcs
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success" style="font-size: 14px;">
                                    Rp {{ number_format($item->total_biaya ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    // Ambil semua bahan baku untuk produksi ini
                                    $bahanList = \App\Models\Komposisi::where('kode_produksi', $item->kode_produksi)
                                        ->with('bahanBaku')
                                        ->get();
                                @endphp
                                @foreach($bahanList as $bahan)
                                    <span class="badge bg-info mb-1 d-block">
                                        {{ $bahan->bahanBaku->nama_bahan ?? '-' }} 
                                        ({{ number_format($bahan->jumlah_dibutuhkan, 2) }} {{ $bahan->bahanBaku->satuan ?? '' }})
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @php
                                    $stok = $item->jenisKeripik->stok->jumlah_stok ?? 0;
                                    $statusClass = $stok > 10 ? 'success' : ($stok > 5 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ number_format($stok, 0, ',', '.') }} Pcs
                                </span>
                            </td>
                            <td>{{ $item->user->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('komposisi.show', $item->kode_produksi) }}" class="btn btn-info btn-sm">
                                    <i data-feather="eye"></i>
                                </a>
                                <form action="{{ route('komposisi.destroy', $item->kode_produksi) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data produksi ini?')">
                                        <i data-feather="trash-2"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Total Keseluruhan</td>
                            <td>{{ number_format($komposisi->sum('jumlah_produksi'), 0, ',', '.') }} Pcs</td>
                            <td>Rp {{ number_format($komposisi->sum('total_biaya'), 0, ',', '.') }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $komposisi->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i data-feather="inbox" class="text-muted" style="width: 48px; height: 48px;"></i>
                <h5 class="mt-3 text-muted">Belum ada data produksi</h5>
                <p class="text-muted">Silakan lakukan produksi keripik terlebih dahulu</p>
                <a href="{{ route('komposisi.create') }}" class="btn btn-primary">
                    <i data-feather="plus"></i> Produksi Baru
                </a>
            </div>
        @endif
    </div>
</div>

@endsection