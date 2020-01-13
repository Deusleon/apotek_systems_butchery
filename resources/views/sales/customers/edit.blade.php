<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Update Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('customers.update','id') }}" method="post">
                        @csrf()
                        @method('PUT')
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Name<font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name_edit" name="name"
                                           aria-describedby="emailHelp"
                                           placeholder="Enter Name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="email_edit" name="email"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                                           title="Eg:info@softlink.tz"
                                           placeholder="Enter Email Address">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Phone<font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control"
                                           id="phone_edit" name="phone" required>
                                    <span id="valid-msg-edit" class="hide"></span>
                                    <span id="error-msg-edit" class="text text-danger"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Address</label>
                                <div class="col-md-8">
                                    <textarea type="text" class="form-control" id="address_edit" rows="1"
                                              name="address" aria-describedby="emailHelp"
                                              placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Credit Limit<font
                                        color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="credit_input_edit"
                                           placeholder="Enter Credit Limit" required>
                                </div>
                            </div>

                            <input type="hidden" name="credit_limit" id="credit_limit_amount_edit">
                            <input type="hidden" name="id" id="id_edit">
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="edit_btn">Save</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
