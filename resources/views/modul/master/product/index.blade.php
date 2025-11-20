@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Product Management</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="close" data-dismiss="alert">×</button>
        </div>
    @endif
    
    {{-- Filter Produk --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="">
                <div class="row">

                    {{-- Filter Kategori --}}
                    <div class="col-md-3">
                        <label>Category</label>
                        <select name="kategori" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}"
                                    {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Warna --}}
                    <div class="col-md-3">
                        <label>Color</label>
                        <select name="color" class="form-control">
                            <option value="">-- Select Color --</option>
                            <option value="hitam" {{ request('color')=='hitam' ? 'selected' : '' }}>Hitam</option>
                            <option value="putih" {{ request('color')=='putih' ? 'selected' : '' }}>Putih</option>
                        </select>
                    </div>

                    {{-- Filter Size --}}
                    <div class="col-md-3">
                        <label>Size</label>
                        <select name="size" class="form-control">
                            <option value="">-- Select Size --</option>
                            <option value="s"  {{ request('size')=='s' ? 'selected' : '' }}>S</option>
                            <option value="m"  {{ request('size')=='m' ? 'selected' : '' }}>M</option>
                            <option value="l"  {{ request('size')=='l' ? 'selected' : '' }}>L</option>
                            <option value="xl" {{ request('size')=='xl' ? 'selected' : '' }}>XL</option>
                            <option value="xxl" {{ request('size')=='xxl' ? 'selected' : '' }}>XXL</option>
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary btn-block mr-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>

                        <a href="{{ route('product.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Product List</h6>
            @if (auth()->user()->role == 'admin')
                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createProductModal">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Category</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>SKU</th>
                            <th>Created</th>
                            <th>Updated</th>
                            @if (auth()->user()->role == 'admin')
                                <th width="15%">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($produk as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->kategori->name }}</td>
                            <td>{{ $p->color }}</td>
                            <td>{{ $p->size }}</td>
                            <td>{{ $p->sku ?? '-' }}</td>
                            <td>{{ $p->created_at }}</td>
                            <td>{{ $p->updated_at }}</td>
                            @if (auth()->user()->role == 'admin')
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                        data-toggle="modal"
                                        data-target="#editProductModal{{ $p->id_produk }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteProductModal"
                                        data-action="{{ route('product.destroy', $p->id_produk) }}"
                                        data-name="{{ $p->color }} - {{ $p->size }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                        @endforeach

                        @if($produk->count() == 0)
                        <tr>
                            <td colspan="8" class="text-muted">No products found.</td>
                        </tr>
                        @endif
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

@include('modul.master.product.modal-create')
@include('modul.master.product.modal-edit')
@include('modul.master.product.modal-delete')

@endsection