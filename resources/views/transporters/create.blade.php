<div class="modal fade" id="register" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Transporter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action="{{ route('transport-logistics.transporters.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Name</label><font color="red">*</font>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                        <span class="text-danger"><strong id="name-error"></strong></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="registration_number">Reg. Number</label><font color="red">*</font>
                                        <input id="registration_number" type="text" class="form-control" name="registration_number" value="{{ old('registration_number') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="business_type">Business Type</label><font color="red">*</font>
                                        <select class="form-control" name="business_type" required>
                                            <option value="company">Company</option>
                                            <option value="individual">Individual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tin_number">TIN Number</label>
                                        <input id="tin_number" type="text" class="form-control" name="tin_number" value="{{ old('tin_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_person">Contact Person</label><font color="red">*</font>
                                        <input id="contact_person" type="text" class="form-control" name="contact_person" value="{{ old('contact_person') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone">Phone</label><font color="red">*</font>
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="physical_address">Address</label>
                                        <textarea id="physical_address" class="form-control" name="physical_address" rows="1">{{ old('physical_address') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="region">Region</label>
                                        <input id="region" type="text" class="form-control" name="region" value="{{ old('region') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <input id="district" type="text" class="form-control" name="district" value="{{ old('district') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="transport_type">Transport Type</label><font color="red">*</font>
                                        <select class="form-control" name="transport_type" required>
                                            <option value="road">Road</option>
                                            <option value="air">Air</option>
                                            <option value="sea">Sea</option>
                                            <option value="rail">Rail</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="number_of_vehicles">No. of Vehicles</label>
                                        <input id="number_of_vehicles" type="number" class="form-control" name="number_of_vehicles" value="{{ old('number_of_vehicles', 1) }}" min="1">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="insurance_coverage">Insurance</label>
                                        <select class="form-control" name="insurance_coverage">
                                            <option value="Comprehensive">Comprehensive</option>
                                            <option value="Third Party">Third Party</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bank_name">Bank Name</label>
                                        <input id="bank_name" type="text" class="form-control" name="bank_name" value="{{ old('bank_name') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="account_number">Account No.</label>
                                        <input id="account_number" type="text" class="form-control" name="account_number" value="{{ old('account_number') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contract_start_date">Contract Start</label>
                                        <input id="contract_start_date" type="date" class="form-control" name="contract_start_date" value="{{ old('contract_start_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contract_end_date">Contract End</label>
                                        <input id="contract_end_date" type="date" class="form-control" name="contract_end_date" value="{{ old('contract_end_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea id="notes" class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
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