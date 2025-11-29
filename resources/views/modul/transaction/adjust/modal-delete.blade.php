<div class="modal fade" id="deleteAdjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="deleteAdjustForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete adjustment for 
                    <strong id="deleteAdjustName"></strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    $('#deleteAdjustModal').on('show.bs.modal', function(event){
        let button = $(event.relatedTarget);
        let action = button.data('action');
        let name = button.data('name');

        $('#deleteAdjustForm').attr('action', action);
        $('#deleteAdjustName').text(name);
    });
});
</script>
