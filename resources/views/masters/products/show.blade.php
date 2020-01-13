<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('products.update','id')}}" method="post">
                @csrf()
                @method("PUT")

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Product Name:</label>
                        <div class="col-md-3">
                            <textarea style="width:250px;height:55px;" type="text" readonly
                                      class="form-control-plaintext"
                                      value="email@example.com" id="name_edit" name="name"></textarea>
                        </div>
                        <label for="code" class="col-md-3 col-form-label text-md-right">BarCode:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="barcode_edit" name="barcode"
                                   value="email@example.com">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Generic Name:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="generic_edit"
                                   name="generic_name">
                        </div>
                        <label for="code" class="col-md-3 col-form-label text-md-right">Category:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="category_edit"
                                   value="email@example.com">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Sub Category:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="sub_category_edit">
                        </div>
                        <div class="col-md-3">
                            <label for="code" style="margin-right: -10%" class="col-form-label text-md-right">Standard
                                Unit Of Measure:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="standard_edit"
                                   name="standardUoM"
                                   value="email@example.com">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Sale Unit Of Measure:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="sale_edit" name="saleUoM">
                        </div>
                        <div class="col-md-3">
                            <label for="code" style="margin-right: -10%" class="col-form-label text-md-right">Purchase
                                Unit Of
                                Measure:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="purchase_edit"
                                   name="purchaseUoM"
                                   value="email@example.com">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Minimum Stock Quantity:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="min_stock_edit" name="min_stock">
                        </div>
                        <label for="code" class="col-md-3 col-form-label text-md-right">Maximum Stock Quantity:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="max_stock_edit"
                                   name="max_stock"
                                   value="email@example.com">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-3 col-form-label text-md-right">Dosage:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="dosage_edit">
                        </div>
                        <label for="code" class="col-md-3 col-form-label text-md-right">Indication:</label>
                        <div class="col-md-3">
                            <input type="text" readonly class="form-control-plaintext" id="indication_edit"
                                   value="email@example.com">
                        </div>
                    </div>

                    <input type="hidden" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
