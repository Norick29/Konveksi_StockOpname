@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-3 text-gray-800">Activity Log</h1>

    {{-- Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row">

                    <div class="col-md-3">
                        <label>User ID</label>
                        <input type="text" name="user_id"
                            value="{{ request('user_id') }}"
                            class="form-control"
                            placeholder="User ID">
                    </div>

                    <div class="col-md-3">
                        <label>Action</label>
                        <select name="action" class="form-control">
                            <option value="">All Actions</option>
                            <option value="CREATE" {{ request('action')=='CREATE'?'selected':'' }}>CREATE</option>
                            <option value="UPDATE" {{ request('action')=='UPDATE'?'selected':'' }}>UPDATE</option>
                            <option value="DELETE" {{ request('action')=='DELETE'?'selected':'' }}>DELETE</option>
                            <option value="OPENING_STOCK" {{ request('action')=='OPENING_STOCK'?'selected':'' }}>OPENING</option>
                            <option value="STOCK_IN" {{ request('action')=='STOCK_IN'?'selected':'' }}>STOCK IN</option>
                            <option value="STOCK_OUT" {{ request('action')=='STOCK_OUT'?'selected':'' }}>STOCK OUT</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>IP</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $logs->firstItem() + $loop->index }}</td>
                            <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                            <td>{{ $log->user->name ?? '-' }}</td>

                            <td>
                                <span class="badge badge-info">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td>{{ $log->module }}</td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address }}</td>

                            <td>
                                @if($log->data)
                                <button class="btn btn-sm btn-outline-primary"
                                    data-toggle="modal"
                                    data-target="#detailModal{{ $log->id }}">
                                    View
                                </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-muted">No activity found.</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        </div>
    </div>

</div>

{{-- MODAL DETAIL --}}
@foreach($logs as $log)
@if($log->data)
<div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Activity Detail</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <pre class="bg-light p-3">
{{ json_encode(json_decode($log->data), JSON_PRETTY_PRINT) }}
                </pre>
            </div>

        </div>
    </div>
</div>
@endif
@endforeach

@endsection
