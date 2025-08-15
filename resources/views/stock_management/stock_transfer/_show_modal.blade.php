<!-- Show Stock Transfer Modal -->
<div class="modal fade" id="showStockTransferModal" tabindex="-1" role="dialog" aria-labelledby="showStockTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Stock Transfer Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Transfer No:</strong>
                        <p id="show_transfer_no"></p>
                    </div>
                    <div class="col-md-4">
                        <strong>From Store:</strong>
                        <p id="show_from_store"></p>
                    </div>
                    <div class="col-md-4">
                        <strong>To Store:</strong>
                        <p id="show_to_store"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Status:</strong>
                        <p id="show_status"></p>
                    </div>
                    <div class="col-md-8">
                        <strong>Remarks:</strong>
                        <p id="show_remarks"></p>
                    </div>
                </div>
                <hr>
                <h6>Transferred Items</h6>
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="show_items_table_body">
                        <!-- Items will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
