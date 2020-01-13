<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Stock Issue</h5>
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
                    <form action="{{route('stock-issue.store')}}" method="post">
                        @csrf()
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transfer_no">Issue Number</label>
                                        <input type="text" class="form-control" id="transfer_no_edit" name="transfer_no"
                                               aria-describedby="emailHelp"
                                               placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Transfer Number</label>
                                        <input type="text" class="form-control" id="name_edit" name="name"
                                               placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" class="form-control" id="quantity_edit"
                                               name="quantity"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit_cost">Unit Cost</label>
                                        <input type="number" class="form-control" id="unit_cost_edit"
                                               name="unit_cost"
                                               placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sale_price">Sale Price</label>
                                        <input type="number" class="form-control" id="sale_price_edit"
                                               name="sale_price"
                                               placeholder="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="total">Sub Total</label>
                                        <input type="number" class="form-control" id="total_edit"
                                               name="total"
                                               readonly>
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
                                        <label for="from_store">Issued To</label>
                                        <select name="from_store" class="form-control" id="from_store">
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">{{$store->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" id="id">
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
