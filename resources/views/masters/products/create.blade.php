<div class="modal fade" id="create" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
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
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="product_name">Product Name <font color="red">*</font></label>
                                        <input type="text" class="form-control" id="name_edit" name="name"
                                               aria-describedby="emailHelp" maxlength="50" minlength="2"
                                               placeholder="" required value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="barcode">Barcode</label>
                                        <input type="text" class="form-control" id="barcode_edit" name="barcode"
                                               placeholder="" value="{{ old('barcode') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="generic_name">Generic Name</label>
                                        <input type="text" class="form-control" id="generic_name_edit"
                                               name="generic_name" maxlength="50" minlength="2"
                                               placeholder="" value="{{ old('generic_name') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Category <font
                                                color="red">*</font></label>
                                        {{--                                        <div id="category_border" style="border: 2px solid white; border-radius: 6px;">--}}
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
                                        {{--                                        </div>--}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect1">Sub Category</label>
                                        <select name="sub_category" class="form-control" id="sub_category">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="standardUoM">Standard Unit of Measure</label>
                                        <input type="number" class="form-control" id="standardUoM_edit"
                                               name="standardUoM" min="1"
                                               placeholder="" value="{{ old('standardUoM') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="saleUoM">Sale Unit of Measure</label>
                                        <input type="number" class="form-control" id="saleUoM_edit" name="saleUoM"
                                               placeholder="" value="{{ old('saleUoM') }}" min="1">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="purchaseUoM">Purchase Unit of Measure</label>
                                        <input type="number" class="form-control" id="purchaseUoM_edit"
                                               name="purchaseUoM" min="1"
                                               placeholder="" value="{{ old('purchaseUoM') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="min_stock">Minimum Stock Quantity</label>
                                        <input type="text" class="form-control" id="min_stock_edits" name="min_stock"
                                               placeholder="" value="{{ old('min_stock') }}" min="1"
                                               onkeypress="return isNumberKey(event,this)">
                                    </div>
                                </div>
                                <div class="col-md-4" hidden>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <input type="text" class="form-control" id="status_edit" name="status"
                                               placeholder="" value="1" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_stock">Maximum Stock Quantity</label>
                                        <input type="text" class="form-control" id="max_stock_edits" name="max_stock"
                                               placeholder="" value="{{ old('max_stock') }}" min="1"
                                               onkeypress="return isNumberKey(event,this)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dosage">Dosage</label>
                                        <textarea class="form-control max-textarea" maxlength="50" rows="1"
                                                  name="dosage" value="{{ old('dosage') }}"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="indication">Indication</label>
                                        <textarea class="form-control max-textarea" maxlength="50" rows="1"
                                                  name="indication" value="{{ old('Indication') }}"></textarea>
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
