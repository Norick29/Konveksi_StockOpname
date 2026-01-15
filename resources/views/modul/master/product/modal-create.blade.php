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
                        <div>
                            @foreach(['S','M','L','XL','XXL'] as $size)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" class="form-check-input">
                                    <label class="form-check-label">{{ $size }}</label>
                                </div>
                            @endforeach
                        </div>
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

    const categorySelect = document.getElementById('categorySelect');
    const colorSelect    = document.getElementById('colorSelect');
    const sizeSelect     = document.getElementById('sizeSelect');
    const skuInput       = document.getElementById('skuInput');

    function genSKU() {
        const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text?.trim() || '';
        const colorText    = colorSelect.value.trim();
        const sizeText     = sizeSelect.value.trim();

        if (categoryText && colorText && sizeText) {
            // SKU full name (tanpa singkatan)
            skuInput.value = `${categoryText}-${colorText}-${sizeText}`;
        } else {
            skuInput.value = '';
        }
    }

    categorySelect.addEventListener('change', genSKU);
    colorSelect.addEventListener('change', genSKU);
    sizeSelect.addEventListener('change', genSKU);

    // Reset modal saat ditutup
    $('#createProductModal').on('hidden.bs.modal', function () {
        const form = this.querySelector('form');
        if (form) form.reset();
        skuInput.value = '';
    });
});
</script>