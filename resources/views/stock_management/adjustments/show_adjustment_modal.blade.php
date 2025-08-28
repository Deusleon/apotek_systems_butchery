<!-- Show Stock Adjustment Modal -->
<div class="modal fade small-table" id="showStockAdjustment" tabindex="-1" role="dialog"
    aria-labelledby="showStockAdjustmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Stock Adjustment Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col mb-2">
                    <div class="row">
                            <strong class="">Product Name:</strong>
                            <p id="show_product_name" class="text-body ml-1"></p>
                    </div>
                </div>
                <table class="display table table-sm nowrap table-striped table-hover mb-6">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Old Qty</th>
                            <th>New Qty</th>
                            <th>Reason</th>
                            <th>Adjusted By</th>
                        </tr>
                    </thead>
                    <tbody id="show_items_table_body">
                        <!-- Items will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>