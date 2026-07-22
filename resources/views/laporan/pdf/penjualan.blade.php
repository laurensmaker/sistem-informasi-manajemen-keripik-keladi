{{-- resources/views/laporan/pdf/penjualan.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 10px;
            margin: 0;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .header .subtitle {
            font-size: 12px;
            color: #555;
            margin-bottom: 3px;
        }
        .header .info {
            font-size: 10px;
            color: #777;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 3px;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item .label {
            font-size: 9px;
            color: #777;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9px;
        }
        .table th {
            background-color: #2c3e50;
            color: #fff;
            border: 1px solid #2c3e50;
            padding: 5px 4px;
            text-align: left;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 4px;
            vertical-align: middle;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .badge-success {
            color: #155724;
            background: #d4edda;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-danger {
            color: #721c24;
            background: #f8d7da;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-warning {
            color: #856404;
            background: #fff3cd;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #333;
        }
        .page-break {
            page-break-after: always;
        }
        /* Ukuran kolom lebih kecil */
        .col-no { width: 25px; }
        .col-transaksi { width: 120px; }
        .col-tanggal { width: 80px; }
        .col-pembeli { width: 120px; }
        .col-hp { width: 80px; }
        .col-item { width: 40px; }
        .col-total { width: 100px; }
        .col-status { width: 60px; }
        
        @page {
            margin: 10px 10px 10px 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">{{ $subtitle }}</div>
        <div class="info">Dicetak: {{ $tanggal_cetak }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Transaksi</div>
            <div class="value">{{ $totalTransaksi }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Item Terjual</div>
            <div class="value">{{ number_format($totalItems) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="col-no text-center">No</th>
                <th class="col-transaksi">No Transaksi</th>
                <th class="col-tanggal text-center">Tanggal</th>
                <th class="col-pembeli">Pembeli</th>
                <th class="col-hp text-center">No HP</th>
                <th class="col-item text-center">Item</th>
                <th class="col-total text-right">Total Harga</th>
                <th class="col-status text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penjualan as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->no_transaksi }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                <td>{{ Str::limit($item->nama_pembeli, 20) }}</td>
                <td class="text-center">{{ $item->no_hp_pembeli ?? '-' }}</td>
                <td class="text-center">{{ $item->details->sum('jumlah') }}</td>
                <td class="text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge-{{ $item->status == 'selesai' ? 'success' : 'warning' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Tidak ada data penjualan</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Keseluruhan</td>
                <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>&copy; {{ date('Y') }} Sistem Informasi Manajemen Keripik Keladi</p>
    </div>
</body>
</html>