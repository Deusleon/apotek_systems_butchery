<div class="modal fade" id="createTransporter" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Transporter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('transport-logistics.transporters.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Name *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Contact Person *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="contact_person" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Phone *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Email</label>
                        <div class="col-md-8">
                            <input type="email" class="form-control" name="email">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Transport Type *</label>
                        <div class="col-md-8">
                            <select class="form-control" name="transport_type" required>
                                <option value="road">Road</option>
                                <option value="rail">Rail</option>
                                <option value="air">Air</option>
                                <option value="sea">Sea</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Number of Vehicles *</label>
                        <div class="col-md-8">
                            <input type="number" class="form-control" name="number_of_vehicles" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Status *</label>
                        <div class="col-md-8">
                            <select class="form-control" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Notes</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Transporter</button>
                </div>
            </form>
        </div>
    </div>
</div>