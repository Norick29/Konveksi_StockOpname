@foreach ($kategori as $kat)
<!-- Edit User Modal -->
<div class="modal fade" id="editCategoriesModal{{ $kat->id_kategori }}" tabindex="-1" role="dialog"
     aria-labelledby="editCategoriesLabel{{ $kat->id_kategori }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editCategoriesLabel{{ $kat->id_kategori }}">Edit Store</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="{{ route('categories.update', $kat->id_kategori) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Full Name --}}
                    <div class="form-group">
                        <label>Categories Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ $kat->name }}"
                               class="form-control" 
                               required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Categories
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach
