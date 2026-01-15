@foreach($produk as $p)
<div class="modal fade" id="editProductModal{{ $p->id_produk }}">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Product</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('product.update', $p->id_produk) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="form-group">
                        <label>Category</label>
                        <select name="id_kategori" id="categorySelect{{ $p->id_produk }}" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($kategori as $k)
                                <option value="{{ $k->id_kategori }}" 
                                    data-code="{{ $k->code ?? strtoupper(substr($k->name,0,3)) }}"
                                    {{ $p->id_kategori == $k->id_kategori ? 'selected' : '' }}>
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Color</label>
                        <select name="color" id="colorSelect{{ $p->id_produk }}" class="form-control" required>
                            <option value="">-- Select Color --</option>
                            {{-- Ambil color dari produk --}}
                            @foreach ($produk as $pr)
                                <option value="{{ $pr->color }}"
                                    {{ $pr->color == $pr->color ? 'selected' : '' }}>
                                    {{ $pr->color }}
                                </option>
                            @endforeach
                            <option value="Hitam" {{ $p->color == 'Hitam' ? 'selected' : '' }}>Hitam</option>
                            <option value="Putih"  {{ $p->color == 'Putih' ? 'selected' : '' }}>Putih</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Size</label>
                        <select name="size" id="sizeSelect{{ $p->id_produk }}" class="form-control" required>
                            <option value="">-- Select Size --</option>
                            @foreach ($produk as $pr)
                                <option value="{{ $pr->size }}"
                                    {{ $pr->size == $pr->size ? 'selected' : '' }}>
                                    {{ $pr->size }}
                                </option>
                            @endforeach
                            <option value="S"   {{ $p->size == 'S' ? 'selected' : '' }}>S</option>
                            <option value="M"   {{ $p->size == 'M' ? 'selected' : '' }}>M</option>
                            <option value="L"   {{ $p->size == 'L' ? 'selected' : '' }}>L</option>
                            <option value="XL"  {{ $p->size == 'XL' ? 'selected' : '' }}>XL</option>
                            <option value="XXL" {{ $p->size == 'XXL' ? 'selected' : '' }}>XXL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="sku" id="skuInput{{ $p->id_produk }}" class="form-control" value="{{ $p->sku }}" readonly>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning">Update Product</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    @foreach($produk as $p)
    (function(id) {
        const cat   = document.getElementById('categorySelect' + id);
        const color = document.getElementById('colorSelect' + id);
        const size  = document.getElementById('sizeSelect' + id);
        const sku   = document.getElementById('skuInput' + id);

        if (!cat || !color || !size || !sku) return;

        function genSKU() {
            const catText = cat.options[cat.selectedIndex]?.text?.trim();
            const colorVal = color.value?.trim();
            const sizeVal  = size.value?.trim();

            if (catText && colorVal && sizeVal) {
                sku.value = `${catText}-${colorVal}-${sizeVal}`;
            }
        }

        ['change'].forEach(evt => {
            cat.addEventListener(evt, genSKU);
            color.addEventListener(evt, genSKU);
            size.addEventListener(evt, genSKU);
        });

        if (window.jQuery) {
            $('#editProductModal' + id).on('shown.bs.modal', genSKU);
        } else {
            genSKU();
        }
    })({{ $p->id_produk }});
    @endforeach
});
</script>
@endforeach