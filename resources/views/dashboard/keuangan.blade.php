@extends('layouts.main')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-4">
    <h3 class="fs-18">Dashboard Keuangan</h3>
    <div>
        <a href="{{ route('laporan.laba-rugi') }}" class="btn btn-primary btn-sm">
            <i data-feather="file-text"></i> Laporan Laba Rugi
        </a>
        <a href="{{ route('laporan.keuntungan-produk') }}" class="btn btn-success btn-sm">
            <i data-feather="package"></i> Keuntungan Produk
        </a>
    </div>
</div>

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

<!-- Grafik 6 Bulan Terakhir -->
<div class="card bg-white border-0 rounded-10 shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Grafik 6 Bulan Terakhir</h5>
        <canvas id="keuanganChart" height="300"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('keuanganChart').getContext('2d');
        
        const dataBulanan = @json($dataBulanan);
        
        const labels = dataBulanan.map(item => item.bulan);
        const pendapatan = dataBulanan.map(item => item.pendapatan);
        const hpp = dataBulanan.map(item => item.hpp);
        const laba = dataBulanan.map(item => item.laba);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: pendapatan,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'HPP',
                        data: hpp,
                        backgroundColor: 'rgba(255, 193, 7, 0.5)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Laba',
                        data: laba,
                        backgroundColor: 'rgba(23, 162, 184, 0.5)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection