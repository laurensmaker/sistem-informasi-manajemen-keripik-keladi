{{-- resources/views/laporan/pdf/jenis-keripik.blade.php --}}
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
            font-size: 9px;
            padding: 8px;
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
            font-size: 8px;
        }
        .table th {
            background-color: #2c3e50;
            color: #fff;
            border: 1px solid #2c3e50;
            padding: 4px 3px;
            text-align: left;
        }
        .table td {
            border: 1px solid #ddd;
            padding: 3px;
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
        .badge-success {
            color: #155724;
            background: #d4edda;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        .badge-danger {
            color: #721c24;
            background: #f8d7da;
            padding: 1px 6px;
            border-radius: 3px;
            font-size: 7px;
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
        .col-no { width: 20px; }
        .col-kode { width: 50px; }
        .col-nama { width: 150px; }
        .col-satuan { width: 40px; }
        .col-harga { width: 80px; }
        .col-stok { width: 50px; }
        .col-masuk { width: 50px; }
        .col-keluar { width: 50px; }
        .col-bahan { width: 40px; }
        
        @page {
            margin: 8px 8px 8px 8px;
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
            <div class="label">Total Jenis Keripik</div>
            <div class="value">{{ $totalJenis }}</div>
        </div>
        {{-- <div class="summary-item">
            <div class="label">Total Stok Tersedia</div>
            <div class="value">{{ number_format($totalStok) }}</div>
        </div> --}}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th class="col-no text-center">No</th>
                <th class="col-kode text-center">Kode</th>
                <th class="col-nama">Nama Keripik</th>
                <th class="col-satuan text-center">Satuan</th>
                <th class="col-harga text-right">Harga Jual</th>
                <th class="col-stok text-center">Stok</th>
                <th class="col-masuk text-center">Masuk</th>
                <th class="col-keluar text-center">Keluar</th>
                {{-- <th class="col-bahan text-center">Bahan</th> --}}
            </tr>
        </thead>
        <tbody>
            @forelse($jenisKeripik as $key => $item)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center">{{ $item->stok->kode_keripik ?? '-' }}</td>
                <td>{{ Str::limit($item->nama_jenis, 25) }}</td>
                <td class="text-center">{{ $item->satuan }}</td>
                <td class="text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge-{{ ($item->stok->jumlah_stok ?? 0) > 0 ? 'success' : 'danger' }}">
                        {{ number_format($item->stok->jumlah_stok ?? 0) }}
                    </span>
                </td>
                <td class="text-center">{{ number_format($item->stok->jumlah_masuk ?? 0) }}</td>
                <td class="text-center">{{ number_format($item->stok->jumlah_keluar ?? 0) }}</td>
                {{-- <td class="text-center">{{ $item->komposisi->count() }}</td> --}}
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Keseluruhan</td>
                {{-- <td class="text-center">{{ number_format($totalStok) }}</td> --}}
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ $tanggal_cetak }}</p>
        <p>&copy; {{ date('Y') }} Sistem Informasi Manajemen Keripik Keladi</p>
    </div>
</body>
</html>