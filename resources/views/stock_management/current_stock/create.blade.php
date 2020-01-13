<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Adjustment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as  $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>
                    @endif
                    <form id="adjust_form" action="{{route('stock-adjustment.store')}}" method="post">
                        @csrf()
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Product Name</label>
                                        <input type="text" class="form-control" id="name_edit" name="name"
                                               aria-describedby="emailHelp"
                                               placeholder="" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity_in">Quantity in Stock</label>
                                        <input type="text" class="form-control" id="quantity_in_edit"
                                               name="quantity_in"
                                               placeholder="" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type">Adjustment Type<span style="color: red; ">*</span></label>
                                        <div id="type_border" style="border: 2px solid white; border-radius: 6px;">
                                            <select name="type" class="form-control" id="type" required>
                                                <option readonly value="0" id="store_name_edit" disabled
                                                        selected>Select Type...
                                                </option>
                                                <option value="Negative">Negative</option>
                                                <option value="Positive">Positive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Quantity to Adjust<span
                                                style="color: red; ">*</span></label>
                                        <input type="number" class="form-control" id="quantity_edit_"
                                               oninput="calculate()"
                                               name="quantity" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Adjustment Reason<span style="color: red; ">*</span></label>
                                        <div id="reason_border" style="border: 2px solid white; border-radius: 6px;">
                                            <select name="reason" class="form-control" id="reason" required>
                                                <option readonly value="0" id="store_name_edit" disabled
                                                        selected>Select Reason...
                                                </option>
                                                @foreach($reasons as $reason)
                                                    <option value="{{$reason->reason}}">{{$reason->reason}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Buying Price</label>
                                        <input type="text" class="form-control" id="unit_cost_edit_"
                                               name="unit_cost"
                                               placeholder="" value="{{ old('unit_cost') }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description_edit"
                                               name="description"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="text" class="form-control" id="amount_edit"
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

                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="product_id" id="product_id">
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

    function calculate() {
        var to_adjust = document.getElementById('quantity_edit_').value;
        var unit_cost = document.getElementById('unit_cost_edit_').value;
        var result = document.getElementById('amount_edit');

        result.value = formatMoney(parseFloat(unit_cost.replace(/\,/g, ''), 10) * to_adjust);
    }

    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
            const negativeSign = amount < 0 ? "-" : "";
            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {

        }
    }


</script>
