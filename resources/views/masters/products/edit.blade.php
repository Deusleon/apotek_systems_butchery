<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
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

                    <div class="form-group row">
                        <label for="product_name" class="col-md-4 col-form-label text-md-right">Product Name <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name_edit" name="name"
                                   aria-describedby="emailHelp" maxlength="50" minlength="2"
                                   placeholder="" required value="{{ old('name') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="barcode" class="col-md-4 col-form-label text-md-right">Barcode</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="barcode_edits" name="barcode"
                                   placeholder="" value="{{ old('barcode') }}">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="exampleFormControlSelect1" class="col-md-4 col-form-label text-md-right">Category
                            <font color="red">*</font></label>
                        <div class="col-md-8">
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

                    <div class="form-group row">
                        <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit of Measure</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="sale_edit" name="sale_uom"
                                   placeholder="" value="{{ old('saleUoM') }}" min="1">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="min_stock" class="col-md-4 col-form-label text-md-right">Min. Stock Quantity</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="min_stock_edit" name="min_stock"
                                   placeholder="" value="{{ old('min_stock') }}" min="1"
                                   onkeypress="return isNumberKey(event,this)">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="max_stock" class="col-md-4 col-form-label text-md-right">Max. Stock Quantity</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="max_stock_edit" name="max_stock"
                                   placeholder="" value="{{ old('max_stock') }}" min="1"
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
