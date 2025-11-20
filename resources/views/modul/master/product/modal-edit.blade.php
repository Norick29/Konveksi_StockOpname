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
        const cat = document.getElementById('categorySelect' + id);
        const color = document.getElementById('colorSelect' + id);
        const size = document.getElementById('sizeSelect' + id);
        const skuInput = document.getElementById('skuInput' + id);

        if (!cat || !color || !size || !skuInput) return;

        function mapCategoryCode(opt) {
            if (!opt) return '';
            const code = (opt.dataset.code || '').trim().toUpperCase();
            if (code === 'SS' || code === 'LS') return code;
            const text = (opt.textContent || opt.innerText || '').toLowerCase();
            if (text.includes('short')) return 'SS';
            if (text.includes('long')) return 'LS';
            return code || (text.slice(0,2).toUpperCase());
        }

        function mapColorCode(val) {
            if (!val) return '';
            const v = val.toLowerCase();
            if (v === 'Hitam' || v === 'Black') return 'HM';
            if (v === 'Putih' || v === 'White') return 'PH';
            return v.slice(0,2).toUpperCase();
        }

        function mapSizeCode(val) {
            if (!val) return '';
            return val.toUpperCase(); // S, M, L, XL, XXL
        }

        function genSKU() {
            const catOpt = cat.options[cat.selectedIndex];
            const catCode = mapCategoryCode(catOpt);
            const colorCode = mapColorCode(color.value);
            const sizeCode = mapSizeCode(size.value);

            if (catCode && colorCode && sizeCode) {
                skuInput.value = `${catCode}-${colorCode}-${sizeCode}`;
            } else {
                // keep existing sku (if any) or clear when fields incomplete
                skuInput.value = skuInput.value && (catCode || colorCode || sizeCode) ? skuInput.value : '';
            }
        }

        ['change','blur','keyup'].forEach(evt => {
            cat.addEventListener(evt, genSKU);
            color.addEventListener(evt, genSKU);
            size.addEventListener(evt, genSKU);
        });

        // generate once when modal shown (Bootstrap/jQuery) or immediately if no jQuery
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