@extends('layouts.app')

@section('content')
@php
    $role = auth()->user()->role;
@endphp

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Daily Report
        @if($role === 'owner')
            <span class="badge badge-info ml-2">View Only</span>
        @endif
    </h1>

    <div class="card shadow">
        <div class="card-body">

            <form action="{{ route('daily-report.generate') }}" method="POST">
                @csrf

                {{-- FILTER (ADMIN & OWNER) --}}
                <div class="row">

                    <div class="col-md-3">
                        <label>Date</label>
                        <input
                            type="date"
                            name="date"
                            class="form-control"
                            id="date"
                            onchange="loadOutSummary()"
                            required
                        >
                    </div>

                    <div class="col-md-3">
                        <label>Store</label>

                        @foreach($toko as $t)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="id_toko{{ $t->id_toko }}"
                                    name="id_toko[]"
                                    value="{{ $t->id_toko }}"
                                    onchange="loadOutSummary()"
                                >
                                <label class="custom-control-label" for="id_toko{{ $t->id_toko }}">
                                    {{ $t->name }}
                                </label>
                            </div>
                        @endforeach

                        <small class="text-muted">Pilih satu atau lebih toko</small>
                    </div>

                </div>

                <hr>

                {{-- PATOKAN OUT (ADMIN ONLY)
                @if($role === 'admin')
                <div class="alert alert-info d-none" id="outInfo">
                    <h6>Patokan Stok Keluar</h6>
                    <div id="outContent"></div>
                </div>
                <hr>
                @endif --}}

                {{-- SHIPMENT (ADMIN ONLY) --}}
                @if($role === 'admin')
                <h5>Shipments</h5>

                <div id="shipmentWrapper">
                    <div class="row mb-2 shipment-row align-items-center">
                        <div class="col-md-3">
                            <select name="shipments[0][expedition]" class="form-control">
                                <option value="">-- Pilih Ekspedisi --</option>
                                <option value="SPX">SPX</option>
                                <option value="JNT">JNT</option>
                                <option value="JNE">JNE</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="number"
                                name="shipments[0][qty]"
                                class="form-control"
                                placeholder="Jumlah"
                                min="1">
                        </div>

                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-shipment">✕</button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" id="addShipment">
                    + Add Shipment
                </button>

                <hr>
                @endif

                {{-- ADMIN NAME (ADMIN ONLY) --}}
                @if($role === 'admin')
                <h5>Admin</h5>

                <div class="row">
                    <div class="col-md-3">
                        <label>PRESS Admin</label>
                        <input type="text" name="press_admin" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>RESI Admin</label>
                        <input type="text" name="resi_admin" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>PACKING Admin</label>
                        <input type="text" name="packing_admin" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Buang Benang Admin</label>
                        <input type="text" name="buang_admin" class="form-control">
                    </div>
                </div>

                <hr>
                @endif

                {{-- SUBMIT --}}
                <button class="btn btn-primary">
                    @if($role === 'admin')
                        <i class="fas fa-file-alt"></i> Generate Report
                    @else
                        <i class="fas fa-eye"></i> Lihat Laporan
                    @endif
                </button>

            </form>

        </div>
    </div>

</div>

{{-- JS ADMIN ONLY --}}
@if($role === 'admin')
<script>
document.addEventListener('DOMContentLoaded', function () {

/* ===============================
   GLOBAL STATE
================================ */
let shipIndex = 1;
let totalOut = 0;

/* ===============================
   ADD SHIPMENT
================================ */
document.getElementById('addShipment').addEventListener('click', function () {
    const wrapper = document.getElementById('shipmentWrapper');
    const row = wrapper.querySelector('.shipment-row').cloneNode(true);

    row.querySelectorAll('input, select').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${shipIndex}]`);
        el.value = '';
    });

    wrapper.appendChild(row);
    shipIndex++;
});

/* ===============================
   REMOVE SHIPMENT
================================ */
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-shipment')) {
        const rows = document.querySelectorAll('.shipment-row');
        if (rows.length > 1) {
            e.target.closest('.shipment-row').remove();
            renderOutInfo();
        }
    }
});

/* ===============================
   LOAD PATOKAN OUT
================================ */
// function loadOutSummary() {
//     const date = document.getElementById('date').value;
//     const tokoChecked = document.querySelectorAll('input[name="id_toko[]"]:checked');

//     if (!date || tokoChecked.length !== 1) {
//         document.getElementById('outInfo').classList.add('d-none');
//         return;
//     }

//     fetch('{{ route("daily-report.out-summary") }}', {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': '{{ csrf_token() }}',
//             'Content-Type': 'application/json'
//         },
//         body: JSON.stringify({
//             date: date,
//             id_toko: [tokoChecked[0].value]
//         })
//     })
//     .then(res => res.json())
//     .then(data => {
//         totalOut = data.length ? data[0].total_out : 0;
//         renderOutInfo();
//         document.getElementById('outInfo').classList.remove('d-none');
//     })
//     .catch(() => alert('Gagal memuat patokan stok OUT'));
// }

/* ===============================
   HITUNG SHIPMENT
================================ */
function calculateShipmentTotal() {
    let total = 0;
    document.querySelectorAll('input[name$="[qty]"]').forEach(input => {
        total += Number(input.value || 0);
    });
    return total;
}

/* ===============================
   RENDER PATOKAN
================================ */
function renderOutInfo() {
    const shipmentTotal = calculateShipmentTotal();
    const remaining = totalOut - shipmentTotal;

    document.getElementById('outContent').innerHTML = `
        <strong>Total OUT :</strong> ${totalOut}<br>
        <strong>Total Shipment :</strong> ${shipmentTotal}<br>
        <strong>Sisa :</strong>
        <span class="${remaining < 0 ? 'text-danger' : 'text-success'}">
            ${remaining}
        </span>
    `;
}

/* ===============================
   EVENT BINDING
================================ */
document.getElementById('date').addEventListener('change', loadOutSummary);

document.querySelectorAll('input[name="id_toko[]"]').forEach(cb => {
    cb.addEventListener('change', loadOutSummary);
});

document.addEventListener('input', function (e) {
    if (e.target.name && e.target.name.endsWith('[qty]')) {
        renderOutInfo();
    }
});

/* ===============================
   VALIDASI SUBMIT
================================ */
document.querySelector('form').addEventListener('submit', function (e) {
    if (calculateShipmentTotal() > totalOut) {
        e.preventDefault();
        alert('Jumlah shipment melebihi stok OUT!');
    }
});

});
</script>
@endif

@endsection

