@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Daily Report</h1>

    <div class="card shadow">
        <div class="card-body">

            <form action="{{ route('daily-report.generate') }}" method="POST">
                @csrf

                <div class="row">

                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Choose Store</option>
                            @foreach($toko as $t)
                                <option value="{{ $t->id_toko }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <hr>

                <h5>Admin</h5>

                <div class="row">
                    <div class="col-md-3">
                        <label>PRESS Admin</label>
                        <input type="text" name="press_admin" class="form-control" placeholder="e.g. Sena, Ageng">
                    </div>

                    <div class="col-md-3">
                        <label>RESI Admin</label>
                        <input type="text" name="resi_admin" class="form-control" placeholder="e.g. Abay, Ipal">
                    </div>

                    <div class="col-md-3">
                        <label>PACKING Admin</label>
                        <input type="text" name="packing_admin" class="form-control" placeholder="e.g. Abay, Ageng">
                    </div>

                    <div class="col-md-3">
                        <label>Buang Benang Admin</label>
                        <input type="text" name="buang_admin" class="form-control" placeholder="e.g. Anjani, Andini">
                    </div>
                </div>

                <hr>

                <button class="btn btn-primary mt-2">
                    <i class="fas fa-file-alt"></i> Generate Report
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
