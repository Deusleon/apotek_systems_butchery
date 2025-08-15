<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicle" tabindex="-1" role="dialog" aria-labelledby="editVehicleLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Edit Vehicle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="form_vehicle_edit" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="vehicle_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Plate Number</label><font color="red">*</font>
                                <input type="text" class="form-control" name="plate_number" id="plate_number_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="transporter_id">Transporter</label><font color="red">*</font>
                                <select class="form-control" name="transporter_id" id="transporter_id_edit" required>
                                    <option value="">Select Transporter</option>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vehicle Type</label><font color="red">*</font>
                                <select class="form-control" name="vehicle_type" id="vehicle_type_edit" required>
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
                                <label>Capacity (tons)</label><font color="red">*</font>
                                <input type="number" class="form-control" name="capacity" id="capacity_edit" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Make</label>
                                <input type="text" class="form-control" name="make" id="make_edit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Model</label>
                                <input type="text" class="form-control" name="model" id="model_edit">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Year</label>
                                <input type="number" class="form-control" name="year" id="year_edit" min="1900" max="{{ date('Y') + 1 }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="text" class="form-control" name="color" id="color_edit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status_edit">
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
                                <label for="chassis_number_edit">Chassis Number</label>
                                <input id="chassis_number_edit" type="text" class="form-control" name="chassis_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="engine_number_edit">Engine Number</label>
                                <input id="engine_number_edit" type="text" class="form-control" name="engine_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="documents_edit">Documents</label>
                                <input id="documents_edit" type="file" class="form-control" name="documents[]" multiple>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="fitness_expiry_edit">Fitness Expiry</label>
                                <input id="fitness_expiry_edit" type="date" class="form-control" name="fitness_expiry">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="insurance_expiry_edit">Insurance Expiry</label>
                                <input id="insurance_expiry_edit" type="date" class="form-control" name="insurance_expiry">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="permit_expiry_edit">Permit Expiry</label>
                                <input id="permit_expiry_edit" type="date" class="form-control" name="permit_expiry">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" id="notes_edit" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Display existing documents -->
                    <div class="row" id="existing-documents-container">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Existing Documents</label>
                                <div class="d-flex flex-wrap" id="existing-documents">
                                    <!-- Documents will be loaded here via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('transporter_id_edit');
    const options = Array.from(select.querySelectorAll('option:not(:first-child)'));
    
    options.sort((a, b) => {
        return a.textContent.trim().localeCompare(b.textContent.trim(), undefined, { sensitivity: 'base' });
    });
    
    options.forEach(option => select.appendChild(option));
});
</script>