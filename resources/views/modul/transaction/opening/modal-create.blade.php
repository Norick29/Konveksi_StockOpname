<div class="modal fade" id="createOpeningModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Opening Stock</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form action="{{ route('opening-stock.store') }}" method="POST">
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

                    {{-- Month --}}
                    <div class="form-group">
                        <label>Month</label>
                        <input type="month" class="form-control" name="month" required>
                    </div>

                    {{-- Quantity --}}
                    <div class="form-group">
                        <label>Opening Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
