{{-- resources/views/stok-keripik/index.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Stok Keripik</h3>
    <div>
        {{-- <a href="{{ route('stok-keripik.laporan') }}" class="btn btn-info btn-sm">
            <i data-feather="file-text"></i> Laporan
        </a> --}}
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

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        {{-- <th>Kode</th> --}}
                        <th>Jenis Keripik</th>
                        <th>Berat</th>
                        <th>Stok Saat Ini</th>
                        <th>Stok Masuk</th>
                        <th>Stok Keluar</th>
                        <th>Tanggal Update</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stokKeripik as $key => $item)
                        <tr>
                            <td>{{ $stokKeripik->firstItem() + $key }}</td>
                            {{-- <td>
                                <span class="badge bg-secondary">{{ $item->kode_keripik ?? '-' }}</span>
                            </td> --}}
                            <td>
                                <strong>{{ $item->jenisKeripik->nama_jenis ?? '-' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item->jenisKeripik->berat ?? 0 }} Gram</span>
                            </td>
                            <td>
                                @php
                                    $stok = $item->jumlah_stok;
                                    $statusClass = $stok > 10 ? 'success' : ($stok > 5 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $statusClass }}" style="font-size: 14px;">
                                    {{ number_format($stok, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ number_format($item->jumlah_masuk, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ number_format($item->jumlah_keluar, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <small>{{ $item->tanggal_update->format('d/m/Y H:i') }}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i data-feather="inbox" class="text-muted" style="width: 40px; height: 40px;"></i>
                                <p class="mt-2 text-muted">Tidak ada data stok keripik</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="4" class="text-end">Total Keseluruhan</td>
                        <td>
                            <span class="badge bg-primary" style="font-size: 14px;">
                                {{ number_format($stokKeripik->sum('jumlah_stok'), 0, ',', '.') }}
                            </span>
                        </td>
                        <td>{{ number_format($stokKeripik->sum('jumlah_masuk'), 0, ',', '.') }}</td>
                        <td>{{ number_format($stokKeripik->sum('jumlah_keluar'), 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $stokKeripik->links() }}
        </div>
    </div>
</div>

@endsection