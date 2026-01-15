@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- =========================
        TITLE
    ========================== --}}
    <h1 class="h3 mb-4 text-gray-800">Monitoring Stok</h1>

    {{-- =========================
        SUMMARY CARDS
    ========================== --}}
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card shadow border-left-primary">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary">TOTAL STOK</div>
                    <div class="h5 font-weight-bold">{{ $totalStock }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow border-left-success">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success">TOTAL PRODUK</div>
                    <div class="h5 font-weight-bold">{{ $totalProduk }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow border-left-warning">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning">TOKO BERMASALAH</div>
                    <div class="h5 font-weight-bold">{{ $tokoBermasalah }}</div>
                </div>
            </div>
        </div>

    </div>

    {{-- =========================
        FILTER PER TOKO
    ========================== --}}
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Monitoring per Toko</h6>
        </div>

        <div class="card-body">

            <form method="GET">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select name="id_toko" class="form-control" onchange="this.form.submit()">
                            <option value="">-- Pilih Toko --</option>
                            @foreach(\App\Models\Toko::orderBy('name')->get() as $t)
                                <option value="{{ $t->id_toko }}"
                                    {{ request('id_toko') == $t->id_toko ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Produk</th>
                            <th>Sisa Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokPerToko as $s)
                        <tr>
                            <td>{{ $s->produk->sku }}</td>
                            <td>{{ $s->total_sisa }}</td>
                            <td>
                                @if($s->total_sisa <= 5)
                                    <span class="badge badge-danger">Kritis</span>
                                @elseif($s->total_sisa <= 10)
                                    <span class="badge badge-warning">Waspada</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-muted">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

<script>
setInterval(function () {
    fetch('/owner/low-stock-alert')
        .then(res => res.json())
        .then(data => {

            let badge = document.querySelector('.badge-counter');
            if (!badge) return;

            badge.innerText = data.length > 0 ? data.length : '';

        });
}, 30000); // 30 detik
</script>