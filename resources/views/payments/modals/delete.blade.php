<div class="modal fade" id="deletePaymentModal" tabindex="-1" role="dialog" aria-labelledby="deletePaymentLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePaymentLabel">Confirm Deletion</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment? This action cannot be undone.</p>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Deleting this payment will affect the order's payment status.
                </div>
                <div class="payment-details">
                    <p><strong>Receipt Number:</strong> <span id="delete_receipt_number" class="font-weight-bold"></span></p>
                    <p><strong>Amount:</strong> <span id="delete_amount" class="font-weight-bold"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <form id="form_payment_delete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="feather icon-trash"></i> Delete Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>