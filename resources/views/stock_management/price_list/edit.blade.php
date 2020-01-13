<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('price-list.update','id')}}" method="post">
                @csrf()
                @method("PUT")
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input type="text" class="form-control" id="name_edit" name="name"
                                       aria-describedby="emailHelp" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_category">Price Category</label>
                                <select name="price_category" class="form-control" id="price_category_edit" required>
                                    <option value="option_select" id="price_category_edit" disabled selected>Select category..</option>
                                    @foreach($price_categories as $price_category)
                                        <option value="{{$price_category->id}}">
                                            {{ $price_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit_cost">Buying Price</label>
                                <input type="number" class="form-control" id="unit_cost_edit"
                                       name="unit_cost" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sell_price">Selling Price</label>
                                <input type="number" class="form-control" id="sell_price_edit"
                                       name="sell_price"
                                       placeholder="" value="{{ old('sell_price') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="batch">Batch Number</label>
                                <input type="text" class="form-control" id="d_auto_4"
                                       name="batch" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiry">Expiry Date</label>
                                <input type="text" class="form-control" id="d_auto_5"
                                       name="expiry"
                                       placeholder="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-md-4" hidden>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <input type="text" class="form-control" id="status_edit" name="status"
                                       placeholder="" value="1" readonly>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="product_id" id="product_id">
                    <input type="hidden" name="stock_id" id="stock_id">

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
