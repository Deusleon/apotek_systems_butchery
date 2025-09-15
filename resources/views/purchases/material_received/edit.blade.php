<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Material Receive</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form action="{{route('material.edit')}}" method="post">
                        @csrf()
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Product Name</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name_edit" name="name_edit"
                                           aria-describedby="emailHelp" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Quantity</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="quantity_edit" name="quantity_edit"
                                           onkeypress="return isNumberKey(event,this)" autocomplete="off" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Price <font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="price_edit" name="price_edit"
                                           onkeypress="return isNumberKey(event,this)" required autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="supplier_id" class="col-md-4 col-form-label text-md-right">Supplier <font color="red">*</font></label>
                                <div class="col-md-8">
                                    <select class="form-control" id="supplier_id_edit" name="supplier_id_edit" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($expire_date === "YES")
                                <div class="form-group row">
                                    <label for="code" class="col-md-4 col-form-label text-md-right">Expire Date <font
                                            color="red">*</font></label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="expire_date_edit"
                                               name="expire_date_edit" autocomplete="off" required>
                                    </div>
                                </div>
                            @else
                                <div class="form-group row">
                                    <label for="code" class="col-md-4 col-form-label text-md-right">Expire Date</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" id="expire_date_edit"
                                               name="expire_date_edit" autocomplete="off" readonly disabled>
                                    </div>
                                </div>
                            @endif
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Receive Date <font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="receive_date_edit"
                                           name="receive_date_edit" required autocomplete="off">
                                </div>
                            </div>

                            <input type="hidden" name="id" id="id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
