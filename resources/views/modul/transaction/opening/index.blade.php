@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">Opening Stock</h1>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button class="close" type="button" data-dismiss="alert">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

 {{-- Filter Opening Stock --}}
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <div class="row">

                {{-- Filter Month --}}
                <div class="col-md-4">
                    <label>Select Month</label>
                    <input type="month"
                        name="month"
                        value="{{ request('month') }}"
                        class="form-control">
                </div>

                {{-- Filter Store --}}
                <div class="col-md-4">
                    <label>Select Store</label>
                    <select name="id_toko" class="form-control">
                        <option value="">All Stores</option>
                        @foreach ($toko as $t)
                            <option value="{{ $t->id_toko }}"
                                {{ request('id_toko') == $t->id_toko ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block mr-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>

                    <a href="{{ route('opening-stock.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-undo"></i> 
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>


{{-- Stock Table --}}
<div class="card shadow mb-4">

    {{-- Header list + tombol add sejajar --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Stock List</h6>

        @if(auth()->check() && auth()->user()->role == 'admin')
            <button class="btn btn-primary btn-sm"
                data-toggle="modal" data-target="#createOpeningModal">
                <i class="fas fa-plus"></i> Add Opening Stock
            </button>
        @endif
    </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Product</th>
                            <th>Store</th>
                            <th>Month</th>
                            <th>Opening Stock</th>
                            <th>Created At</th>
                            @if(auth()->user()->role == 'admin')
                                <th width="15%">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($stok as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->produk->sku }}</td>
                            <td>{{ $s->toko->name }}</td>
                            <td>{{ $s->month }}</td>
                            <td>{{ $s->quantity }}</td>
                            <td>{{ $s->created_at }}</td>

                            @if(auth()->user()->role == 'admin')
                            <td>
                                <button class="btn btn-warning btn-sm" 
                                        data-toggle="modal"
                                        data-target="#editOpeningModal{{ $s->id_stok_bulan }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-action="{{ route('opening-stock.destroy', $s->id_stok_bulan) }}"
                                        data-name="{{ $s->produk->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-muted">No data available</td>
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
                        {{ $stok->links('pagination::bootstrap-4') }}
                    </div>
                </div>    

            </div>
        </div>
    </div>

</div>

{{-- Import Modal --}}
@include('modul.transaction.opening.modal-create')
@include('modul.transaction.opening.modal-edit')
@include('modul.transaction.opening.modal-delete')

@endsection
