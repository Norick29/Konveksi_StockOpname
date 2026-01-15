<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        h3 {
            margin-bottom: 5px;
        }
        h4 {
            margin-top: 15px;
            margin-bottom: 5px;
        }
        hr {
            margin: 12px 0;
        }
        ul {
            margin: 5px 0 10px 18px;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 4px;
            font-size: 11px;
        }
        table th {
            background: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>

@foreach($reports as $r)

<h3>{{ strtoupper($r['store']->name) }} - Daily Report {{ $date }}</h3>

{{-- ================= BUANG BENANG ================= --}}
<h4>Laporan Buang Benang</h4>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Size</th>
            <th>Kategori</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($r['in']->groupBy(fn($i) => $i->produk->sku) as $sku => $items)
            @foreach($items->groupBy(fn($i) => $i->produk->size ?? '-') as $size => $rows)
                <tr>
                    <td>{{ $sku }}</td>
                    <td>{{ $size }}</td>
                    <td>{{ $rows->first()->produk->kategori->name ?? '-' }}</td>
                    <td>{{ $rows->sum('quantity') }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- ================= E-COMMERCE ================= --}}
<h4>Laporan E-Commerce</h4>

<table>
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Total Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($r['out']->groupBy(fn($o) => $o->produk->kategori->name) as $cat => $items)
            <tr>
                <td>{{ $cat }}</td>
                <td>{{ $items->sum('quantity') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Total Keseluruhan:</strong> {{ $r['total_out'] }}</p>

<h4>Ekspedisi</h4>

<table>
    <thead>
        <tr>
            <th>Kurir</th>
            <th>Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($r['shipments'] as $s)
            <tr>
                <td>{{ strtoupper($s->courier) }}</td>
                <td>{{ $s->total }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@foreach ($reports as $report)
    <strong>{{ $report['store']->nama_toko }}</strong>
    <h4>Admin</h4>
    <p>
        Press: {{ $report['press'] }} <br>
        Resi: {{ $report['resi'] }} <br>
        Packing: {{ $report['packing'] }} <br>
        Buang Benang: {{ $report['buang'] }}
    </p>
@endforeach

<hr>

@endforeach

</body>
</html>
