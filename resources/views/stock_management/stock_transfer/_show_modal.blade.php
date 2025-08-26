<!-- Show Stock Transfer Modal -->
<div class="modal fade" id="showStockTransferModal" tabindex="-1" role="dialog"
    aria-labelledby="showStockTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Stock Transfer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col mb-2">
                    <div class="row">
                        <div class="col-4">
                            <strong class="">Transfer No:</strong>
                            <p id="show_transfer_no" class="text-body"></p>
                        </div>
                        <div class="col-4">
                            <strong class="">Status:</strong>
                            <p id="show_status" class="text-body"></p>
                        </div>
                        <div class="col-4">
                            <strong class="">Remarks:</strong>
                            <p id="show_remarks" class="text-body"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <strong class="">From Store:</strong>
                            <p id="show_from_store" class="text-body"></p>
                        </div>
                        <div class="col-4">
                            <strong class="">To Store:</strong>
                            <p id="show_to_store" class="text-body"></p>
                        </div>
                        <div class="col-4">
                            <strong class="">Approved By:</strong>
                            <p id="show_approved_by" class="text-body"></p>
                        </div>
                    </div>
                </div>
                <table class="display table nowrap table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="show_items_table_body">
                        <!-- Items will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="reject" class="btn btn-danger btn-reject-transfer"
                    data-target="#confirmRejectModal" data-transfer-no="" data-action="cancelled" data-from-store=""
                    data-to-store="" title="Reject Transfer">Reject</button>
                <button type="button" id="approve" class="btn btn-primary btn-approve-transfer"
                    data-target="#confirmModal" data-transfer-no="" data-action="approved" data-from-store=""
                    data-to-store="" data-status="" title="Approve Transfer">
                    Approve
                </button>
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>