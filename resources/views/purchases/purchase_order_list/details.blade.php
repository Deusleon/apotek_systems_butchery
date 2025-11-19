<div class="modal fade" id="purchases-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ordered Products List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div style="overflow-x: auto;">
                    <table id="order_details_table" class="table nowrap table-striped table-hover"
                           style="width:100%; min-width: 600px;">
                    </table>
                </div>
            </div>
            <!-- Modal Footer (ADD THIS) -->
            <div class="modal-footer">
                @if (Auth::user()->checkPermission('Approve Purchase Order'))
                                <!-- Cancel in modal (opens your existing cancel confirmation modal) -->
                <button type="button" id="cancel_btn_modal" class="btn btn-danger">
                    Reject
                </button>

                 <!-- Approve in modal -->
                <button type="button" id="approve_btn" class="btn btn-primary">
                    Approve
                </button>

                @endif

                <!-- Status message for non-actionable orders -->
                <div id="status_message" class="alert alert-info d-none">
                    This order cannot be modified as it's already processed.
                </div>
                
            </div>
        </div>
    </div>
</div>
