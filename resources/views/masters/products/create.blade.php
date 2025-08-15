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

                            {{-- Product Name --}}
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">
                                    Product Name 
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name_edit" name="name"
                                           maxlength="50" minlength="2" required value="{{ old('name') }}">
                                </div>
                            </div>

                            {{-- Barcode --}}
                            <div class="form-group row">
                                <label for="barcode" class="col-md-4 col-form-label text-md-right">Barcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="barcode_edit" name="barcode"
                                           value="{{ old('barcode') }}" autocomplete="off">
                                </div>
                            </div>

                            {{-- Brand --}}
                            <div class="form-group row">
                                <label for="brand" class="col-md-4 col-form-label text-md-right">Brand</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="brand" name="brand"
                                           value="{{ old('brand') }}">
                                </div>
                            </div>

                            {{-- Pack Size --}}
                            <div class="form-group row">
                                <label for="pack_size" class="col-md-4 col-form-label text-md-right">Pack Size</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="pack_size" name="pack_size"
                                           value="{{ old('pack_size') }}" placeholder="" onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                            {{-- Category --}}
                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">Category </label>
                                <div class="col-md-8">
                                    <select name="category" class="form-control" id="category_option" required onchange="createOption()">
                                        <option selected value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="category_border" style="display: none; color: red; font-size: 0.9em">category required</span>
                                </div>
                            </div>

                            {{-- Unit of Measure --}}
                            <div class="form-group row">
                                <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit of Measure</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="saleUoM_edit" name="sale_uom" placeholder="eg. Kg, mls"
                                           value="{{ old('sale_uom') }}">
                                </div>
                            </div>

                            {{-- Min Stock --}}
                            <div class="form-group row">
                                <label for="min_quantinty" class="col-md-4 col-form-label text-md-right">Min. Stock Quantity</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="min_stock_edits" name="min_quantinty"
                                           value="{{ old('min_quantinty') }}" onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                            {{-- Max Stock --}}
                            <div class="form-group row">
                                <label for="max_quantinty" class="col-md-4 col-form-label text-md-right">Max. Stock Quantity</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="max_stock_edits" name="max_quantinty"
                                           value="{{ old('max_quantinty') }}" onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                           {{-- Product Type --}}
                            <!--<div class="form-group row">
                                <label for="product_type" class="col-md-4 col-form-label text-md-right">Type <font color="red">*</font></label>
                                <div class="col-md-8">
                                    <select name="product_type" class="form-control" id="product_type">
                                        <option selected value="stockable">Stockable</option>
                                        <option value="consumable">Service</option>
                                    </select>
                                </div>
                            </div>-->

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
