<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_product_edit" action="{{route('products.update', ['product' => ':id'])}}" method="post">
                @csrf()
                @method("PUT")

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Product Name --}}
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">
                                    Product Name<span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name_edit" name="name" maxlength="50"
                                        minlength="2" required value="{{ old('name') }}">
                                </div>
                            </div>

                            {{-- Barcode --}}
                            <div class="form-group row">
                                <label for="barcode" class="col-md-4 col-form-label text-md-right">Barcode</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="barcode_edits" name="barcode"
                                        value="{{ old('barcode') }}" autocomplete="off">
                                </div>
                            </div>

                            {{-- Brand --}}
                            <div class="form-group row">
                                <label for="brand" class="col-md-4 col-form-label text-md-right">Brand</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="brand_edits" name="brand"
                                        value="{{ old('brand') }}">
                                </div>
                            </div>

                            {{-- Pack Size --}}
                            <div class="form-group row">
                                <label for="pack_size" class="col-md-4 col-form-label text-md-right">Pack Size</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="pack_size_edit" name="pack_size"
                                        value="{{ old('pack_size') }}" placeholder="e.g. 6, 12, 24"
                                        onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Category --}}
                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">Category <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select name="category" class="form-control" id="category_options" required
                                        onchange="createOption()">
                                        <option selected value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="category_border"
                                        style="display: none; color: red; font-size: 0.9em">category
                                        required</span>
                                </div>
                            </div>

                            {{-- Unit of Measure --}}
                            <div class="form-group row">
                                <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="sale_edit" name="sale_uom"
                                        placeholder="e.g. pcs, kg, ml" value="{{ old('sale_uom') }}">
                                </div>
                            </div>

                            {{-- Min Stock --}}
                            <div class="form-group row">
                                <label for="min_quantinty" class="col-md-4 col-form-label text-md-right">Min. Stock
                                    </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="min_stock_edit" name="min_quantinty"
                                        value="{{ old('min_quantinty') }}" onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>

                            {{-- Max Stock --}}
                            <div class="form-group row">
                                <label for="max_quantinty" class="col-md-4 col-form-label text-md-right">Max. Stock
                                    </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="max_stock_edit" name="max_quantinty"
                                        value="{{ old('max_quantinty') }}" onkeypress="return isNumberKey(event,this)">
                                </div>
                            </div>
                            {{-- Status --}}
                            <div class="form-group row">
                                <label for="max_quantinty" class="col-md-4 col-form-label text-md-right">Status</label>
                                <div class="col-md-8">
                                    <select name="status" class="form-control" id="status_edit" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
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