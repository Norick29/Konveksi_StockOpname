@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Daily Report — {{ $date }}</h1>

    {{-- SECTION 1: LAPORAN BUANG BENANG --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="m-0">Buang Benang (Stock IN)</h5>
        </div>

        <div class="card-body">
            @forelse($in as $i)
                <p>{{ $i->produk->sku }} = {{ $i->quantity }} pcs</p>
            @empty
                <p class="text-muted">No Stock IN data.</p>
            @endforelse
        </div>
    </div>

    {{-- SECTION 2: DAILY REPORT E-COMMERCE --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="m-0">E-Commerce (Stock OUT)</h5>
        </div>

        <div class="card-body">
            @php $total = $out->sum('quantity'); @endphp

            <strong>Total Qty: {{ $total }} pcs</strong>

            <hr>

            @forelse($out as $o)
                <p>{{ $o->produk->sku }} = {{ $o->quantity }} pcs</p>
            @empty
                <p class="text-muted">No Stock OUT data.</p>
            @endforelse
        </div>
    </div>

    {{-- SECTION 3: SHIPMENTS --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0">Shipments</h5>
        </div>

        <div class="card-body">
            @forelse($shipments as $s)
                <p>{{ $s->courier }} : {{ $s->quantity }} pcs</p>
            @empty
                <p class="text-muted">No shipment data.</p>
            @endforelse
        </div>
    </div>

    {{-- SECTION 4: ADMIN --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="m-0">Admin List</h5>
        </div>

        <div class="card-body">
            <p><strong>Press:</strong> {{ $press ?? '-' }}</p>
            <p><strong>Resi:</strong> {{ $resi ?? '-' }}</p>
            <p><strong>Packing:</strong> {{ $packing ?? '-' }}</p>
            <p><strong>Buang Benang:</strong> {{ $buang ?? '-' }}</p>
        </div>
    </div>

</div>
@endsection
