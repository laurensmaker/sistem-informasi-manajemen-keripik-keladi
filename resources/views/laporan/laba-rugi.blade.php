@extends('layouts.main')

@section('content')


<!-- Ringkasan -->
<div class="row">
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
                <h6 class="text-muted">Total Pendapatan</h6>
                <h4 class="fw-bold text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total HPP</h6>
                <h4 class="fw-bold text-warning">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Total Laba/Rugi</h6>
                <h4 class="fw-bold text-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">
                    {{ $totalLaba >= 0 ? '+' : '-' }} Rp {{ number_format(abs($totalLaba), 0, ',', '.') }}
                </h4>
            </div>
        </div>
    </div>
</div>

<!-- Detail Transaksi -->
<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Detail Transaksi</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>Total Penjualan</th>
                        <th>HPP</th>
                        <th>Laba/Rugi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan as $key => $item)
                        @php
                            $hpp = $item->hpp;
                            $laba = $item->laba_rugi;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->no_transaksi }}</td>
                            <td>{{ $item->tanggal->format('d/m/Y H:i') }}</td>
                            <td>{{ $item->nama_pembeli }}</td>
                            <td>{{ $item->total_harga_formatted }}</td>
                            <td>Rp {{ number_format($hpp, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $laba >= 0 ? 'success' : 'danger' }}">
                                    {{ $laba >= 0 ? '+' : '-' }} Rp {{ number_format(abs($laba), 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $item->status_badge }}">
                                    {{ $item->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-info fw-bold">
                        <td colspan="4" class="text-end">TOTAL</td>
                        <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                        <td class="text-{{ $totalLaba >= 0 ? 'success' : 'danger' }}">
                            {{ $totalLaba >= 0 ? '+' : '-' }} Rp {{ number_format(abs($totalLaba), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Per Produk -->
<div class="card bg-white border-0 rounded-10">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Ringkasan Per Produk</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>Qty Terjual</th>
                        <th>Total Penjualan</th>
                        <th>Total HPP</th>
                        <th>Laba</th>
                        <th>Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ringkasanProduk as $key => $item)
                        @php
                            $margin = $item['total_penjualan'] > 0 ? ($item['laba'] / $item['total_penjualan']) * 100 : 0;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ number_format($item['qty'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['total_penjualan'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['hpp'], 0, ',', '.') }}</td>
                            <td class="text-{{ $item['laba'] >= 0 ? 'success' : 'danger' }}">
                                {{ $item['laba'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($item['laba']), 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger') }}">
                                    {{ number_format($margin, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Per Bahan Baku -->
<div class="card bg-white border-0 rounded-10 mt-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Ringkasan Pemakaian Bahan Baku</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bahan Baku</th>
                        <th>Satuan</th>
                        <th>Total Terpakai</th>
                        <th>Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ringkasanBahan as $key => $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['nama'] }}</td>
                            <td>{{ $item['satuan'] }}</td>
                            <td>{{ number_format($item['total_terpakai'], 2, ',', '.') }}</td>
                            <td>Rp {{ number_format($item['total_biaya'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data bahan baku</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    @media print {
        .btn, .d-sm-flex .btn, form {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
        .table {
            font-size: 11px;
        }
    }
</style>
@endpush