<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Invoice Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" style="font-size: 15px;">
                
                <!-- Row 1 -->
                <div class="form-group row mb-2">
                    <label class="col-md-3 col-form-label text-md-right">Invoice Number:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="inv_no">
                    </div>
                    <label class="col-md-3 col-form-label text-md-right">Supplier's Name:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="supplier">
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="form-group row mb-2" style="margin-top: -2%;">
                    <label class="col-md-3 col-form-label text-md-right">Invoice Date:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="inv_date">
                    </div>
                    <label class="col-md-3 col-form-label text-md-right">Invoice Amount:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="amount">
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="form-group row mb-2" style="margin-top: -2%;">
                    <label class="col-md-3 col-form-label text-md-right">Grace Period (In Days):</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="period">
                    </div>
                    <label class="col-md-3 col-form-label text-md-right">Paid Amount:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="paid">
                    </div>
                </div>

                <!-- Row 4 (Received Amount + Received Status) -->
                <div class="form-group row mb-2" style="margin-top: -2%;">
                    <label class="col-md-3 col-form-label text-md-right">Received Amount:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="received">
                    </div>
                    <label class="col-md-3 col-form-label text-md-right">Received Status:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="status">
                    </div>
                </div>

                <!-- Row 5 -->
                <div class="form-group row mb-2" style="margin-top: -2%;">
                    <label class="col-md-3 col-form-label text-md-right">Payment Due Date:</label>
                    <div class="col-md-3">
                        <input type="text" readonly class="form-control-plaintext" id="due">
                    </div>
                </div>

                <!-- Remarks Styled Like Edit Modal -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="remarks" >Remarks / Description</label>
                            <textarea readonly id="remarks" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
