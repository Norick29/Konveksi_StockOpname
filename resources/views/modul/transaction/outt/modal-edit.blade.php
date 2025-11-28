@foreach($stok as $s)
<div class="modal fade" id="editStockOutModal{{ $s->id_stok_harian }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Stock OUT</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('stock-out.update', $s->id_stok_harian) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Qty --}}
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" value="{{ $s->quantity }}" min="1" class="form-control" required>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" value="{{ $s->transaction_date }}" class="form-control" required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group">
                        <label>Note</label>
                        <input type="text" name="note" value="{{ $s->note }}" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit"><i class="fas fa-save"></i> Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach