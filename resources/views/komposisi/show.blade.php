{{-- resources/views/komposisi/show.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Detail Komposisi Keripik</h3>
    <div>
        <a href="{{ route('komposisi.edit', $komposisi->id) }}" class="btn btn-warning btn-sm">
            <i data-feather="edit-2"></i> Edit
        </a>
        <a href="{{ route('komposisi.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    {{-- Informasi Jenis Keripik --}}
    <div class="col-lg-4 mb-3">
        <div class="card bg-white border-0 rounded-10">
            <div class="card-body p-4">
                <h5 class="text-primary mb-3">
                    <i data-feather="package"></i> Informasi Keripik
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <td width="120"><strong>Nama Keripik</strong></td>
                        <td>: {{ $komposisi->jenisKeripik->nama_jenis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Satuan</strong></td>
                        <td>: {{ $komposisi->jenisKeripik->satuan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Berat</strong></td>
                        <td>: {{ $komposisi->jenisKeripik->berat ?? '-' }} Gram</td>
                    </tr>
                    <tr>
                        <td><strong>Harga Jual</strong></td>
                        <td>: Rp {{ number_format($komposisi->jenisKeripik->harga_jual ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Biaya Produksi</strong></td>
                        <td>: 
                            <span class="badge bg-success">
                                Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Margin Keuntungan</strong></td>
                        <td>: 
                            @php
                                $hargaJual = $komposisi->jenisKeripik->harga_jual ?? 0;
                                $margin = $hargaJual - $totalBiaya;
                                $persentase = $totalBiaya > 0 ? round(($margin / $totalBiaya) * 100, 2) : 0;
                            @endphp
                            <span class="badge {{ $margin > 0 ? 'bg-success' : 'bg-danger' }}">
                                Rp {{ number_format($margin, 0, ',', '.') }} 
                                ({{ $persentase }}%)
                            </span>
                        </td>
                    </tr>
                    @if($komposisi->jenisKeripik->deskripsi)
                    <tr>
                        <td><strong>Deskripsi</strong></td>
                        <td>: {{ $komposisi->jenisKeripik->deskripsi }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Bahan Baku --}}
    <div class="col-lg-8 mb-3">
        <div class="card bg-white border-0 rounded-10">
            <div class="card-body p-4">
                <h5 class="text-success mb-3">
                    <i data-feather="list"></i> Detail Bahan Baku
                </h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Bahan</th>
                                <th>Satuan</th>
                                <th>Jumlah Dibutuhkan</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            <tr class="table-primary">
                                <td>1</td>
                                <td>
                                    <strong>{{ $komposisi->bahanBaku->nama_bahan ?? '-' }}</strong>
                                    @if($komposisi->bahanBaku && $komposisi->bahanBaku->supplier)
                                        <br>
                                        <small class="text-muted">
                                             {{ $komposisi->bahanBaku->supplier }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $komposisi->bahanBaku->satuan ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ number_format($komposisi->jumlah_dibutuhkan, 2, ',', '.') }}</span>
                                </td>
                                <td>
                                    Rp {{ number_format($komposisi->bahanBaku->harga_satuan ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        Rp {{ number_format(($komposisi->bahanBaku->harga_satuan ?? 0) * $komposisi->jumlah_dibutuhkan, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $total += ($komposisi->bahanBaku->harga_satuan ?? 0) * $komposisi->jumlah_dibutuhkan;
                                    @endphp
                                </td>
                            </tr>

                            @foreach($komposisiLainnya as $key => $item)
                            <tr>
                                <td>{{ $key + 2 }}</td>
                                <td>
                                    {{ $item->bahanBaku->nama_bahan ?? '-' }}
                                    @if($item->bahanBaku && $item->bahanBaku->supplier)
                                        <br>
                                        <small class="text-muted">
                                             {{ $item->bahanBaku->supplier }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->bahanBaku->satuan ?? '-' }}</span>
                                </td>
                                <td>
                                    {{ number_format($item->jumlah_dibutuhkan, 0, ',', '.') }}
                                </td>
                                <td>
                                    Rp {{ number_format($item->bahanBaku->harga_satuan ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        Rp {{ number_format(($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan, 0, ',', '.') }}
                                    </span>
                                    @php
                                        $total += ($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan;
                                    @endphp
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Biaya Produksi</td>
                                <td>
                                    <span class="badge bg-success fs-6">
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Harga Jual</td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        Rp {{ number_format($komposisi->jenisKeripik->harga_jual ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Keuntungan</td>
                                <td>
                                    @php
                                        $hargaJual = $komposisi->jenisKeripik->harga_jual ?? 0;
                                        $keuntungan = $hargaJual - $total;
                                    @endphp
                                    <span class="badge {{ $keuntungan > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                        Rp {{ number_format($keuntungan, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Riwayat Perubahan (Opsional) --}}
<div class="row">
    <div class="col-lg-12 mb-3">
        <div class="card bg-white border-0 rounded-10">
            <div class="card-body p-4">
                <h5 class="text-muted mb-3">
                    <i data-feather="clock"></i> Informasi Sistem
                </h5>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="150"><strong>Tanggal Dibuat</strong></td>
                        <td>: {{ $komposisi->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Terakhir Diupdate</strong></td>
                        <td>: {{ $komposisi->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Oleh</strong></td>
                        <td>: {{ $komposisi->user->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection