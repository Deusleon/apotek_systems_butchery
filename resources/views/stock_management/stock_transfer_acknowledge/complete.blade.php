<div class="modal fade" id="completes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Complete Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="transfer_ack" action="{{route('stock-transfer-acknowledge.update','id')}}" method="post">
                    @csrf()
                    @method("PUT")

                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Transfer #') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="transfer_no" name="transfer_no"
                                   aria-describedby="emailHelp" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('From') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="from" name="from" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('To') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="to" name="to"
                                   aria-describedby="emailHelp" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code"
                               class="col-md-4 col-form-label text-md-right">{{ __('Transfered Quantity') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="quantity_trn" name="quantity_trn"
                                   aria-describedby="emailHelp" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Quantity Received') }}
                            <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="number" min="0" class="form-control" id="quantity_rcvd"
                                   name="quantity_rcvd" onchange="compareVal()" required>
                            <span id="span_danger" style="display: none; color: red"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Remarks') }}</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="remarks" name="remarks">
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="stock_id" id="stock_id">


                    <div class="modal-footer">
                        <button id="ack" class="btn btn-primary">Complete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>


        </div>
    </div>
</div>
