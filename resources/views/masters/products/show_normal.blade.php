<!-- Product Details Modal -->
<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="productDetailsModal" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white">
                <h5 class="modal-title text-black">
                    </i>Product Details
                </h5>
                <button type="button" class="close text-black" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 gap-3 width-100">
                    <label class="col-5 text-right">Name:</label>
                    <div class="col text-dark" id="name_edit"></div>
                </div>
                <div class="row mb-3 gap-3">
                    <label class="col-5 text-right">Barcode:</label>
                    <div class="col text-dark" id="barcode_edit"></div>
                </div>
                <div class="row mb-3 gap-3">
                    <label class="col-5 text-right">Category:</label>
                    <div class="col text-dark" id="category_edit"></div>
                </div>
                <div class="row mb-3 gap-3" hidden>
                    <label class="col-5 text-right">Unit:</label>
                    <div class="col text-dark" id="sale_edit"></div>
                </div>
                <div class="row mb-3 gap-3">
                    <label class="col-5 text-right">Min. Stock:</label>
                    <div class="col text-dark" id="min_quantinty_show"></div>
                </div>
                <div class="row gap-3">
                    <label class="col-5 text-right">Status:</label>
                    <div class="col text-dark" id="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>