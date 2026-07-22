{{-- resources/views/laporan/penjualan.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Laporan Penjualan</h3>
    <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">
        <i data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card bg-white border-0 rounded-10 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('laporan.penjualan.download') }}" method="POST" target="_blank">
            @csrf
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i data-feather="download"></i> Download PDF
                    </button>
                </div>
            </div>
            <div class="alert alert-info">
                <i data-feather="info"></i>
                <strong>Catatan:</strong> Kosongkan tanggal untuk laporan hari ini
            </div>
        </form>
    </div>
</div>

@endsection