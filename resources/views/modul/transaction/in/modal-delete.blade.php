<div class="modal fade" id="deleteStockInModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form id="deleteStockInForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    Are you sure you want to delete Stock IN for  
                    <strong id="deleteStockInName"></strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    $('#deleteStockInModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action');
        let name = button.data('name');

        $('#deleteStockInForm').attr('action', action);
        $('#deleteStockInName').text(name);
    });
});
</script>