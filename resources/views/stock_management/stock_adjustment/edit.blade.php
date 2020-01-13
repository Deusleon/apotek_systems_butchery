<div class="modal fade" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stock Adjustment</h5>
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
                    <form action="" method="post">
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
                                        <label for="type">Type</label>
                                        <select name="type" class="form-control" id="type" required>
                                            <option readonly value="option_select" id="store_name_edit" disabled
                                                    selected>Select Adjustment Type...
                                            </option>
                                            <option value="Negative">Negative</option>
                                            <option value="Positive">Positive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity_in">Quantity Adjusted</label>
                                        <input type="number" class="form-control" id="quantity_edit"
                                               name="quantity_in"
                                               placeholder="" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Reason</label>
                                        <select name="reason" class="form-control" id="reason_edit" required>
                                            <option readonly value="option_select" id="store_name_edit" disabled
                                                    selected>Select Adjustment Reason...
                                            </option>
                                            @foreach($reasons as $reason)
                                                <option value="{{$reason->reason}}">{{$reason->reason}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description_edit"
                                               name="description"
                                               placeholder="" required>
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
                            <input type="hidden" name="stock_id" id="id">
                            <input type="hidden" name="product_id" id="product_id">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#test").autocomplete({
        source: ["apple", "boy", "laugh"],
        minLength: 0
    }).focus(function () {
        $(this).data("uiAutocomplete").search($(this).val());
    });
</script>