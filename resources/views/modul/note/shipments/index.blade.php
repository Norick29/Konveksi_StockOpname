@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Shipments</h1>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button class="close" data-dismiss="alert">×</button>
    </div>
    @endif

    {{-- FILTER --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form class="row" method="GET">

                <div class="col-md-3">
                    <label>Date</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Store</label>
                    <select name="id_toko" class="form-control">
                        <option value="">All Stores</option>
                        @foreach($toko as $t)
                            <option value="{{ $t->id_toko }}" {{ request('id_toko') == $t->id_toko ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filter</button>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('shipments.index') }}" class="btn btn-secondary btn-block"><i class="fas fa-undo"></i></a>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Shipments List</h6>

            @if(auth()->user()->role == 'admin')
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createShipmentModal">
                <i class="fas fa-plus"></i> Add Shipment
            </button>
            @endif
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered text-center">

                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Store</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Courier</th>
                            <th>Quantity</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            @if(auth()->user()->role == 'admin')
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($shipments as $s)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->toko->name }}</td>
                            <td>{{ $s->user->name }}</td>
                            <td>{{ $s->date }}</td>
                            <td>{{ $s->courier }}</td>
                            <td>{{ $s->quantity }}</td>
                            <td>{{ $s->created_at }}</td>
                            <td>{{ $s->updated_at }}</td>
                            @if(auth()->user()->role == 'admin')
                            <td>
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editShipmentModal{{ $s->id_ekspedisi }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteShipmentModal"
                                        data-action="{{ route('shipments.destroy', $s->id_ekspedisi) }}"
                                        data-name="{{ $s->courier }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-muted">No data</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@include('modul.note.shipments.modal-create')
@include('modul.note.shipments.modal-edit')
@include('modul.note.shipments.modal-delete')

@endsection
