<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Product Subcategory</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('sub-categories.update','id')}}" method="post">
                @csrf()
                @method("PUT")

                <div class="modal-body">
                     <div class="form-group">
                        <label for="code">Category</label>
                        <select name="category_id" class="form-control" id="category_edit">
                             <option value="">Select Subcategory..</option>
                            @foreach($categories as $category)
                             <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                           </div>
                    <div class="form-group">
                        <label for="code">Subcategory Name</label>
                        <input type="text" class="form-control" id="subcategory_edit" name="subcategory_name" aria-describedby="emailHelp"  maxlength = "30"
                               placeholder="Enter Product Sub Category" required="true">
                    </div>

                    <input type="hidden" name="id" id="id_edit">
                </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
