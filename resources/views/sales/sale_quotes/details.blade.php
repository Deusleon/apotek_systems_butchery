<div class="modal fade" id="quote-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Products List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4">
                        <div class="row">
                            <span class="col-12">Order Number:</span>
                            <span id="quote_no" class="text-body col-12"></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="row">
                            <span class="col-12">Customer Name:</span>
                            <span id="customer_name" class="text-body col-12"></span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="row">
                            <span class="col-12">Date:</span>
                            <span id="sales_date" class="text-body col-12"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="sales_history_table" class="table nowrap table-striped table-hover" width="100%">
                            <thead>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="table-responsive">
                    <table id="items_table" class="table nowrap table-striped table-hover" width="100%"
                        style="font-size: 14px;"></table>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <div class="quote_remark" id="quote_remark"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-rounded btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>