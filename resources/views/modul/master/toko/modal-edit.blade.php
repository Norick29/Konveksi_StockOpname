@foreach ($toko as $tk)
<!-- Edit User Modal -->
<div class="modal fade" id="editStoreModal{{ $tk->id_toko }}" tabindex="-1" role="dialog"
     aria-labelledby="editStoreLabel{{ $tk->id_toko }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editStoreLabel{{ $tk->id_toko }}">Edit Store</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="{{ route('stores.update', $tk->id_toko) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Full Name --}}
                    <div class="form-group">
                        <label>Store Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ $tk->name }}"
                               class="form-control" 
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Store
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach
