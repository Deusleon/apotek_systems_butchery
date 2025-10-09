<div class="modal fade" id="purchase-return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Purchase Return</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('purchase-returns.store') }}" method="post" name="purchase-return-form"
                  enctype="multipart/form-data">
                @csrf()
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="product_name" class="col-md-4 col-form-label text-md-right">Product Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="product_name" readonly>

                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="quantity" class="col-md-4 col-form-label text-md-right">Quantity<font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="hidden" class="form-control"
                                   name="quantity" value="" min="1" step="1" id="rtn_qty"
                                   placeholder="">
                            <input type="text" class="form-control"
                                   name="quantity_to_show" value="" min="1" step="1" id="rtn_qty_to_show"
                                   placeholder="Enter quantity" required>
                            <div class="text text-danger" id="qty_error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="reason" class="col-md-4 col-form-label text-md-right">Reason<font color="red">*</font></label>
                        <div class="col-md-8">
                                     <textarea type="text" class="form-control"
                                               name="reason"
                                               placeholder="Enter reason for return" required></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="goods_receiving_id" id="goods_receiving_id" value="">
                    <input type="hidden" name="original_qty" id="original_qty" value="">

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save_btn">Save</button>

                </div>
            </form>

        </div>
    </div>
</div>