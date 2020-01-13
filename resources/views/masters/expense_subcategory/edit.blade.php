<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Expense Subcategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('expense-subcategories.update','id') }}" method="post">
                @csrf()
                @method("PUT")
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Expense Category Name <font
                                color="red">*</font></label>
                        <div class="col-md-6">
                            <select name="expense_category_id" class="form-control" id="exampleFormControlSelect1"
                                    required="true">
                                <option value="">Select Category...</option>
                                @foreach($expense_categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-6 col-form-label text-md-right">Expense Subcategory Name <font
                                color="red">*</font></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="subcategory_name" name="name"
                                   placeholder="Enter Expense Subcategory Name" required="true" maxlength="30">
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id_edit">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
