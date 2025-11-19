<!-- Create Store Modal -->
<div class="modal fade" id="createCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="createCategoriesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createCategoriesLabel">Add New Categories</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form id="createCategoriesForm" action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">Categories Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            placeholder="Enter categories name"
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
                        <i class="fas fa-save"></i> Save Categories
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Toggle password visibility --}}
<script>

// reset form + UI helper
function resetCreateCategoriesForm() {
    const form = document.getElementById('createCategoriesForm');
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
    $('#createCategoriesModal').on('hidden.bs.modal', resetCreateCategoriesForm);
}

// fallback: reset when any data-dismiss button inside modal is clicked
document.querySelectorAll('#createCategoriesModal [data-dismiss="modal"]').forEach(btn => {
    btn.addEventListener('click', resetCreateCategoriesForm);
});
</script>
