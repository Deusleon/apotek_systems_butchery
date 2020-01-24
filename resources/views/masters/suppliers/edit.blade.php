<div class="modal fade" id="edit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <form action="{{route('suppliers.update','id')}}" method="post">
                        @csrf()
                        @method("PUT")
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name<font
                                    color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="name_edit" name="name"
                                       aria-describedby="emailHelp" required="true" maxlength="20">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="contact_person" class="col-md-4 col-form-label text-md-right">Contact
                                Person</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="contact_edit" name="contact_person"
                                       aria-describedby="emailHelp"
                                       maxlength="20">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">Phone<font
                                    color="red">*</font></label>
                            <div class="col-md-8">
                                {{--                                <input type="text" class="form-control  mob_no" id="phone_edit" name="mobile"--}}
                                {{--                                       data-mask="9999 999 999"--}}
                                {{--                                       pattern="^0\d{3}[\-]\d{3}[\-]\d{3}$"--}}
                                {{--                                       title="Eg:0777-777-777" required="true">--}}
                                <input type="text" class="form-control"
                                       id="phone_edits" name="mobile" required style="width: 134%;">
                                <span id="valid-msg" class="hide"></span>
                                <span id="error-msg" class="text text-danger"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                            <div class="col-md-8">
                                <input type="email" class="form-control" id="email_edit" name="email"
                                       aria-describedby="emailHelp"
                                       maxlength="40">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="contact_person" class="col-md-4 col-form-label text-md-right">Address</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="address_edit" name="address"
                                       aria-describedby="emailHelp"
                                       maxlength="90">
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
