<div class="modal fade" id="create_normal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
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
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form_product_normal">
                        @csrf()
                        <div class="modal-body">
                            <div class="row" id="form-layout-container">
                                {{-- Product Name --}}
                                <div class="col-12 d-flex mb-3">
                                    <label for="name" class="col-md-3 col-form-label text-md-right">
                                        Name<span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="name_edit_normal" name="name"
                                            maxlength="50" minlength="2" required value="{{ old('name') }}">
                                    </div>
                                </div>
                                {{-- Barcode --}}
                                <div class="col-12 d-flex mb-3">
                                    <label for="barcode" class="col-md-3 col-form-label text-md-right">Barcode</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="barcode_edit_normal" name="barcode"
                                            value="{{ old('barcode') }}" autocomplete="off">
                                    </div>
                                </div>
                                {{-- Category --}}
                                <div class="col-12 d-flex mb-3">
                                    <label for="category" class="col-md-3 col-form-label text-md-right">Category
                                        <span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <select name="category" class="form-control" id="category_option_normal"
                                            required onchange="createOptionNormal()">
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
                                <div class="col-12 d-flex mb-3">
                                    <label for="saleUoM" class="col-md-3 col-form-label text-md-right">Unit </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="saleUoM_edit_normal" name="sale_uom"
                                            placeholder="e.g. pcs, kg, ml" value="{{ old('sale_uom') }}">
                                    </div>
                                </div>
                                {{-- Min Stock --}}
                                <div class="col-12 d-flex mb-3">
                                    <label for="min_quantinty" class="col-md-3 col-form-label text-md-right">Min.
                                        Stock </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" id="min_stock_edits_normal"
                                            name="min_quantinty" value="{{ old('min_quantinty') }}"
                                            onkeypress="return isNumberKey(event,this)">
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="id" id="id">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>