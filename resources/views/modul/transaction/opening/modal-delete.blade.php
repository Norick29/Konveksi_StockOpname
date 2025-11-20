<div class="modal fade" id="deleteConfirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Opening Stock</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p class="text-center">
                        Are you sure you want to delete this opening stock?
                    </p>
                    <h5 id="deleteItemName" class="text-center font-weight-bold"></h5>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit"><i class="fas fa-trash"></i> Delete</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    $('#deleteConfirmModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action');
        let name = button.data('name');

        $('#deleteForm').attr('action', action);
        $('#deleteItemName').text(name);
    });
</script>
