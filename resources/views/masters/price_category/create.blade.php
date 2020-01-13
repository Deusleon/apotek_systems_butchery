<div style="display: none" class="modal fade" id="create" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Price Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('price-categories.store')}}" method="post">
                @csrf()
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code"
                               class="col-md-4 col-form-label text-md-right">{{ __('Category Name') }} <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name_edit" name="name"
                                   aria-describedby="emailHelp"
                                   placeholder="" required maxlength="45">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Price Type') }} <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <select name="code" class="form-control" id="code_edit">
                                <option>CASH</option>
                                <option>BILL</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="price_category_id" id="price_category_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
