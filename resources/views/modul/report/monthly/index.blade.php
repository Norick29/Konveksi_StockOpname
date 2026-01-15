@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h3 class="mb-4">Monthly Stock Report — {{ $month }}</h3>

    {{-- Filter --}}
    <form class="row mb-3">
        <div class="col-md-3">
            <label>Month</label>
            <input type="month" name="month" value="{{ $month }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label>Store</label>
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

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary btn-block">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <a href="{{ route('monthly-report.index') }}" class="btn btn-secondary btn-block">
                <i class="fas fa-undo"></i> Reset
            </a>
        </div>

        <div class="col-md-3 d-flex align-items-end mt-3">
            <a href="{{ route('report.monthly.export', ['month' => $month, 'id_toko' => request('id_toko')]) }}"
                class="btn btn-success">
                Export Excel
            </a>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-striped text-center">
                <thead class="thead-dark">
                    <tr>
                        <th>Store</th>
                        <th>SKU</th>
                        <th>Opening</th>
                        <th>IN</th>
                        <th>Adjust IN</th>
                        <th>OUT</th>
                        <th>Adjust OUT</th>
                        <th>Closing</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($report as $r)
                    <tr>
                        <td>{{ $r['toko'] }}</td>
                        <td>{{ $r['produk'] }}</td>
                        <td>{{ $r['opening'] }}</td>
                        <td class="text-success font-weight-bold">{{ $r['in'] }}</td>
                        <td class="text-info">{{ $r['adjust_in'] }}</td>
                        <td class="text-danger font-weight-bold">{{ $r['out'] }}</td>
                        <td class="text-warning">{{ $r['adjust_out'] }}</td>
                        <td class="font-weight-bold">{{ $r['closing'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-muted">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $report->firstItem() }} to {{ $report->lastItem() }}
                        of {{ $report->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $report->links('pagination::bootstrap-4') }}
                    </div>
                </div> 
        </div>
    </div>

</div>
@endsection
