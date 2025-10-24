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
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="form_product">
                        @csrf()
                        <div class="modal-body">
                            <div class="row">
                                <!-- Left Column - Always contains the basic fields -->
                                <div class="col-md-6" id="left-column">
                                    <div class="form-group row">
                                        <label for="product_name" class="col-md-4 col-form-label text-md-right">
                                            Name: <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="name_edit" name="name"
                                                aria-describedby="emailHelp" maxlength="50" minlength="2" placeholder=""
                                                required value="{{ old('name') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="barcode" class="col-md-4 col-form-label text-md-right">Barcode <span
                                                class="text-danger">*</span> </label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="barcode_edit" name="barcode"
                                                placeholder="" value="{{ old('barcode') }}" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleFormControlSelect1"
                                            class="col-md-4 col-form-label text-md-right">Category <font color="red">*
                                            </font></label>
                                        <div class="col-md-8">
                                            <select name="category" class="form-control" id="category_option" required
                                                onchange="createOption()">
                                                <option selected="true" value="0" disabled="disabled">Select Category
                                                </option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" name="category">
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <span id="category_border"
                                                style="display: none; color: red; font-size: 0.9em">category
                                                required</span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="saleUoM" class="col-md-4 col-form-label text-md-right">Unit of
                                            Measure <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="saleUoM_edit" name="saleUoM"
                                                placeholder="" value="{{ old('saleUoM') }}" min="1" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="min_stock" class="col-md-4 col-form-label text-md-right">Min. Stock
                                            Quantity <span class="text-danger">*</span></label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="min_stock_edits"
                                                name="min_stock" placeholder="" value="{{ old('min_stock') }}" min="1"
                                                onkeypress="return isNumberKey(event,this)" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Right Column - Contains additional fields for Detailed mode -->
                                <div class="col-md-6" id="right-column">
                                    <div class="form-group row product-detail-field" id="max_stock_field">
                                        <label for="max_stock" class="col-md-4 col-form-label text-md-right">Max. Stock
                                            Quantity</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="max_stock_edits"
                                                name="max_stock" placeholder="" value="{{ old('max_stock') }}" min="1"
                                                onkeypress="return isNumberKey(event,this)">
                                        </div>
                                    </div>

                                    <div class="form-group row product-detail-field" id="brand_field">
                                        <label for="brand" class="col-md-4 col-form-label text-md-right">Brand</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="brand_edit" name="brand"
                                                placeholder="" value="{{ old('brand') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row product-detail-field" id="pack_size_field">
                                        <label for="pack_size" class="col-md-4 col-form-label text-md-right">Pack Size</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="pack_size_edit" name="pack_size"
                                                placeholder="e.g. 6, 12, 24" value="{{ old('pack_size') }}">
                                        </div>
                                    </div>

                                    <div class="form-group row product-detail-field" id="product_type_field">
                                        <label for="product_type" class="col-md-4 col-form-label text-md-right">Type
                                            <font color="red">*
                                            </font>
                                        </label>
                                        <div class="col-md-8">
                                            <select name="product_type" class="form-control" id="product_type" required>
                                                <option selected value="stockable">Stockable</option>
                                                <option value="consumable">Service</option>
                                            </select>
                                        </div>
                                    </div>

                                    <input type="hidden" name="id" id="id">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>

                    <script>
                        $(document).ready(function() {
                            // Function to get product details option setting via AJAX
                            function getProductDetailsOption() {
                                var setting = 'Detailed'; // Default fallback
                                $.ajax({
                                    url: '{{ url("api/get-setting/127") }}',
                                    type: 'GET',
                                    async: false,
                                    success: function(data) {
                                        if (data && data.value) {
                                            setting = data.value;
                                            console.log('Fetched setting:', setting);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('AJAX Error:', status, error);
                                        console.log('Could not fetch product details option setting, using default');
                                    }
                                });
                                return setting;
                            }

                            // Function to apply field visibility based on setting
                            function applyFieldVisibility(setting) {
                                console.log('Applying visibility for setting:', setting);
                                if (setting === 'Normal') {
                                    // Hide fields not needed for Normal mode
                                    $('.product-detail-field').hide();
                                    // Arrange in single column for vertical layout
                                    $('#left-column').removeClass('col-md-6').addClass('col-md-12');
                                    $('#right-column').hide();
                                    console.log('Hiding product-detail-field elements and switching to single column');
                                } else {
                                    // Show all fields for Detailed mode (default)
                                    $('.product-detail-field').show();
                                    // Arrange in two columns
                                    $('#left-column').removeClass('col-md-12').addClass('col-md-6');
                                    $('#right-column').show();
                                    console.log('Showing product-detail-field elements and switching to two columns');
                                }
                            }

                            // Get current setting and apply visibility
                            var currentSetting = getProductDetailsOption();
                            console.log('Current setting loaded:', currentSetting);
                            applyFieldVisibility(currentSetting);

                            // Store setting in localStorage for potential future use
                            localStorage.setItem('product_details_option', currentSetting);

                            // Re-apply visibility when modal is shown (in case setting changes)
                            $('#create').on('show.bs.modal', function() {
                                var refreshedSetting = getProductDetailsOption();
                                console.log('Modal shown, refreshing setting:', refreshedSetting);
                                applyFieldVisibility(refreshedSetting);
                                localStorage.setItem('product_details_option', refreshedSetting);
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>