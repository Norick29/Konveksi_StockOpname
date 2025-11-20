@foreach($stok as $s)
<div class="modal fade" id="editOpeningModal{{ $s->id_stok_bulan }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Opening Stock</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form action="{{ route('opening-stock.update', $s->id_stok_bulan) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Product --}}
                    <div class="form-group">
                        <label>Product</label>
                        <select name="id_produk" class="form-control" required>
                            @foreach($produk as $p)
                                <option value="{{ $p->id_produk }}" 
                                    {{ $s->id_produk == $p->id_produk ? 'selected' : '' }}>
                                    {{ $p->sku }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Store --}}
                    <div class="form-group">
                        <label>Store</label>
                        <select name="id_toko" class="form-control" required>
                            @foreach($toko as $tk)
                                <option value="{{ $tk->id_toko }}"
                                    {{ $s->id_toko == $tk->id_toko ? 'selected' : '' }}>
                                    {{ $tk->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Month --}}
                    <div class="form-group">
                        <label>Month</label>
                        <input type="month" name="month" class="form-control"
                            value="{{ $s->month }}" required>
                    </div>

                    {{-- Quantity --}}
                    <div class="form-group">
                        <label>Opening Quantity</label>
                        <input type="number" name="quantity" class="form-control"
                            value="{{ $s->quantity }}" min="0" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit"><i class="fas fa-save"></i> Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
