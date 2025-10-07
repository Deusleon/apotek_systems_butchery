<!-- Edit Stock Transfer Modal -->
<div class="modal fade" id="editStockTransferModal" tabindex="-1" role="dialog" aria-labelledby="editStockTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <form method="POST" id="form_stock_transfer_edit" action="">
                @csrf
                @method('PUT')
                <div class="modal-header bg-white text-white">
                    <h5 class="modal-title">Edit Stock Transfer</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Transfer #:</strong>
                            <p id="edit_transfer_no"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>From Store:</strong>
                            <p id="edit_from_store"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>To Store:</strong>
                            <p id="edit_to_store"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remarks_edit">Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks_edit" rows="3"></textarea>
                    </div>
                    <hr>
                    <h6>Transferred Items</h6>
                    <div id="edit_items_container">
                        <!-- Items will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
