<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
        <div class="modal-dialog " role="document">
          <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirm-modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('approve-transfer')}}" method="post">
                    @csrf
                    @method("POST")

                    <div class="modal-body">
                        <div id="confirm-message"></div>

                        <input type="hidden" name="transfer_no" id="confirm_transfer_no" value="">
                        <input type="hidden" name="from_store" id="confirm_from_store" value="">
                        <input type="hidden" name="to_store" id="confirm_to_store" value="">
                        <input type="hidden" name="status" id="confirm_status" value="">
                        <input type="hidden" name="confirm_action" id="confirm_action" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="close_confirm_modal" class="btn btn-danger btn-sm" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                    </div>
                </form>

          </div>
        </div>
 </div>
