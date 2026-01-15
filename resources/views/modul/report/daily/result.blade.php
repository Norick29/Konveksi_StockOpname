@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Daily Report {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
    </h1>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('daily-report.index') }}" class="btn btn-secondary mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <a href="{{ route('daily-report.pdf') }}" class="btn btn-danger">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>

@foreach($reports as $r)

{{-- BUANG BENANG --}}
<div class="row">

    {{-- BUANG BENANG --}}
    <div class="col-md-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header bg-warning">
                <h5 class="m-0">
                    {{ strtoupper($r['store']->name) }} – Laporan Buang Benang
                </h5>
            </div>

            <div class="card-body">

                @php
                    $totalIn = $r['in']->sum('quantity');
                @endphp

                <strong>TOTAL BUANG BENANG : {{ $totalIn }}</strong>

                <hr>

                @forelse($r['in']->groupBy(fn($i) => $i->produk->sku) as $sku => $items)
                    <p>- {{ $sku }} : {{ $items->sum('quantity') }}</p>
                @empty
                    <p class="text-muted">Tidak ada data buang benang</p>
                @endforelse

            </div>
        </div>
    </div>

    {{-- E-COMMERCE --}}
    <div class="col-md-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="m-0">
                    {{ strtoupper($r['store']->name) }} Daily Report E-commerce
                </h5>
            </div>

            <div class="card-body">

                <strong>TOTAL QUANTITY : {{ $r['total_out'] }}</strong>

                <hr>

                {{-- PRODUK --}}
                @foreach(
                    $r['out']->groupBy(function ($o) {
                        return $o->produk->color . ' ' . $o->produk->kategori->name;
                    }) as $label => $items
                )
                    <p>- {{ $label }} : {{ $items->sum('quantity') }}</p>
                @endforeach

                <hr>

                {{-- EKSPEDISI --}}
                <strong>EKSPEDISI</strong>
                @forelse($r['shipments'] as $s)
                    <p>- {{ strtoupper($s->courier) }} : {{ $s->total }}</p>
                @empty
                    <p class="text-muted">No shipment data</p>
                @endforelse

                <hr>

                {{-- ADMIN --}}
                <strong>ADMIN</strong>
                <p>- PRESS : {{ $r['press'] }}</p>
                <p>- RESI : {{ $r['resi'] }}</p>
                <p>- PACKING : {{ $r['packing'] }}</p>
                <p>- BUANG BENANG : {{ $r['buang'] }}</p>

            </div>
        </div>
    </div>

</div>

@endforeach

<div class="row">

    {{-- ================== BUANG BENANG (KIRI) ================== --}}
    <div class="col-md-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header bg-success text-white">
                <h5 class="m-0">WhatsApp Report Buang Benang (Auto)</h5>
            </div>

            <div class="card-body d-flex flex-column">
                <textarea id="waBuangText" class="form-control flex-grow-1" rows="18" readonly>
@foreach($reports as $r)
Laporan Buang Benang:
{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}

{{ strtoupper($r['store']->name) }}

@foreach($r['in']->groupBy(fn($i) => $i->produk->sku) as $sku => $items)
{{ $sku }}
@foreach($items->groupBy(fn($i) => $i->produk->size ?? '-') as $size => $rows)
{{ $size }} = {{ $rows->sum('quantity') }} ({{ $rows->first()->produk->kategori->name ?? '-' }})
@endforeach

@endforeach
------------------------------------
@endforeach
                </textarea>

                <button class="btn btn-success mt-3" onclick="copyAndOpenWABuang()">
                    <i class="fab fa-whatsapp"></i> Copy & Open WhatsApp
                </button>
            </div>
        </div>
    </div>

    {{-- ================== E-COMMERCE (KANAN) ================== --}}
    <div class="col-md-6">
        <div class="card shadow mb-4 h-100">
            <div class="card-header bg-success text-white">
                <h5 class="m-0">WhatsApp Report E-Commerce (Auto)</h5>
            </div>

            <div class="card-body d-flex flex-column">
                <textarea id="waText" class="form-control flex-grow-1" rows="18" readonly>
@foreach($reports as $r)
{{ strtoupper($r['store']->name) }} Daily Report E-commerce {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
TOTAL QUANTITY : {{ $r['total_out'] }}

@foreach(
    $r['out']->groupBy(function ($o) {
        return $o->produk->color . ' ' . $o->produk->kategori->name;
    }) as $label => $items
)
- {{ $label }} : {{ $items->sum('quantity') }}
@endforeach

EKSPEDISI
@foreach($r['shipments'] as $s)
- {{ strtoupper($s->courier) }} : {{ $s->total }}
@endforeach

ADMIN
- PRESS : {{ $r['press'] }}
- RESI : {{ $r['resi'] }}
- PACKING : {{ $r['packing'] }}
- BUANG BENANG : {{ $r['buang'] }}

------------------------------------
@endforeach
                </textarea>

                <button class="btn btn-success mt-3" onclick="copyAndOpenWA()">
                    <i class="fab fa-whatsapp"></i> Copy & Open WhatsApp
                </button>
            </div>
        </div>
    </div>

</div>

@endsection

<script>

function copyAndOpenWABuang() {
    const text = document.getElementById('waBuangText').value;

    navigator.clipboard.writeText(text).then(() => {
        const encoded = encodeURIComponent(text);
        window.open(`https://wa.me/?text=${encoded}`, '_blank');
    }).catch(err => {
        alert('Gagal copy text');
        console.error(err);
    });
}

function copyAndOpenWA() {
    const text = document.getElementById('waText').value;

    navigator.clipboard.writeText(text).then(() => {
        const encoded = encodeURIComponent(text);
        window.open(`https://wa.me/?text=${encoded}`, '_blank');
    }).catch(err => {
        alert('Gagal copy text');
        console.error(err);
    });
}

</script>