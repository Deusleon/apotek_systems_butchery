<div class="modal fade" id="credit-sale-payment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Credit Sale Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('credit-payments.creditSalePayment') }}" method="post" name="payment-form"
                  enctype="multipart/form-data">
                @csrf()
                <div class="modal-body">
                    <div class="form-group row" style="padding-top:0px;">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Receipt#</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="receipt-number" readonly>
                        </div>
                    </div>
                    <div class="form-group row" style="padding-top:0px;">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Balance</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="outstanding" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Amount <font color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" value="" id="paying"
                                   placeholder="Enter Paying Amount" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Remark <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <textarea type="text" class="form-control"
                                      name="remark"
                                      placeholder="Enter Remark" required></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="sale_id" id="id_of_sale" value="">
                    <input type="hidden" name="customer_id" id="customer-id" value="">
                    <input type="hidden" name="balance" id="balance-amount" value="">
                    <input type="hidden" name="paid_amount" id="paid-amount" value="">

                </div>
                <div class="modal-footer">
                    <div class="row">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" id="save_btn">Save</button>
                    </div>
                </div>
            </form>


        </div>
    </div>
</div>
