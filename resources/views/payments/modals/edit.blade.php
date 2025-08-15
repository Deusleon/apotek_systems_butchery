<div class="modal fade" id="editPayment" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Update Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="form_payment_edit" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="payment_id" name="id">

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Amount</label>
                        <div class="col-md-9">
                            <input type="number" step="0.01" class="form-control" name="amount" id="amount_edit" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Payment Type</label>
                        <div class="col-md-9">
                            <select class="form-control" name="payment_type" id="payment_type_edit" required>
                                <option value="advance">Advance</option>
                                <option value="balance">Balance</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Payment Method</label>
                        <div class="col-md-9">
                            <select class="form-control" name="payment_method" id="payment_method_edit" required>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Receipt Number</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="receipt_number" id="receipt_number_edit" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Transaction Reference</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="transaction_reference" id="transaction_reference_edit">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Payment Date</label>
                        <div class="col-md-9">
                            <input type="date" class="form-control" name="payment_date" id="payment_date_edit" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Notes</label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="notes" id="notes_edit" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Status</label>
                        <div class="col-md-9">
                            <select class="form-control" name="status" id="status_edit">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Payment Proof</label>
                        <div class="col-md-9">
                            <input type="file" class="form-control-file" name="payment_proof">
                            <small class="form-text text-muted">Upload new proof (optional, max 2MB)</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>