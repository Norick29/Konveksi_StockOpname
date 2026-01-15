@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Stock Adjust</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="close" data-dismiss="alert">×</button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" 
                               name="date" 
                               value="{{ request('date') }}" 
                               class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Store</label>
                        <select name="id_toko" class="form-control">
                            <option value="">All Stores</option>
                            @foreach($toko as $tk)
                                <option value="{{ $tk->id_toko }}" 
                                    {{ request('id_toko') == $tk->id_toko ? 'selected' : '' }}>
                                    {{ $tk->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Product</label>
                        <select name="id_produk" class="form-control">
                            <option value="">All Products</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id_produk }}" 
                                    {{ request('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                    {{ $p->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <a href="{{ route('stock-adjust.index') }}" 
                           class="btn btn-secondary btn-block">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Stock Adjust List</h6>

            @if(auth()->user()->role == 'admin')
            <button class="btn btn-primary btn-sm" 
                    data-toggle="modal" 
                    data-target="#createAdjustModal">
                <i class="fas fa-plus"></i> Adjust Stock
            </button>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Store</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Date</th>
                            <th>Note</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($stok as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->produk->sku }}</td>
                            <td>{{ $s->toko->name }}</td>

                            {{-- ADJUST TYPE --}}
                            <td>
                                @if($s->adjust_type == 'IN')
                                    <span class="badge badge-success">IN (+)</span>
                                @else
                                    <span class="badge badge-danger">OUT (-)</span>
                                @endif
                            </td>

                            <td>{{ $s->quantity }}</td>
                            <td>{{ $s->transaction_date }}</td>
                            <td>{{ $s->note ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-muted">No data found</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $stok->firstItem() }} to {{ $stok->lastItem() }}
                        of {{ $stok->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $stok->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@include('modul.transaction.adjust.modal-create')
{{-- @include('modul.transaction.adjust.modal-edit')
@include('modul.transaction.adjust.modal-delete') --}}

@endsection