@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Detail Penjualan</h3>
    <div>
        @if($penjualan->status != 'selesai' && $penjualan->status != 'batal')
            <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="btn btn-warning btn-sm">
                <i data-feather="edit-2"></i> Edit
            </a>
        @endif
        {{-- <a href="{{ route('penjualan.print-struk', $penjualan->id) }}" class="btn btn-success btn-sm" target="_blank">
            <i data-feather="printer"></i> Print Struk
        </a> --}}
        <a href="{{ route('penjualan.index') }}" class="btn btn-secondary btn-sm">
            <i data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">No. Transaksi</th>
                                <td><strong>{{ $penjualan->no_transaksi }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ $penjualan->tanggal_formatted }}</td>
                            </tr>
                            <tr>
                                <th>Pembeli</th>
                                <td>{{ $penjualan->nama_pembeli }}</td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>{{ $penjualan->no_hp_pembeli ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Status</th>
                                <td>
                                    <span class="{{ $penjualan->status_badge }}" style="font-size: 14px;">
                                        {{ $penjualan->status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Kasir</th>
                                <td>{{ $penjualan->user->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td>
                                    <h5 class="fw-bold text-primary">{{ $penjualan->total_harga_formatted }}</h5>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-white border-0 rounded-10 shadow-sm mt-3">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Detail Produk</h5>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penjualan->details as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->jenisKeripik->nama_jenis ?? '-' }}</td>
                                    <td>{{ $item->harga_satuan_formatted }}</td>
                                    <td>{{ $item->jumlah }}</td>
                                    <td>{{ $item->subtotal_formatted }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="4" class="text-end">Total</th>
                                <th>{{ $penjualan->total_harga_formatted }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Update Status</h5>
                <form id="formUpdateStatus">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select" id="statusSelect">
                            <option value="pesan" {{ $penjualan->status == 'pesan' ? 'selected' : '' }}>Pesan</option>
                            <option value="proses" {{ $penjualan->status == 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="selesai" {{ $penjualan->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ $penjualan->status == 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i data-feather="refresh-cw"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Update status
    $('#formUpdateStatus').submit(function(e) {
        e.preventDefault();
        
        const status = $('#statusSelect').val();
        const url = "{{ route('penjualan.update-status', $penjualan->id) }}";
        const data = $(this).serialize();

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Yakin ingin mengubah status menjadi ' + $('#statusSelect option:selected').text() + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal mengupdate status!'
                        });
                    }
                });
            }
        });
    });
</script>
@endpush