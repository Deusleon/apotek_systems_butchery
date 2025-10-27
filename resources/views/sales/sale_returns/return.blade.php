<div class="modal fade" id="sale-return" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Sales Return</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sale-returns.store') }}" method="post" name="return-form"
                  enctype="multipart/form-data">
                @csrf()
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">

                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Product Name</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name_of_item" readonly>

                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Quantity<font
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
                        <label for="code" class="col-md-4 col-form-label text-md-right">Reason<font color="red">*</font></label>
                        <div class="col-md-8">
                                     <textarea type="text" class="form-control"
                                               name="reason"
                                               placeholder="Enter reason for return" required></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="item_id" id="id_of_item" value="">
                    <input type="hidden" name="original_qty" id="og_item_qty" value="">

                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save_btn">Save</button>

                </div>
            </form>

        </div>
    </div>
</div>
