<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Issue Return</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="issue_return" action="{{ route('stock-issue-return.store') }}" method="post" name="return-form"
                  enctype="multipart/form-data">
                @csrf()
                <div class="modal-body">

                    <div class="form-group row" style="padding-top:0px;">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Product Name') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name_of_item" readonly>

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code"
                               class="col-md-4 col-form-label text-md-right">{{ __('Quantity Issued') }}</label>
                        <div class="col-md-8">
                            <input type="number" class="form-control"
                                   name="quantity" value="" id="quantity"
                                   placeholder="Enter quantity" readonly>
                            <span class="help-inline">
                      </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Quantity Returned') }}
                            <font color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" min="0"
                                   name="quantity_rtn" value="" id="rtn_qty"
                                   placeholder="Enter quantity" required>
                            <span class="help-inline">
                      <div class="text text-danger" id="qty_error"></div>
                      </span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Return Reason') }}<font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <textarea type="text" class="form-control"
                                      name="reason" placeholder="Enter Reason To Return" required></textarea>
                        </div>
                    </div>

                    <div hidden>
                        <input name="issued_date" id="issued_date">
                        <input name="issued_to" id="issued_to">
                        <input name="stock_id" id="stock_id">
                        <input name="unit_cost" id="unit_cost">
                        <input name="issue_no_edit" id="issue_no_edit">
                        <input name="row_index" id="row_index">
                        <input name="id" id="id">
                        <input name="current_stock" id="current_stock">

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="save_btn">Save</button>
                </div>
            </form>


        </div>
    </div>
</div>
