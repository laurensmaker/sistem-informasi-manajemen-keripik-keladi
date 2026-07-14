@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Biaya Produksi Keripik</h3>
    <div>
        <button onclick="window.print()" class="btn btn-success btn-sm">
            <i data-feather="printer"></i> Print
        </button>
        <a href="{{ route('komposisi.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    @forelse($data as $item)
        <div class="col-md-6 mb-4">
            <div class="card bg-white border-0 rounded-10 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $item['jenis_keripik'] }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item['komposisi'] as $kom)
                                <tr>
                                    <td>{{ $kom->bahanBaku->nama_bahan ?? '-' }}</td>
                                    <td>{{ $kom->bahanBaku->satuan ?? '-' }}</td>
                                    <td>{{ number_format($kom->jumlah_dibutuhkan, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($kom->bahanBaku->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        Rp {{ number_format(($kom->bahanBaku->harga_satuan ?? 0) * $kom->jumlah_dibutuhkan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="4" class="text-end">Total Biaya Produksi:</th>
                                <th>
                                    <span class="badge bg-success" style="font-size: 14px;">
                                        {{ $item['total_biaya_formatted'] }}
                                    </span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i data-feather="info"></i> Belum ada data komposisi
            </div>
        </div>
    @endforelse
</div>

@endsection

@push('styles')
<style>
    @media print {
        .btn, .d-sm-flex .btn {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
        }
    }
</style>
@endpush