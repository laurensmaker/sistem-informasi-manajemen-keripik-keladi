@extends('layouts.main')

@section('content')


<!-- Statistik Bulan Ini -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Pendapatan Bulan Ini</h6>
                <h3 class="fw-bold text-success">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">HPP Bulan Ini</h6>
                <h3 class="fw-bold text-warning">Rp {{ number_format($hppBulanIni, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card bg-white border-0 rounded-10 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Laba/Rugi Bulan Ini</h6>
                <h3 class="fw-bold text-{{ $labaBulanIni >= 0 ? 'success' : 'danger' }}">
                    {{ $labaBulanIni >= 0 ? '+' : '-' }} Rp {{ number_format(abs($labaBulanIni), 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
</div>



@endsection