<div class="modal fade" id="createOpeningModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Opening Stock</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form method="POST" action="{{ route('opening-stock.store') }}">
                @csrf

                <div class="modal-body px-4 py-3">

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            <option value="">Choose store</option>
                            @foreach($toko as $t)
                                <option value="{{ $t->id_toko }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Month --}}
                    <div class="form-group">
                        <label>Month</label>
                        <input type="month" name="month" class="form-control" required>
                    </div>

                    <hr class="my-3">

                    <label>Products</label>

                    <div id="openingItemsWrapper">
                        <div class="row mb-2 item-row opening-template">
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
                                <input type="number"
                                    name="items[0][quantity]"
                                    min="0"
                                    class="form-control"
                                    placeholder="Qty"
                                    required>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                        class="btn btn-sm btn-outline-primary mt-2"
                        id="addOpeningItem">
                        + Add Product
                    </button>

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
let openingIndex = 1;

document.getElementById('addOpeningItem').addEventListener('click', function () {
    const wrapper = document.getElementById('openingItemsWrapper');
    const template = wrapper.querySelector('.opening-template');

    const clone = template.cloneNode(true);

    clone.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${openingIndex}]`);
        el.value = '';
    });

    wrapper.appendChild(clone);
    openingIndex++;
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
