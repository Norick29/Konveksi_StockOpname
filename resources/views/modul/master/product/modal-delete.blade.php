<div class="modal fade" id="deleteProductModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Product</h5>
                    <button class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    Are you sure want to delete <strong id="deleteName"></strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger">Delete</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#deleteProductModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        $('#deleteForm').attr('action', button.data('action'));
        $('#deleteName').text(button.data('name'));
    });
});
</script>
