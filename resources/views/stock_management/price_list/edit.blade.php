<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form action="{{route('price-list.update','id')}}" method="post" id="editPriceForm">
                        @csrf()
                        @method("PUT")

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Product Name</label>
                                    <input type="text" class="form-control" name="name" id="name" required
                                           value="{{ old('product_name') }}">

                                    <span class="text-danger">
                                        <strong id="name-error1"></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Brand</label>
                                    <input type="text" class="form-control" name="brand" id="brand_edit"
                                           value="{{ old('brand') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pack Size</label>
                                    <input type="text" class="form-control" name="pack_size" id="pack_size_edit"
                                           value="{{ old('pack_size') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Buy Price</label>
                                    <input type="text" class="form-control" name="unit_cost" id="unit_cost_edit"
                                           value="{{ old('unit_cost') }}" required autofocus>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sell Price</label>
                                    <input type="text" class="form-control" name="sell_price" id="sell_price_edit"
                                           value="{{ old('sell_price') }}" required autofocus>
                                </div>
                            </div>
                            </div>
                        </div>

                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="price_category_id" id="price_category_id">
                        <input type="hidden" name="product_id" id="product_id">
                        <input type="hidden" name="stock_id" id="stock_id">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


