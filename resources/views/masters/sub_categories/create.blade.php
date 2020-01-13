<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Product Subcategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('sub-categories.store')}}" method="post">
                @csrf()
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Category <font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <select name="category_id" class="form-control" id="category_edit"
                                    required="true">
                                <option value="" selected disabled>Select Category..</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">SubCategory<font
                                color="red">*</font></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="subcategory_edit" name="subcategory_name"
                                   aria-describedby="emailHelp" required="true" maxlength="30">
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
