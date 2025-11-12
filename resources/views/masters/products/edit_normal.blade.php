<div class="modal fade" id="edit_normal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form_product_edit_normal" action="{{route('products.update', ['product' => ':id'])}}" method="post">
                @csrf()
                @method("PUT")

                <div class="modal-body pl-0 pr-0">
                    {{-- Product Name --}}
                    <div class="col-12 d-flex mb-3">
                        <label for="name" class="col-md-3 col-form-label text-md-right">
                            Name<span class="text-danger">*</span>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="name_edit_normal" name="name" maxlength="100"
                                minlength="2" required value="{{ old('name') }}">
                        </div>
                    </div>

                    {{-- Barcode --}}
                    <div class="col-12 d-flex mb-3">
                        <label for="barcode" class="col-md-3 col-form-label text-md-right">Barcode</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="barcode_edits_normal" name="barcode"
                                value="{{ old('barcode') }}" autocomplete="off">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-12 d-flex mb-3">
                        <label for="category" class="col-md-3 col-form-label text-md-right">Category <span
                                class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="category" class="form-control" id="category_options_normal" required
                                onchange="createOption()">
                                <option selected value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span id="category_border_normal" style="display: none; color: red; font-size: 0.9em">category
                                required</span>
                        </div>
                    </div>
                    {{-- Unit of Measure --}}
                    {{-- <div class="col-12 d-flex mb-3" hidden>
                        <label for="saleUoM" class="col-md-3 col-form-label text-md-right">Unit</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="sale_edit_normal" name="sale_uom"
                                placeholder="e.g. pcs, kg, ml" value="{{ old('sale_uom') }}">
                        </div>
                    </div> --}}

                    {{-- Min Stock --}}
                    <div class="col-12 d-flex mb-3">
                        <label for="min_quantinty" class="col-md-3 col-form-label text-md-right">Min. Stock
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="min_stock_edit_normal" name="min_quantinty"
                                value="{{ old('min_quantinty') }}" onkeypress="return isNumberKey(event,this)">
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-12 d-flex mb-3">
                        <label for="max_quantinty" class="col-md-3 col-form-label text-md-right">Status</label>
                        <div class="col-md-9">
                            <select name="status" class="form-control" id="status_edit_normal" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                <input type="hidden" name="code" id="code_edit_normal">
                <input type="hidden" name="id" id="id_normal">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
        </form>
    </div>
</div>
</div>