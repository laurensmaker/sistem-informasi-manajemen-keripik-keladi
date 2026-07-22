@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Data Komposisi Keripik</h3>
    <a href="{{ route('komposisi.create') }}" class="btn btn-primary btn-sm">
        <i data-feather="plus"></i> Tambah Komposisi
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        @if($jenisKeripik->count() > 0)
            @foreach($jenisKeripik as $jenis)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary">
                        <i data-feather="package"></i> 
                        {{ $jenis->nama_jenis }}
                    </h5>
                    <span class="badge bg-secondary">
                        {{ $jenis->komposisi->count() }} Bahan
                    </span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Bahan</th>
                                <th>Satuan</th>
                                <th>Jumlah Dibutuhkan</th>
                                <th>Harga Satuan</th>
                                <th>Total Biaya</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenis->komposisi as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $item->bahanBaku->nama_bahan ?? '-' }}</strong>
                                    @if($item->bahanBaku && $item->bahanBaku->supplier)
                                        <br>
                                        <small class="text-muted">Supplier: {{ $item->bahanBaku->supplier }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->bahanBaku->satuan ?? '-' }}</span>
                                </td>
                                <td>{{ number_format($item->jumlah_dibutuhkan, 2, ',', '.') }}</td>
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
                                <td colspan="7" class="text-center text-muted">
                                    <i data-feather="inbox"></i> 
                                    Belum ada komposisi untuk jenis keripik ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold">Total Biaya Keseluruhan:</td>
                                <td colspan="2">
                                    <span class="badge bg-success fs-6">
                                        Rp {{ number_format($jenis->komposisi->sum(function($item) {
                                            return ($item->bahanBaku->harga_satuan ?? 0) * $item->jumlah_dibutuhkan;
                                        }), 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <hr>
            </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i data-feather="inbox" class="text-muted" style="width: 48px; height: 48px;"></i>
                <h5 class="mt-3 text-muted">Belum ada data komposisi</h5>
                <p class="text-muted">Silakan tambahkan komposisi keripik terlebih dahulu</p>
                <a href="{{ route('komposisi.create') }}" class="btn btn-primary">
                    <i data-feather="plus"></i> Tambah Komposisi
                </a>
            </div>
        @endif
    </div>
</div>

@endsection