@foreach($stok as $s)
<div class="modal fade" id="editStockInModal{{ $s->id_stok_harian }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Stock IN</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form method="POST" action="{{ route('stock-in.update', $s->id_stok_harian) }}">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Product (disabled) --}}
                    <div class="form-group">
                        <label>Product</label>
                        <input type="text" class="form-control" 
                               value="{{ $s->produk->sku }}" disabled>
                    </div>

                    {{-- Store (disabled) --}}
                    <div class="form-group">
                        <label>Store</label>
                        <input type="text" class="form-control" 
                               value="{{ $s->toko->name }}" disabled>
                    </div>

                    {{-- Quantity --}}
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" min="1"
                               name="quantity"
                               class="form-control"
                               value="{{ $s->quantity }}"
                               required>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date"
                               name="transaction_date"
                               class="form-control"
                               value="{{ $s->transaction_date }}"
                               required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group">
                        <label>Note</label>
                        <input type="text"
                               name="note"
                               class="form-control"
                               value="{{ $s->note }}">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach