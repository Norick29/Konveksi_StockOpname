@foreach($shipments as $s)
<div class="modal fade" id="editShipmentModal{{ $s->id_ekspedisi }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Shipment</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form method="POST" action="{{ route('shipments.update', $s->id_ekspedisi) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            @foreach($toko as $t)
                                <option value="{{ $t->id_toko }}" 
                                    {{ $s->id_toko == $t->id_toko ? 'selected' : '' }}>
                                    {{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $s->date }}" required>
                    </div>

                    {{-- Courier --}}
                    <div class="form-group">
                        <label>Courier</label>
                        <input type="text" name="courier" class="form-control" 
                               value="{{ $s->courier }}" required>
                    </div>

                    {{-- Qty --}}
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" 
                               value="{{ $s->quantity }}" min="1" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
