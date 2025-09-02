<div class="modal fade" id="confirmRejectModal" tabindex="-1" role="dialog" aria-labelledby="confirmRejectModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reject-modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('reject-transfer') }}" method="post">
                @csrf
                @method("POST")

                <div class="modal-body">
                    <div id="reject-message"></div>

                    <input type="hidden" name="reject_transfer_no" id="reject_transfer_no" value="">

                    <div class="form-group mt-2">
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3"
                            placeholder="Enter rejection reason"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="close_reject_modal" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                </div>
            </form>

        </div>
    </div>
</div>