<div class="modal fade" id="editVehicle" tabindex="-1" role="dialog" aria-labelledby="editVehicleTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Vehicle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action="" id="editVehicleForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_plate_number">Plate Number</label><font color="red">*</font>
                                        <input id="edit_plate_number" type="text" class="form-control" name="plate_number" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_transporter_id">Transporter</label><font color="red">*</font>
                                        <select class="form-control" name="transporter_id" id="edit_transporter_id" required>
                                            <option value="">Select Transporter</option>
                                            @foreach($transporters as $transporter)
                                                <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_vehicle_type">Vehicle Type</label><font color="red">*</font>
                                        <select class="form-control" name="vehicle_type" id="edit_vehicle_type" required>
                                            @foreach(App\Vehicle::typeOptions() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_capacity">Capacity (tons)</label><font color="red">*</font>
                                        <input id="edit_capacity" type="number" step="0.01" class="form-control" name="capacity" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_make">Make</label>
                                        <input id="edit_make" type="text" class="form-control" name="make">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_model">Model</label>
                                        <input id="edit_model" type="text" class="form-control" name="model">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_year">Year</label>
                                        <input id="edit_year" type="number" class="form-control" name="year">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_color">Color</label>
                                        <input id="edit_color" type="text" class="form-control" name="color">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_status">Status</label>
                                        <select class="form-control" name="status" id="edit_status">
                                            @foreach(App\Vehicle::statusOptions() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_chassis_number">Chassis Number</label>
                                        <input id="edit_chassis_number" type="text" class="form-control" name="chassis_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_engine_number">Engine Number</label>
                                        <input id="edit_engine_number" type="text" class="form-control" name="engine_number">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_documents">Documents (Fitness/Insurance/Permit)</label>
                                        <input id="edit_documents" type="file" class="form-control" name="documents[]" multiple>
                                        <small class="text-muted">Current documents will remain unless replaced</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_fitness_expiry">Fitness Expiry</label>
                                        <input id="edit_fitness_expiry" type="date" class="form-control" name="fitness_expiry">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_insurance_expiry">Insurance Expiry</label>
                                        <input id="edit_insurance_expiry" type="date" class="form-control" name="insurance_expiry">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="edit_permit_expiry">Permit Expiry</label>
                                        <input id="edit_permit_expiry" type="date" class="form-control" name="permit_expiry">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="edit_notes">Notes</label>
                                        <textarea id="edit_notes" class="form-control" name="notes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Vehicle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // When edit button is clicked
    $('.edit-vehicle-btn').click(function() {
        var vehicleId = $(this).data('id');
        
        // Fetch vehicle data
        $.get('/vehicles/' + vehicleId + '/edit', function(data) {
            // Populate form fields
            $('#edit_plate_number').val(data.plate_number);
            $('#edit_transporter_id').val(data.transporter_id);
            $('#edit_vehicle_type').val(data.vehicle_type);
            $('#edit_capacity').val(data.capacity);
            $('#edit_make').val(data.make);
            $('#edit_model').val(data.model);
            $('#edit_year').val(data.year);
            $('#edit_color').val(data.color);
            $('#edit_status').val(data.status);
            $('#edit_chassis_number').val(data.chassis_number);
            $('#edit_engine_number').val(data.engine_number);
            $('#edit_fitness_expiry').val(data.fitness_expiry);
            $('#edit_insurance_expiry').val(data.insurance_expiry);
            $('#edit_permit_expiry').val(data.permit_expiry);
            $('#edit_notes').val(data.notes);
            
            // Set form action URL
            $('#editVehicleForm').attr('action', '/vehicles/' + vehicleId);
            
            // Show modal
            $('#editVehicle').modal('show');
        });
    });
});
</script>
@endpush