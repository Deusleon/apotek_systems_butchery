<div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Stock Price</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="update_stock" action="{{route('current-stock.update','id')}}" method="post">
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
                                <label for="store_name">Price Category<span style="color: red; ">*</span></label>
                                <select name="store_name" class="form-control" id="category_"
                                        required onchange="priceCategory(1)">
                                    <option name="store_name" value="" id="price_category"
                                            disabled selected readonly>
                                    </option>
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
                                <label for="exampleFormControlSelect1">Buying Price</label>
                                <input type="text" class="form-control" id="unit_cost_edit_"
                                       name="unit_cost" onkeypress="return isNumberKey(event,this)"
                                       placeholder="" value="{{ old('unit_cost') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sell_price">Selling Price<span style="color: red; ">*</span></label>
                                <input type="text" class="form-control" id="sell_price_edit_"
                                       name="sell_price" onkeypress="return isNumberKey(event,this)"
                                       placeholder="" required>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="product_id" id="product_id_">
                    <input type="hidden" name="stock_id" id="stock_id_">
                    <input type="hidden" name="sales_id" id="sales_id_">
                    <input type="hidden" name="page_pricing" value="1">


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
