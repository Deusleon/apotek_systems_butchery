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
            <div>
              <div>Receipt Number: <span id="receipt_no" class="text-body"></span></div> 
              <div>Custmer Name: <span id="customer_name" class="text-body"></span> </div>
              <div>Date: <span id="sales_date" class="text-body"></span> </div>
            </div>
            <div class="table-responsive">
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
