<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Stock Adjustment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                @csrf()

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Product Name:</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="name_edit" name="name">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -5%">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Quantity Adjusted:</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="quantity_edit">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -5%">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Adjustment Type:</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="type">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -5%">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Adjustment Reason:</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="reason_edit">
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: -5%">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Description:</label>
                        <div class="col-md-6">
                            <input type="text" readonly class="form-control-plaintext"
                                   value="email@example.com" id="description_edit">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
