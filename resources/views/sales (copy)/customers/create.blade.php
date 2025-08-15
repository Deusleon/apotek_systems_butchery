<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Add New Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <form action="{{ route('customers.store') }}" method="post" id="customerForm">
                        @csrf()
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Name<font color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name') }}" required>
                                    @if($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="email" name="email"
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                                           title="Eg:info@softlink.tz"
                                           placeholder="Enter Email Address">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">Phone<font color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ old('phone') }}" required>
                                    @if($errors->has('phone'))
                                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="address" class="col-md-4 col-form-label text-md-right">Address</label>
                                <div class="col-md-8">
                                    <textarea type="text" class="form-control" rows="1" name="address"
                                              aria-describedby="emailHelp" id="address"
                                              placeholder="Enter Address"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="tin" class="col-md-4 col-form-label text-md-right">TIN</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="tin" name="tin"
                                           placeholder="Enter TIN">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="credit_limit" class="col-md-4 col-form-label text-md-right">Credit Limit<font color="red">*</font></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="credit_limit" name="credit_limit"
                                           placeholder="Enter Credit Limit" value="0.00" required>
                                </div>
                            </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="customer_save_btn">Save</button>
            </div>
        </form>
    </div>
</div>
</div>
