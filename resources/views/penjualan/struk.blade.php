<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - {{ $penjualan->no_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
        .header {
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .header h3 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
            color: #666;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 4px 0;
        }
        .item-table td {
            padding: 2px 0;
        }
        .total {
            font-weight: bold;
            font-size: 16px;
        }
        .footer {
            border-top: 2px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-selesai { background: #d4edda; color: #155724; }
        .status-pesan { background: #fff3cd; color: #856404; }
        .status-proses { background: #d1ecf1; color: #0c5460; }
        .status-batal { background: #f8d7da; color: #721c24; }
        @media print {
            body { padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <h3>🍟 Toko Keripik Keladi</h3>
        <p>Jl. Raya No. 123, Kota</p>
        <p>Telp: 0812-3456-7890</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>No. Transaksi</strong></td>
                <td>: {{ $penjualan->no_transaksi }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ $penjualan->tanggal_formatted }}</td>
            </tr>
            <tr>
                <td><strong>Pembeli</strong></td>
                <td>: {{ $penjualan->nama_pembeli }}</td>
            </tr>
            @if($penjualan->no_hp_pembeli)
            <tr>
                <td><strong>No. HP</strong></td>
                <td>: {{ $penjualan->no_hp_pembeli }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Status</strong></td>
                <td>: 
                    <span class="status-badge status-{{ $penjualan->status }}">
                        {{ $penjualan->status_label }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="items">
        <table class="item-table">
            <tr>
                <th style="text-align:left;">Produk</th>
                <th style="text-align:center;">Qty</th>
                <th style="text-align:right;">Subtotal</th>
            </tr>
            @foreach($penjualan->details as $item)
            <tr>
                <td>{{ $item->jenisKeripik->nama_jenis ?? '-' }}</td>
                <td style="text-align:center;">{{ $item->jumlah }}</td>
                <td style="text-align:right;">{{ $item->subtotal_formatted }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="divider"></div>

    <div class="total">
        <table>
            <tr>
                <td style="font-size:16px;"><strong>Total</strong></td>
                <td style="text-align:right;font-size:18px;">
                    <strong>{{ $penjualan->total_harga_formatted }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="footer">
        <p>Terima kasih telah berbelanja!</p>
        <p>Kasir: {{ $penjualan->user->nama ?? '-' }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
        <p style="font-size:10px;">*Barang yang sudah dibeli tidak dapat dikembalikan</p>
    </div>

    <div class="text-center no-print" style="margin-top:20px;">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Struk
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            ✖ Tutup
        </button>
    </div>

    <style>
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</body>
</html>