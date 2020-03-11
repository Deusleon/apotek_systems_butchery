<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
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
                        <label for="code" class="col-md-5 col-form-label text-md-right">Product Name:</label>
                        <div class="col-md-7">
                            <textarea style="width:250px;height:55px;" type="text" readonly
                                      class="form-control-plaintext"
                                      value="email@example.com" id="name_edit" name="name"></textarea>
                        </div>
                    </div>

                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-5 col-form-label text-md-right">Barcode:</label>
                        <div class="col-md-7">
                            <input type="text" readonly class="form-control-plaintext" id="barcode_edit" name="barcode"
                                   value="email@example.com">
                        </div>
                    </div>

                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-5 col-form-label text-md-right">Category:</label>
                        <div class="col-md-7">
                            <input type="text" readonly class="form-control-plaintext" id="category_edit"
                                   value="email@example.com">
                        </div>
                    </div>

                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-5 col-form-label text-md-right">Unit of Measure:</label>
                        <div class="col-md-7">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="sale_edit" name="saleUoM">
                        </div>
                    </div>

                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-5 col-form-label text-md-right">Min. Stock Quantity:</label>
                        <div class="col-md-7">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="min_stock_edit" name="min_stock">
                        </div>
                    </div>

                    <div class="form-group row" style="margin-top: -2%">
                        <label for="code" class="col-md-5 col-form-label text-md-right">Max. Stock Quantity:</label>
                        <div class="col-md-7">
                            <input type="text" readonly class="form-control-plaintext" id="max_stock_edit"
                                   name="max_stock"
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
