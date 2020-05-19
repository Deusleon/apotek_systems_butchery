<div class="modal fade" id="bulk_adjust_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Stock Adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <form id="adjust_form_" action="{{route('adjustment-history.store')}}" method="post">
                        @csrf()
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <input type="text" class="form-control" id="name_edit_bulk" name="name"
                                               aria-describedby="emailHelp"
                                               placeholder="" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity_in">Quantity in Stock</label>
                                        <input type="text" class="form-control" id="quantity_in_edit_bulk"
                                               name="quantity_in"
                                               placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Adjustment Type<span style="color: red; ">*</span></label>
                                        <select name="type" class="form-control" id="type_bulk" required>
                                            <option readonly value="" disabled
                                                    selected>Select Type...
                                            </option>
                                            <option value="Negative">Negative</option>
                                            <option value="Positive">Positive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Quantity to Adjust<span
                                                style="color: red; ">*</span></label>
                                        <input type="number" class="form-control" id="quantity_edit_bulk"
                                               oninput="calculateBulk()"
                                               name="quantity" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Adjustment Reason<span style="color: red; ">*</span></label>
                                        <select name="reason" class="form-control" id="reason_bulk" required>
                                            <option readonly value="" disabled
                                                    selected>Select Reason...
                                            </option>
                                            @foreach($reasons as $reason)
                                                <option value="{{$reason->reason}}">{{$reason->reason}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Buying Price</label>
                                        <input type="text" class="form-control" id="unit_cost_edit_bulk"
                                               name="unit_cost"
                                               placeholder="" value="{{ old('unit_cost') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description_edit_bulk"
                                               name="description"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="text" class="form-control" id="amount_edit_bulk"
                                               name="amount"
                                               placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6" hidden>
                                    <div class="form-group">
                                        <label for="created_by">Created By</label>
                                        <input type="text" class="form-control" id="created_by_edit"
                                               name="created_by"
                                               placeholder="">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" id="stock_id_bulk">
                            <input type="hidden" name="product_id" id="product_id_bulk">
                            <input type="hidden" name="bulk" value="1">
                        </div>
                        <div class="modal-footer">
                            <button id="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    function calculateBulk() {
        var to_adjust = document.getElementById('quantity_edit_bulk').value;
        var unit_cost = document.getElementById('unit_cost_edit_bulk').value;
        var result = document.getElementById('amount_edit_bulk');

        result.value = formatMoney(parseFloat(unit_cost.replace(/\,/g, ''), 10) * to_adjust);
    }

</script>
