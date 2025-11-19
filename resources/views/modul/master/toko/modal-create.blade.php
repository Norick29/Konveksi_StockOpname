<!-- Create Store Modal -->
<div class="modal fade" id="createStoreModal" tabindex="-1" role="dialog" aria-labelledby="createStoreLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createStoreLabel">Add New Store</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form id="createStoreForm" action="{{ route('stores.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">Store Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            placeholder="Enter store name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Toggle password visibility --}}
<script>

// reset form + UI helper
function resetCreateStoreForm() {
    const form = document.getElementById('createStoreForm');
    if (!form) return;

    // reset fields
    form.reset();

    // remove validation classes
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    // remove server-side invalid-feedback blocks (optional: keep if you want)
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

}

// Bootstrap event (jQuery) if available
if (window.jQuery) {
    $('#createStoreModal').on('hidden.bs.modal', resetCreateStoreForm);
}

// fallback: reset when any data-dismiss button inside modal is clicked
document.querySelectorAll('#createStoreModal [data-dismiss="modal"]').forEach(btn => {
    btn.addEventListener('click', resetCreateStoreForm);
});
</script>
