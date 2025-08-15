<div class="modal fade" id="editTransporter" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Transporter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="form_transporter_edit" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="transporter_id" name="id">
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Name *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" id="name_edit" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Contact Person *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="contact_person" id="contact_person_edit" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Phone *</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="phone" id="phone_edit" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Email</label>
                        <div class="col-md-8">
                            <input type="email" class="form-control" name="email" id="email_edit">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Transport Type *</label>
                        <div class="col-md-8">
                            <select class="form-control" name="transport_type" id="transport_type_edit" required>
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
                            <input type="number" class="form-control" name="number_of_vehicles" id="number_of_vehicles_edit" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Status *</label>
                        <div class="col-md-8">
                            <select class="form-control" name="status" id="status_edit" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Notes</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="notes" id="notes_edit" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Transporter</button>
                </div>
            </form>
        </div>
    </div>
</div>