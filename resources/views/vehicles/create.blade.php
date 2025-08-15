<div class="modal fade" id="createVehicle" tabindex="-1" role="dialog" aria-labelledby="createVehicleTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Vehicle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form method="POST" action="{{ route('vehicles.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="plate_number">Plate Number</label><font color="red">*</font>
                                        <input id="plate_number" type="text" class="form-control" name="plate_number" value="{{ old('plate_number') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="transporter_id">Transporter</label><font color="red">*</font>
                                        <select class="form-control" name="transporter_id" id="transporterSelect" required>
                                            <option value="">Select Transporter</option>
                                            @foreach($transporters as $transporter)
                                                <option value="{{ $transporter->id }}">
                                                    {{ $transporter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="vehicle_type">Vehicle Type</label><font color="red">*</font>
                                        <select class="form-control" name="vehicle_type" required>
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
                                        <label for="capacity">Capacity (tons)</label><font color="red">*</font>
                                        <input id="capacity" type="number" step="0.01" class="form-control" name="capacity" value="{{ old('capacity') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="make">Make</label>
                                        <input id="make" type="text" class="form-control" name="make" value="{{ old('make') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="model">Model</label>
                                        <input id="model" type="text" class="form-control" name="model" value="{{ old('model') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="year">Year</label>
                                        <input id="year" type="number" class="form-control" name="year" value="{{ old('year') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="color">Color</label>
                                        <input id="color" type="text" class="form-control" name="color" value="{{ old('color') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status">
                                            @foreach(App\Vehicle::statusOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ $value == 'active' ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="chassis_number">Chassis Number</label>
                                        <input id="chassis_number" type="text" class="form-control" name="chassis_number" value="{{ old('chassis_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="engine_number">Engine Number</label>
                                        <input id="engine_number" type="text" class="form-control" name="engine_number" value="{{ old('engine_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="documents">Documents</label>
                                        <input id="documents" type="file" class="form-control" name="documents[]" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fitness_expiry">Fitness Expiry</label>
                                        <input id="fitness_expiry" type="date" class="form-control" name="fitness_expiry" value="{{ old('fitness_expiry') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="insurance_expiry">Insurance Expiry</label>
                                        <input id="insurance_expiry" type="date" class="form-control" name="insurance_expiry" value="{{ old('insurance_expiry') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="permit_expiry">Permit Expiry</label>
                                        <input id="permit_expiry" type="date" class="form-control" name="permit_expiry" value="{{ old('permit_expiry') }}">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('transporterSelect');
    const options = Array.from(select.querySelectorAll('option:not(:first-child)'));
    
    options.sort((a, b) => {
        return a.textContent.trim().localeCompare(b.textContent.trim(), undefined, { sensitivity: 'base' });
    });
    
    options.forEach(option => select.appendChild(option));
});
</script>