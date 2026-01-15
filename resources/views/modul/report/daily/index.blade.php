{{-- @extends('layouts.app')

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
                        <input type="date" name="date" class="form-control" id="date" onchange="loadOutSummary()" required>
                    </div>

                    <div class="col-md-3">
                        <label>Store</label>

                        @foreach($toko as $t)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox"
                                    class="custom-control-input"
                                    id="id_toko{{ $t->id_toko }}"
                                    name="id_toko[]"
                                    value="{{ $t->id_toko }}" >
                                <label class="custom-control-label" for="id_toko{{ $t->id_toko }}">
                                    {{ $t->name }}
                                </label>
                            </div>
                        @endforeach

                        <small class="text-muted">Pilih satu atau lebih toko</small>
                    </div>

                </div>

                <hr>

                <div class="alert alert-info d-none" id="outInfo">
                    <h6>Patokan Stok Keluar</h6>
                    <div id="outContent"></div>
                </div>

                <hr>
                
                <h5>Shipments</h5>

                <div id="shipmentWrapper">
                    <div class="row mb-2 shipment-row align-items-center">
                        <!-- Ekspedisi -->
                        <div class="col-md-3">
                            <select name="shipments[0][expedition]" class="form-control">
                                <option value="">-- Pilih Ekspedisi --</option>
                                <option value="SPX">SPX</option>
                                <option value="JNT">JNT</option>
                                <option value="JNE">JNE</option>
                            </select>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-md-2">
                            <input type="number"
                                name="shipments[0][qty]"
                                class="form-control"
                                placeholder="Jumlah"
                                min="1">
                        </div>

                        <!-- Tombol Hapus -->
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-shipment">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-sm btn-outline-primary" id="addShipment">
                    + Add Shipment
                </button>

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

<script>
let shipIndex = 1;

// ADD SHIPMENT
document.getElementById('addShipment').addEventListener('click', function () {
    const wrapper = document.getElementById('shipmentWrapper');
    const row = wrapper.querySelector('.shipment-row').cloneNode(true);

    // Update input & select
    row.querySelectorAll('input, select').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${shipIndex}]`);
        el.value = '';
    });

    wrapper.appendChild(row);
    shipIndex++;
});

// REMOVE SHIPMENT (event delegation)
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-shipment')) {
        const rows = document.querySelectorAll('.shipment-row');
        if (rows.length > 1) {
            e.target.closest('.shipment-row').remove();
        } else {
            alert('Minimal harus ada satu shipment.');
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function loadOutSummary() {
        const date = document.querySelector('input[name="date"]').value;

        const tokoChecked = document.querySelectorAll(
            'input[name="id_toko[]"]:checked'
        );

        if (!date || tokoChecked.length === 0) {
            document.getElementById('outInfo').classList.add('d-none');
            return;
        }

        const toko = Array.from(tokoChecked).map(cb => cb.value);

        fetch('{{ route("daily-report.out-summary") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                id_toko: toko
            })
        })
        .then(res => {
            if (!res.ok) throw new Error('Request gagal');
            return res.json();
        })
        .then(data => {
            let html = '';

            data.forEach(r => {
                html += `<strong>${r.toko}</strong><br>`;
                html += `Total OUT: <b>${r.total_out}</b><ul>`;

                for (const [cat, qty] of Object.entries(r.by_category)) {
                    html += `<li>${cat} : ${qty}</li>`;
                }

                html += '</ul><hr>';
            });

            document.getElementById('outContent').innerHTML = html;
            document.getElementById('outInfo').classList.remove('d-none');
        })
        .catch(err => {
            console.error(err);
            alert('Gagal memuat patokan stok OUT');
        });
    }

    // 🔥 TRIGGER YANG BENAR
    document.querySelectorAll('input[name="id_toko[]"]').forEach(cb => {
        cb.addEventListener('change', loadOutSummary);
    });

    document.querySelector('input[name="date"]').addEventListener('change', loadOutSummary);

});
</script>
@endsection --}}

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

                {{-- PATOKAN OUT (ADMIN ONLY) --}}
                @if($role === 'admin')
                <div class="alert alert-info d-none" id="outInfo">
                    <h6>Patokan Stok Keluar</h6>
                    <div id="outContent"></div>
                </div>
                <hr>
                @endif

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
function loadOutSummary() {
    const date = document.getElementById('date').value;
    const tokoChecked = document.querySelectorAll('input[name="id_toko[]"]:checked');

    if (!date || tokoChecked.length !== 1) {
        document.getElementById('outInfo').classList.add('d-none');
        return;
    }

    fetch('{{ route("daily-report.out-summary") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            date: date,
            id_toko: [tokoChecked[0].value]
        })
    })
    .then(res => res.json())
    .then(data => {
        totalOut = data.length ? data[0].total_out : 0;
        renderOutInfo();
        document.getElementById('outInfo').classList.remove('d-none');
    })
    .catch(() => alert('Gagal memuat patokan stok OUT'));
}

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

