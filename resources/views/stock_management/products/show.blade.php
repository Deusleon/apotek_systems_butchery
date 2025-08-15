<!-- Product Details Modal -->
<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="productDetailsModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="productDetailsModal">
                    <i class="fas fa-box-open mr-2"></i>Product Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Product Header -->
                <div class="border-bottom pb-3 mb-4">
                    <h4 class="mb-2" id="name_edit"></h4>
                    <span class="badge badge-info mr-2" id="product_type"></span>
                    <span class="badge badge-primary" id="category_edit"></span>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle text-primary mr-2"></i>Basic Information
                            </h6>
                            <div class="mb-4">
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Brand</label>
                                    <div class="h6" id="brand_show"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Barcode</label>
                                    <div class="bg-light p-2 rounded" id="barcode_edit"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Information -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-warehouse text-primary mr-2"></i>Stock Information
                            </h6>
                            <div class="mb-4">
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Minimum Stock Level</label>
                                    <div class="h6" id="min_quantinty_show"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Maximum Stock Level</label>
                                    <div class="h6" id="max_quantinty_show"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Product Specifications -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-ruler text-primary mr-2"></i>Product Specifications
                            </h6>
                            <div class="mb-4">
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Pack Size</label>
                                    <div class="h6" id="pack_size_show"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-muted mb-2">Unit of Measure</label>
                                    <div class="h6" id="sale_edit"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
