{{-- resources/views/komposisi/show.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Detail Produksi</h3>
    <div>
        <a href="{{ route('komposisi.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card bg-white border-0 rounded-10 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold">Informasi Produksi</h5>
                <table class="table table-borderless">
                    <tr>
                        <td width="150"><strong>Kode Produksi</strong></td>
                        <td>: <span class="badge bg-secondary">{{ $detail->kode_produksi }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Produksi</strong></td>
                        <td>: {{ \Carbon\Carbon::parse($detail->tanggal_produksi)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Keripik</strong></td>
                        <td>: {{ $detail->jenisKeripik->nama_jenis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Berat</strong></td>
                        <td>: {{ $detail->jenisKeripik->berat ?? 0 }} Gram</td>
                    </tr>
                    <tr>
                        <td><strong>Jumlah Produksi</strong></td>
                        <td>: <span class="badge bg-primary">{{ number_format($detail->jumlah_produksi, 0, ',', '.') }} Pcs</span></td>
                    </tr>
                    <tr>
                        <td><strong>Total Biaya</strong></td>
                        <td>: <span class="badge bg-success">Rp {{ number_format($detail->total_biaya ?? 0, 0, ',', '.') }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Operator</strong></td>
                        <td>: {{ $detail->user->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card bg-white border-0 rounded-10 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold">Komposisi Bahan Baku</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Jumlah Dibutuhkan</th>
                                <th>Satuan</th>
                                <th>Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produksi as $item)
                            <tr>
                                <td>{{ $item->bahanBaku->nama_bahan ?? '-' }}</td>
                                <td>{{ number_format($item->jumlah_dibutuhkan, 2, ',', '.') }}</td>
                                <td>{{ $item->bahanBaku->satuan ?? '-' }}</td>
                                <td>
                                    Rp {{ number_format($item->bahanBaku->harga_satuan * $item->jumlah_dibutuhkan, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">Total</td>
                                <td>Rp {{ number_format($detail->total_biaya, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection