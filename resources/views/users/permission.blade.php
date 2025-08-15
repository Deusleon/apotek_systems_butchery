<div class="modal fade" id="add-permission" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" >Add User Permission </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{ route('add.permission') }}" aria-label="{{ __('Permission') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}<font color="red">*</font></label>

                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                                <span class="text-danger">
                                    <strong id="name-error"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">Category Name <font color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control select2"  class="form-control" name="category"  data-placeholder="Select Module Name" required data-width="100%">
                                        <option value="ACCOUNTING">ACCOUNTING</option>
                                        <option value="DASHBOARD">DASHBOARD</option>
                                        <option value="INVENTORY">INVENTORY</option>
                                        <option value="PURCHASING">PURCHASING</option>
                                        <option value="REPORTS">REPORTS</option>
                                        <option value="SALES">SALES</option>
                                        <option value="SETTINGS">SETTINGS</option>
                                </select>
                                <span class="text-danger">
                                    <strong id="role-error"></strong>
                                </span>
                            </div>
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>


            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
