<div class="modal fade small-table" id="acknowledgeModal" tabindex="-1" role="dialog"
  aria-labelledby="acknowledgeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="acknowledgeModalLabel">Acknowledge Transfer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <div class="mb-2">
            Transfer #: <span id="acknowledge_transfer_no" class="text-body"></span><br>
          </div>
          From: <span id="acknowledge_from_store" class="text-body mr-3"></span>
          To: <span id="acknowledge_to_store" class="text-body"></span><br>
        </div>
        <form id="transfer" action="{{ route('acknowledge-transfer') }}" method="post" enctype="multipart/form-data">
          @csrf
          @method('POST')

          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Transferred</th>
                  <th>Received</th>
                  <th>Receive</th>
                </tr>
              </thead>
              <tbody id="acknowledge_items_body"></tbody>
            </table>
          </div>
          <input type="hidden" name="transfer_no" id="acknowledge_transfer_no_input">
          <input type="hidden" name="from_id" id="acknowledge_from_store_input">
          <input type="hidden" name="to_id" id="acknowledge_to_store_input">
          <textarea name="remarks" id="acknowledge_remark" placeholder="Add your remarks here..." class="form-control"
            rows="3"></textarea>

          <div class="text-right mt-3">
            <a id="acknowledge_evidence" class="btn btn-sm btn-secondary"
              target="_blank">
              Evidence
            </a>
            <button type="button" id="edit-acknowledge-btn"
              class="btn btn-sm btn-warning edit-acknowledge-btn text-body">Edit</button>
            <button type="submit" class="btn btn-sm btn-primary">Acknowledge</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmAcknowledgeModal" tabindex="-1" role="dialog"
  aria-labelledby="confirmAcknowledgeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmAcknowledgeModalLabel">Confirm Acknowledge</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to acknowledge this transfer?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="confirmAcknowledgeBtn" class="btn btn-primary btn-sm">Yes</button>
      </div>
    </div>
  </div>
</div>