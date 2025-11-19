@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">Categories Management</h1> 

    {{-- Succes Massage --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Categories List</h6>
            @if (auth()->user()->role == 'admin')
                <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createCategoriesModal">
                    <i class="fas fa-user-plus"></i> Add Categories
                </a>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            @if (auth()->user()->role == 'admin')
                                <th width="15%">Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($kategori as $kat)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $kat->name }}</td>
                            <td>{{ $kat->created_at }}</td>
                            <td>{{ $kat->updated_at }}</td>
                            @if (auth()->user()->role == 'admin')
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editCategoriesModal{{ $kat->id_kategori }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <button type="button"
                                        class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-action="{{ route('categories.destroy', $kat->id_kategori) }}"
                                        data-name="{{ $kat->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                        @endforeach

                        @if ($kategori->count() == 0)
                        <tr>
                            <td colspan="5" class="text-center text-muted">No store found.</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('modul.master.categories.modal-create')
@include('modul.master.categories.modal-edit')
@include('modul.master.categories.modal-delete')

@endsection