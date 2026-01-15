<div class="modal fade" id="createStockOutModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Stock OUT</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('stock-out.store') }}" method="POST">
                @csrf

                {{-- ERROR VALIDATION --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div class="modal-body px-4 py-3">

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Choose store</option>
                            @foreach($toko as $tk)
                                <option value="{{ $tk->id_toko }}">{{ $tk->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="transaction_date" class="form-control" required>
                    </div>

                    <hr class="my-3">

                    <label>Products</label>

                    <div id="itemsWrapper">
                        <div class="row mb-2 item-row" id="itemTemplate">
                            <div class="col-md-7">
                                <select name="items[0][id_produk]" class="form-control" required>
                                    <option value="">Pilih Produk</option>

                                    @php
                                        $sizeOrder = ['S','M','L','XL','XXL'];
                                    @endphp

                                    @foreach($produk->groupBy('kategori.name') as $kategori => $itemsKategori)
                                        <optgroup label="{{ $kategori }}">

                                            @foreach($itemsKategori->groupBy('color') as $color => $itemsColor)

                                                @foreach(
                                                    $itemsColor->sortBy(fn($p) => array_search($p->size, $sizeOrder))
                                                    as $p
                                                )
                                                    <option value="{{ $p->id_produk }}">
                                                        {{ $color }} | Size {{ $p->size }} ({{ $p->sku }})
                                                    </option>
                                                @endforeach

                                            @endforeach

                                        </optgroup>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <input
                                    type="number"
                                    name="items[0][quantity]"
                                    min="1"
                                    class="form-control"
                                    placeholder="Qty"
                                    required
                                >
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary mt-2"
                        id="addItem">
                        + Add Product
                    </button>

                    <div class="form-group mt-3">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control" placeholder="Optional">
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


<script>
let itemIndex = 1;

document.getElementById('addItem').addEventListener('click', function () {
    const wrapper = document.getElementById('itemsWrapper');
    const template = document.getElementById('itemTemplate');

    const clone = template.cloneNode(true);

    clone.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        el.value = '';
    });

    wrapper.appendChild(clone);
    itemIndex++;
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
        }
    }
});
</script>



