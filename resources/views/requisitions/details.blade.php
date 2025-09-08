<div class="modal fade" id="requisition-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Requisition List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <div class="requisition-info row mb-3">
                        <div class="form-group col-md-4">
                            <label>Requisition #</label>
                            <h6 id="req_no">Loading...</h6>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Created By</label>
                            <h6 id="created_by">Loading...</h6>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Date Created</label>
                            <h6 id="date_created">Loading...</h6>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table style="width: 100%" class="table nowrap table-striped table-hover" id="order_table">
                            <thead>
                            <tr class="bg-navy disabled">
                                <th>Product Name</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




