<div class="modal fade" id="createStockOutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Stock OUT</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('stock-out.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Product --}}
                    <div class="form-group">
                        <label>Product</label>
                        <select name="id_produk" class="form-control" required>
                            <option value="">Select product</option>
                            @foreach($produk as $p)
                                <option value="{{ $p->id_produk }}">{{ $p->sku }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Select store</option>
                            @foreach($toko as $tk)
                                <option value="{{ $tk->id_toko }}">{{ $tk->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Qty --}}
                    <div class="form-group">
                        <label>Quantity OUT</label>
                        <input type="number" name="quantity" min="1" class="form-control" required>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" class="form-control" required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control" placeholder="Optional">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>