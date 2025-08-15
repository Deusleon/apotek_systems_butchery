<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
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
                    <form id="form_product">
                        @csrf()
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Product Name') }}
                                    <font color="red">*</font></label>

                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name_edit" name="name"
                                           aria-describedby="emailHelp" maxlength="50" minlength="2"
                                           placeholder="" required value="{{ old('name') }}">
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="barcode" class="col-md-4 col-form-label text-md-right">Barcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="barcode_edit" name="barcode"
                                           placeholder="" value="{{ old('barcode') }}" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="exampleFormControlSelect1"
                                       class="col-md-4 col-form-label text-md-right">Category <font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <select name="category" class="form-control" id="category_option" required
                                            onchange="createOption()">
                                        <option selected="true" value="0" disabled="disabled">Select Category
                                        </option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    name="category">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="category_border" style="display: none; color: red; font-size: 0.9em">category required</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit of
                                    Measure</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="saleUoM_edit" name="saleUoM"
                                           placeholder="" value="{{ old('saleUoM') }}" min="1">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="min_stock" class="col-md-4 col-form-label text-md-right">Min. Stock
                                    Quantity</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="min_stock_edits" name="min_stock"
                                           placeholder="" value="{{ old('min_stock') }}" min="1"
                                           onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="max_stock" class="col-md-4 col-form-label text-md-right">Max. Stock
                                    Quantity</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="max_stock_edits" name="max_stock"
                                           placeholder="" value="{{ old('max_stock') }}" min="1"
                                           onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="product_type" class="col-md-4 col-form-label text-md-right">Type <font color="red">*
                                    </font></label>
                                <div class="col-md-8">
                                    <select name="product_type" class="form-control" id="product_type" required>
                                        <option selected value="stockable">Stockable</option>
                                        <option value="consumable">Service</option>
                                    </select>
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
