{{-- resources/views/laporan/index.blade.php --}}
@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Laporan</h3>
</div>

<div class="row">
    {{-- Laporan Penjualan --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i data-feather="shopping-cart" style="width: 48px; height: 48px;" class="text-primary"></i>
                </div>
                <h5 class="fw-bold">Laporan Penjualan</h5>
                <p class="text-muted small">Download laporan penjualan berdasarkan periode</p>
                <a href="{{ route('laporan.penjualan') }}" class="btn btn-primary btn-sm w-100">
                    <i data-feather="file-text"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- Laporan Jenis Keripik --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i data-feather="package" style="width: 48px; height: 48px;" class="text-success"></i>
                </div>
                <h5 class="fw-bold">Laporan Jenis Keripik</h5>
                <p class="text-muted small">Download laporan jenis keripik dengan stok</p>
                 <a href="{{ route('laporan.jenis-keripik') }}" class="btn btn-success btn-sm w-100">
                    <i data-feather="file-text"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- Laporan Bahan Baku --}}
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i data-feather="box" style="width: 48px; height: 48px;" class="text-warning"></i>
                </div>
                <h5 class="fw-bold">Laporan Bahan Baku</h5>
                <p class="text-muted small">Download laporan bahan baku dengan stok</p>
                <a href="{{ route('laporan.bahan-baku') }}" class="btn btn-warning btn-sm w-100">
                    <i data-feather="file-text"></i> Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection