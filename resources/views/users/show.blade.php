<div class="modal fade" id="showUser" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form>
                        @csrf

                        <div class="form-group row">
                            <label for="name1" class="col-md-4 col-form-label text-md-right">{{ __('Name') }} :</label>
                            <div class="col-md-4 col-form-label text-body" id="name1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email1" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}
                                :</label>
                            <div class="col-md-4 col-form-label text-body" id="email1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mobile1" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}
                                :</label>
                            <div class="col-md-4 col-form-label text-body" id="mobile1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="position1" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}
                                :</label>
                            <div class="col-md-4 col-form-label text-body" id="position1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role1" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }}
                                :</label>
                            <div class="col-md-4 col-form-label text-body" id="role1">
                            </div>
                            <input id="UserID" name="UserID" type="hidden">
                        </div>

                        <div class="form-group row">
                            <label for="store" class="col-md-4 col-form-label text-md-right">{{ __('User Branch') }}
                                :</label>
                            <div class="col-md-4 col-form-label text-body" id="store">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!--/.modal -->