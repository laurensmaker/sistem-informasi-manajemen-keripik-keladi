{{-- resources/views/laporan/bahan-baku.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Laporan Bahan Baku & Stok</h3>
    <div>
        <a href="{{ route('laporan.bahan-baku.download') }}" class="btn btn-success btn-sm">
            <i data-feather="download"></i> Download PDF
        </a>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        {{-- <th>Kode</th> --}}
                        <th>Nama Bahan</th>
                        <th>Satuan</th>
                        <th>Supplier</th>
                        <th>Harga Satuan</th>
                        <th>Stok Tersedia</th>
                        <th>Total Masuk</th>
                        <th>Total Keluar</th>
                        <th>Nilai Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanBaku as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        {{-- <td>{{ $item->stok->kode_bahan ?? '-' }}</td> --}}
                        <td><strong>{{ $item->nama_bahan }}</strong></td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->supplier ?? '-' }}</td>
                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ ($item->stok->jumlah_stok ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format($item->stok->jumlah_stok ?? 0, 0) }}
                            </span>
                        </td>
                        <td>{{ number_format($item->stok->jumlah_masuk ?? 0, 0) }}</td>
                        <td>{{ number_format($item->stok->jumlah_keluar ?? 0, 0) }}</td>
                        <td>
                            Rp {{ number_format(($item->stok->jumlah_stok ?? 0) * $item->harga_satuan, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data</td>
                    </tr>
                    @endforelse
                </tbody>
                {{-- <tfoot class="table-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">Total</td>
                        <td><strong>{{ number_format($totalStok, 2) }}</strong></td>
                        <td colspan="2"></td>
                        <td><strong>Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot> --}}
            </table>
        </div>
    </div>
</div>

@endsection