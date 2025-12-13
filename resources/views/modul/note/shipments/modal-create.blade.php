<div class="modal fade" id="createShipmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Add Shipment</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form method="POST" action="{{ route('shipments.store') }}">
                @csrf

                <div class="modal-body">

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Select store</option>
                            @foreach($toko as $t)
                                <option value="{{ $t->id_toko }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>

                    {{-- Courier --}}
                    <div class="form-group">
                        <label>Courier</label>
                        <select name="courier" class="form-control">
                            <option value="">Select courier</option>
                            <option value="SPX">SPX</option>
                            <option value="JNT">JNT</option>
                            <option value="JNE">JNE</option>
                            <option value="SiCepat">SiCepat</option>
                        </select>
                        {{-- <input type="text" name="courier" 
                               class="form-control"
                               placeholder="Enter courier name"
                               required> --}}
                    </div>

                    {{-- Qty --}}
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
