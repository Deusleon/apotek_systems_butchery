<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Adjustment Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('adjustment-reasons.update','id')}}" method="post">
                @csrf()
                @method("PUT")

                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Reason <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name_edit" name="name"
                                   aria-describedby="emailHelp" maxlength="30" required="true">
                        </div>
                    </div>

                    <input type="hidden" name="adjustment_id" id="price_category_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
