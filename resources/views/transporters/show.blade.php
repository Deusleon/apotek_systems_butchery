<!-- Show Transporter Modal -->
<div class="modal fade" id="showTransporter" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Transporter Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name:</label>
                            <input type="text" class="form-control" id="show_name" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Contact Person:</label>
                            <input type="text" class="form-control" id="show_contact_person" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone:</label>
                            <input type="text" class="form-control" id="show_phone" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="text" class="form-control" id="show_email" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Number of Vehicles:</label>
                            <input type="text" class="form-control" id="show_number_of_vehicles" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status:</label>
                            <div id="show_status" class="pt-2"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes:</label>
                            <textarea class="form-control" id="show_notes" rows="3" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>