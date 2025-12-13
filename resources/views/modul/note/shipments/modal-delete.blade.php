<div class="modal fade" id="deleteShipmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete Shipment</h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <form method="POST" id="deleteShipmentForm">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    Are you sure you want to delete shipment from 
                    <strong id="deleteShipmentName"></strong> ?
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
document.addEventListener('DOMContentLoaded', function () {
    $('#deleteShipmentModal').on('show.bs.modal', function (event) {
        let btn = $(event.relatedTarget);
        let action = btn.data('action');
        let name = btn.data('name');

        $('#deleteShipmentForm').attr('action', action);
        $('#deleteShipmentName').text(name);
    });
});
</script>
