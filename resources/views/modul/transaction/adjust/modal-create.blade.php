<div class="modal fade" id="createAdjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Stock Adjustment</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('stock-adjust.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Product --}}
                  <div class="form-group">
    <label>Product</label>
    <select name="id_produk" class="form-control" required>
        <option value="">Select Product</option>

        @php
            $sizeOrder = ['S','M','L','XL','XXL'];
        @endphp

        @foreach($produk->groupBy('kategori.name') as $kategori => $itemsKategori)

            @foreach($itemsKategori->groupBy('color') as $color => $itemsColor)

                {{-- OPTGROUP = KATEGORI + WARNA --}}
                <optgroup label="{{ $kategori }} ({{ $color }})">

                    @foreach(
                        $itemsColor->sortBy(fn($p) => array_search($p->size, $sizeOrder))
                        as $p
                    )
                        <option value="{{ $p->id_produk }}">
                            Size {{ $p->size }} ({{ $p->sku }})
                        </option>
                    @endforeach

                </optgroup>

            @endforeach

        @endforeach

    </select>
</div>


                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Select Store</option>
                            @foreach($toko as $tk)
                            <option value="{{ $tk->id_toko }}">{{ $tk->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Adjust Type --}}
                    <div class="form-group">
                        <label>Adjustment Type</label>
                        <select name="adjust_type" class="form-control" required>
                            <option value="">Select Type</option>
                            <option value="IN">IN (+) Add Stock</option>
                            <option value="OUT">OUT (–) Reduce Stock</option>
                        </select>
                    </div>

                    {{-- Quantity --}}
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min="1" class="form-control" required>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" class="form-control" required>
                    </div>

                    {{-- Note --}}
                    <div class="form-group">
                        <label>Note (Optional)</label>
                        <input type="text" name="note" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>