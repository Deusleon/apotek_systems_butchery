<div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Price Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                @csrf()

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Product Name</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="name_edit"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Price Category</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="price_category_edit"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Buying Price</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="unit_cost_edit"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Selling Price</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="sell_price_edit"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Batch Number</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="d_auto_4"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <b>Expiry Date</b>
                        </div>
                        <div class="col-sm-9">
                            <p id="d_auto_5"></p>
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
