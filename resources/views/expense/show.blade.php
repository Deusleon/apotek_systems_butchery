<div class="modal fade" id="show" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Date:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="show_date" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Pay Method:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="show_payment_method" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Amount:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="show_amount" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Category:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="show_category" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Updated by:</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="show_updated_by" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">Description:</label>
                    <div class="col-md-8">
                        <input class="form-control" id="show_description" readonly rows="3"></input>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>