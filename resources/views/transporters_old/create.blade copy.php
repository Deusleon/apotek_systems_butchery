<div class="modal fade" id="register" role="dialog">
    <div class="modal-dialog modal-xl" role="document"> <!-- Changed to modal-xl for extra large size -->
        <div class="modal-content">
        <div class="modal-header">
                <h5 class="modal-title">Add Transporter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px 30px;"> <!-- Increased padding -->
                <div class="panel-body">
                    <form method="POST" action="{{ route('transport-logistics.transporters.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Basic Information Column -->
                            <div class="col-md-6 pr-4"> <!-- Added right padding -->
                                <div class="form-group row mb-4"> <!-- Increased margin-bottom -->
                                    <label for="name" class="col-md-4 col-form-label text-right pr-2"> <!-- Right-aligned labels -->
                                        <span>Name</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                                        <span class="text-danger"><strong id="name-error"></strong></span>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="registration_number" class="col-md-4 col-form-label text-right pr-2">
                                        <span>Reg. Number</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <input id="registration_number" type="text" class="form-control" name="registration_number" value="{{ old('registration_number') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="business_type" class="col-md-4 col-form-label text-right pr-2">
                                        <span>Business Type</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="business_type" required>
                                            <option value="company">Company</option>
                                            <option value="individual">Individual</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="tin_number" class="col-md-4 col-form-label text-right pr-2">
                                        TIN Number
                                    </label>
                                    <div class="col-md-8">
                                        <input id="tin_number" type="text" class="form-control" name="tin_number" value="{{ old('tin_number') }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="contact_person" class="col-md-4 col-form-label text-right pr-2">
                                        <span>Contact Person</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <input id="contact_person" type="text" class="form-control" name="contact_person" value="{{ old('contact_person') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="phone" class="col-md-4 col-form-label text-right pr-2">
                                        <span>Phone</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="email" class="col-md-4 col-form-label text-right pr-2">
                                        Email
                                    </label>
                                    <div class="col-md-8">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Address & Transport Details Column -->
                            <div class="col-md-6 pl-4"> <!-- Added left padding -->
                                <div class="form-group row mb-4">
                                    <label for="physical_address" class="col-md-4 col-form-label text-right pr-2">
                                        Address
                                    </label>
                                    <div class="col-md-8">
                                        <textarea id="physical_address" class="form-control" name="physical_address" rows="2">{{ old('physical_address') }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="region" class="col-md-4 col-form-label text-right pr-2">
                                        Region
                                    </label>
                                    <div class="col-md-8">
                                        <input id="region" type="text" class="form-control" name="region" value="{{ old('region') }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="district" class="col-md-4 col-form-label text-right pr-2">
                                        District
                                    </label>
                                    <div class="col-md-8">
                                        <input id="district" type="text" class="form-control" name="district" value="{{ old('district') }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="transport_type" class="col-md-4 col-form-label text-right pr-2">
                                        <span>Transport Type</span> <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="transport_type" required>
                                            <option value="road">Road</option>
                                            <option value="air">Air</option>
                                            <option value="sea">Sea</option>
                                            <option value="rail">Rail</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="number_of_vehicles" class="col-md-4 col-form-label text-right pr-2">
                                        No. of Vehicles
                                    </label>
                                    <div class="col-md-8">
                                        <input id="number_of_vehicles" type="number" class="form-control" name="number_of_vehicles" value="{{ old('number_of_vehicles', 1) }}" min="1">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="vehicle_types" class="col-md-4 col-form-label text-right pr-2">
                                        Vehicle Types
                                    </label>
                                    <div class="col-md-8">
                                        <select id="vehicle_types" class="form-control select2" name="vehicle_types[]" multiple="multiple" data-placeholder="Select vehicle types">
                                            <option value="Truck">Truck</option>
                                            <option value="Van">Van</option>
                                            <option value="Pickup">Pickup</option>
                                            <option value="Trailer">Trailer</option>
                                            <option value="Container">Container</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="insurance_coverage" class="col-md-4 col-form-label text-right pr-2">
                                        Insurance
                                    </label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="insurance_coverage">
                                            <option value="Comprehensive">Comprehensive</option>
                                            <option value="Third Party">Third Party</option>
                                            <option value="None">None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Additional Information</h5>
                                
                                <div class="form-group row mb-4">
                                    <label for="bank_name" class="col-md-2 col-form-label text-right pr-2">
                                        Bank Name
                                    </label>
                                    <div class="col-md-4">
                                        <input id="bank_name" type="text" class="form-control" name="bank_name" value="{{ old('bank_name') }}">
                                    </div>
                                    
                                    <label for="account_number" class="col-md-2 col-form-label text-right pr-2">
                                        Account No.
                                    </label>
                                    <div class="col-md-4">
                                        <input id="account_number" type="text" class="form-control" name="account_number" value="{{ old('account_number') }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="contract_start_date" class="col-md-2 col-form-label text-right pr-2">
                                        Contract Start
                                    </label>
                                    <div class="col-md-4">
                                        <input id="contract_start_date" type="date" class="form-control" name="contract_start_date" value="{{ old('contract_start_date') }}">
                                    </div>
                                    
                                    <label for="contract_end_date" class="col-md-2 col-form-label text-right pr-2">
                                        Contract End
                                    </label>
                                    <div class="col-md-4">
                                        <input id="contract_end_date" type="date" class="form-control" name="contract_end_date" value="{{ old('contract_end_date') }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-4">
                                    <label for="status" class="col-md-2 col-form-label text-right pr-2">
                                        Status
                                    </label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="status">
                                            <option value="active" selected>Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <label for="notes" class="col-md-2 col-form-label text-right pr-2">
                                        Notes
                                    </label>
                                    <div class="col-md-4">
                                        <textarea id="notes" class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Transporter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page_scripts')
<script>
$(document).ready(function() {
    // Initialize select2 for multiple vehicle types
    $('.select2').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        width: '100%' // Make select2 full width
    });
});
</script>
@endpush