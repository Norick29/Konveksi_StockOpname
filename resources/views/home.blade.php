@extends('layouts.app')

@section('content')
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

            {{-- ===============================
            KPI CARDS
            =============================== --}}
            <div class="row">

                <div class="col-md-3 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Produk
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalProduk }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Toko
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $totalToko }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Stock IN Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stockInToday }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Stock OUT Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stockOutToday }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===============================
            TOTAL STOCK
            =============================== --}}
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow border-left-warning">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-warning">
                                Total Stock Available (FIFO Based)
                            </h6>
                            <h3 class="font-weight-bold">
                                {{ $totalStock }} pcs
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->role === 'owner')
                {{-- ===============================
                CHART STOCK IN vs OUT
                =============================== --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header font-weight-bold text-primary">
                                Stock Movement (Last 30 Days)
                            </div>
                            <div class="card-body">
                                <canvas id="stockChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ===============================
            TOP PRODUK & LOW STOCK
            =============================== --}}
            @php
                $isOwner = auth()->user()->role === 'owner';
            @endphp

            <div class="row">

                {{-- TOP PRODUK (Owner Only) --}}
                @if($isOwner)
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header font-weight-bold text-success">
                            Top 5 Produk Terlaris (Bulan Ini)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Total OUT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProduk as $tp)
                                    <tr>
                                        <td>{{ $tp->produk->sku }}</td>
                                        <td class="font-weight-bold">{{ $tp->total_out }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-muted">No data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- LOW STOCK (Dynamic Width) --}}
                <div class="{{ $isOwner ? 'col-md-6' : 'col-md-12' }} mb-4">
                    <div class="card shadow">
                        <div class="card-header font-weight-bold text-danger">
                            Low Stock Alert (≤ 50 pcs)
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Toko</th>
                                        <th>Produk</th>
                                        <th>Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lowStock as $ls)
                                    <tr class="{{ $ls->total_sisa <= 5 ? 'table-danger' : 'table-warning' }}">
                                        <td>{{ $ls->toko->name }}</td>
                                        <td>{{ $ls->produk->sku }}</td>
                                        <td class="font-weight-bold">{{ $ls->total_sisa }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-muted">Stock aman</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===============================
            LAST ACTIVITY
            =============================== --}}
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card shadow">
                        <div class="card-header font-weight-bold text-secondary">
                            Last Stock Activities
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Produk</th>
                                        <th>Toko</th>
                                        <th>Qty</th>
                                        <th>User</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lastActivity as $a)
                                    <tr>
                                        <td>
                                            <span class="badge 
                                                {{ $a->type == 'IN' ? 'badge-success' : ($a->type == 'OUT' ? 'badge-danger' : 'badge-warning') }}">
                                                {{ $a->type }}
                                            </span>
                                        </td>
                                        <td>{{ $a->produk->sku }}</td>
                                        <td>{{ $a->toko->name }}</td>
                                        <td>{{ $a->quantity }}</td>
                                        <td>{{ $a->user->name }}</td>
                                        <td>{{ $a->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===============================
        CHART SCRIPT
        =============================== --}}
        @if(auth()->user()->role === 'owner')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const rawData = @json($chartData);

                const labels = [...new Set(rawData.map(d => d.date))];

                const inData = labels.map(date => {
                    const found = rawData.find(d => d.date === date && d.type === 'IN');
                    return found ? found.total : 0;
                });

                const outData = labels.map(date => {
                    const found = rawData.find(d => d.date === date && d.type === 'OUT');
                    return found ? found.total : 0;
                });

                new Chart(document.getElementById('stockChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Stock IN',
                                data: inData,
                                borderColor: 'green',
                                fill: false,
                            },
                            {
                                label: 'Stock OUT',
                                data: outData,
                                borderColor: 'red',
                                fill: false,
                            }
                        ]
                    }
                });
            </script>
        @endif
@endsection