<div class="modal fade" id="sale-details" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sale Products List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-4">
            <div class="row">
              <span class="col-12">Receipt Number:</span>
              <span id="receipt_no" class="text-body col-12"></span>
            </div>
          </div>
          <div class="col-4">
            <div class="row">
              <span class="col-12">Custmer Name:</span>
              <span id="customer_name" class="text-body col-12"></span>
            </div>
          </div>
          <div class="col-4">
            <div class="row">
              <span class="col-12">Date:</span>
              <span id="sales_date" class="text-body col-12"></span>
            </div>
          </div>
          <div class="table-responsive mt-3">
            <table id="sales_history_table" class="table nowrap table-striped table-hover" width="100%">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Price</th>
                  <th>Quantity</th>
                </tr>
              </thead>
              <tbody>


              </tbody>
            </table>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-rounded btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>