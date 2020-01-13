<div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Item Return Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
      <form action="{{ route('sale-returns-approval.approve') }}" method="post"  enctype="multipart/form-data">
          @csrf()
             <div class="modal-body">
              <div class="row">
                <div class="col-md-12 form-group" >
                  
                </div>
              </div>
                  <div class="col-md-12 form-group">
                  <label for="code">Return Reason</label>
                  <textarea type="text" id="reason" class="form-control" readonly></textarea>
                  </div>
                   <div class="col-md-12 form-group">
                  <label for="code">Remark</label>
                  <textarea type="text" class="form-control"
                         name="remark"
                         placeholder="Write a Remark" required></textarea>
                  </div>
                   <input type="hidden" name="detail_id" id="item_detail_id" value="">
                   <input type="hidden" name="returned_qty" id="r_qty" value="">
                   <input type="hidden" name="original_qty" id="qty" value="">
                   <input type="hidden" name="stock_id" id="stock-id" value="">
     
      </div>
       <div class="modal-footer">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                               <button type="submit" class="btn btn-primary">Save</button>
                      </div>
      </form>
   
           
        </div>
    </div>
</div>
