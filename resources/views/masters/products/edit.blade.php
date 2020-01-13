<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_product_edit" action="{{route('products.update','id')}}" method="post">
                @csrf()
                @method("PUT")

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
                                <input type="text" class="form-control" id="generic_edit"
                                       name="generic" maxlength="50" minlength="2"
                                       placeholder="" value="{{ old('generic_name') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Category <font color="red">*</font></label>
                                <select name="category" class="form-control" id="category_options"
                                        onchange="editOption()">
                                    <option id="category_edit" disabled selected></option>
                                    @foreach($categories as $cat)
                                        <option value="{{$cat->id}}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <span id="category_borders" style="display: none; color: red; font-size: 0.9em">category required</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Sub Category</label>
                                <select name="sub_category" class="form-control" id="sub_categories">
                                    <option id="sub_category_edit" disabled selected></option>
                                    @foreach($sub_categories as $sub)
                                        <option value="{{$sub->id}}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="standardUoM">Standard Unit of Measure</label>
                                <input type="number" class="form-control" id="standard_edit"
                                       name="standard_uom" min="1"
                                       placeholder="" value="{{ old('standardUoM') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="saleUoM">Sale Unit of Measure</label>
                                <input type="number" class="form-control" id="sale_edit" name="sale_uom"
                                       placeholder="" value="{{ old('saleUoM') }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="purchaseUoM">Purchase Unit of Measure</label>
                                <input type="number" class="form-control" id="purchase_edit"
                                       name="purchase_uom" min="1"
                                       placeholder="" value="{{ old('purchaseUoM') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="min_stock">Minimum Stock Quantity</label>
                                <input type="text" class="form-control" id="min_stock_edit" name="min_stock"
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
                                <input type="text" class="form-control" id="max_stock_edit" name="max_stock"
                                       placeholder="" value="{{ old('max_stock') }}" min="1"
                                       onkeypress="return isNumberKey(event,this)">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dosage_edit">Dosage</label>
                                <textarea class="form-control max-textarea" maxlength="50" rows="1"
                                          id="dosage_edit" name="dosage" value="{{ old('dosage') }}"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="indication_edit">Indication</label>
                                <textarea class="form-control max-textarea" maxlength="255" rows="1"
                                          id="indication_edit" name="indication"
                                          value="{{ old('Indication') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="code" id="code_edit">

                    <input type="hidden" name="id" id="id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
