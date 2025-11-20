<div class="modal fade" id="createProductModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Product</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('product.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="form-group">
                        <label>Category</label>
                        <select name="id_kategori" id="categorySelect" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($kategori as $k)
                                {{-- use existing code property if available, otherwise derive from name --}}
                                <option value="{{ $k->id_kategori }}" data-code="{{ $k->code ?? strtoupper(substr($k->name,0,3)) }}">
                                    {{ $k->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Color</label>
                        <select name="color" id="colorSelect" class="form-control" required>
                            <option value="">-- Select Color --</option>
                            <option value="Hitam">Hitam</option>
                            <option value="Putih">Putih</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Size</label>
                        <select name="size" id="sizeSelect" class="form-control" required>
                            <option value="">-- Select Size --</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>SKU (Auto)</label>
                        <input type="text" name="sku" id="skuInput" class="form-control" readonly placeholder="SKU akan terisi otomatis">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary">Save Product</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Auto-generate SKU when category/color/size selected --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cat = document.getElementById('categorySelect');
    const color = document.getElementById('colorSelect');
    const size = document.getElementById('sizeSelect');
    const skuInput = document.getElementById('skuInput');

    function mapCategoryCode(opt) {
        if (!opt) return '';
        const dataCode = (opt.dataset.code || '').trim().toUpperCase();
        if (dataCode === 'SS' || dataCode === 'LS') return dataCode;

        const text = (opt.textContent || '').toLowerCase();
        if (text.includes('short') || text.includes('short sleeve') || text.includes('ss')) return 'SS';
        if (text.includes('long') || text.includes('long sleeve') || text.includes('ls')) return 'LS';

        // fallback: first two letters of category name
        return (opt.textContent || '').trim().slice(0, 2).toUpperCase();
    }

    function mapColorCode(val) {
        if (!val) return '';
        const v = val.toLowerCase();
        if (v === 'Hitam' || v === 'Black') return 'HM';
        if (v === 'Putih' || v === 'White') return 'PH';
        // fallback: first two letters
        return v.slice(0, 2).toUpperCase();
    }

    function mapSizeCode(val) {
        if (!val) return '';
        return val.toUpperCase(); // S, M, L, XL, XXL
    }

    function genSKU() {
        if (!cat || !color || !size || !skuInput) return;
        const catOpt = cat.options[cat.selectedIndex];
        const catCode = mapCategoryCode(catOpt);
        const colorCode = mapColorCode(color.value);
        const sizeCode = mapSizeCode(size.value);

        if (catCode && colorCode && sizeCode) {
            skuInput.value = `${catCode}-${colorCode}-${sizeCode}`;
        } else {
            skuInput.value = '';
        }
    }

    ['change','blur','keyup'].forEach(evt => {
        if (cat) cat.addEventListener(evt, genSKU);
        if (color) color.addEventListener(evt, genSKU);
        if (size) size.addEventListener(evt, genSKU);
    });

    // reset SKU when modal closed (if using bootstrap/jQuery)
    if (window.jQuery) {
        $('#createProductModal').on('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            if (form) form.reset();
            if (skuInput) skuInput.value = '';
        });
    } else {
        // fallback: clear when modal hidden via data-dismiss buttons
        document.querySelectorAll('#createProductModal [data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const form = document.querySelector('#createProductModal form');
                if (form) form.reset();
                if (skuInput) skuInput.value = '';
            });
        });
    }
});
</script>