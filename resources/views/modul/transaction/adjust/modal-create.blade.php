<div class="modal fade" id="createAdjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Add Stock Adjustment</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('stock-adjust.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <label>Product</label>
                        <select name="id_produk" class="form-control" required>
                            <option value="">Select Product</option>
                            @foreach($produk as $p)
                            <option value="{{ $p->id_produk }}">{{ $p->sku }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Select Store</option>
                            @foreach($toko as $tk)
                            <option value="{{ $tk->id_toko }}">{{ $tk->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity Adjustment</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Note (Optional)</label>
                        <input type="text" name="note" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>