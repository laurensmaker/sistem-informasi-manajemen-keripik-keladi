{{-- resources/views/laporan/pdf/bahan-baku.blade.php --}}
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
            font-size: 11px;
            padding: 15px;
            margin: 0;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header .subtitle {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .header .info {
            font-size: 11px;
            color: #777;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 12px 15px;
            background: #f5f5f5;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item .label {
            font-size: 10px;
            color: #777;
            font-weight: normal;
        }
        .summary-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-top: 2px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        .table th {
            background-color: #2c3e50;
            color: #fff;
            border: 1px solid #2c3e50;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
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
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .badge-danger {
            color: #721c24;
            background: #f8d7da;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .total-row {
            background-color: #f0f0f0 !important;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #333;
        }
        
        /* Ukuran kolom yang lebih proporsional */
        .col-no { width: 5%; }
        .col-nama { width: 22%; }
        .col-satuan { width: 8%; }
        .col-supplier { width: 15%; }
        .col-harga { width: 12%; }
        .col-stok { width: 10%; }
        .col-masuk { width: 10%; }
        .col-keluar { width: 10%; }
        .col-nilai { width: 15%; }
        
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .table th {
                background-color: #2c3e50 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .badge-success, .badge-danger {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
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
            <div class="label">Total Jenis Bahan</div>
            <div class="value">{{ $totalBahan }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Nilai Stok</div>
            <div class="value">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="col-no text-center">No</th>
                <th class="col-nama">Nama Bahan</th>
                <th class="col-satuan text-center">Satuan</th>
                <th class="col-supplier">Supplier</th>
                <th class="col-harga text-right">Harga Satuan</th>
                <th class="col-stok text-center">Stok</th>
                <th class="col-masuk text-center">Masuk</th>
                <th class="col-keluar text-center">Keluar</th>
                <th class="col-nilai text-right">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bahanBaku as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $item->nama_bahan }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td>{{ $item->supplier ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-center">
                    @php
                        $stok = $item->stok->jumlah_stok ?? 0;
                    @endphp
                    <span class="{{ $stok > 0 ? 'badge-success' : 'badge-danger' }}">
                        {{ number_format($stok, 0, ',', '.') }}
                    </span>
                </td>
                <td class="text-center">{{ number_format($item->stok->jumlah_masuk ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($item->stok->jumlah_keluar ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">
                    Rp {{ number_format(($item->stok->jumlah_stok ?? 0) * $item->harga_satuan, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4">Tidak ada data bahan baku</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($totalStok, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>&copy; {{ date('Y') }} Sistem Informasi Manajemen Keripik Keladi</p>
    </div>
</body>
</html>