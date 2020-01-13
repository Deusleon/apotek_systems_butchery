  <div class="modal fade" id="register" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">

            <div class="modal-header">
                    <h5 class="modal-title" >Add User </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{ route('users.register') }}" aria-label="{{ __('Register') }}">
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
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }} <font color="red">*</font></label>

                            <div class="col-md-8">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                <span class="text-danger">
                                     <strong id="email-error"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                                <label for="mobile" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>

                                <div class="col-md-8">
                                    <input id="mobile" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{ old('mobile') }}" data-inputmask='"mask": "0999-999-9999"' data-mask>

                                    <span class="text-danger">
                                         <strong id="email-error"></strong>
                                    </span>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="job" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}</label>

                                <div class="col-md-8">
                                    <input id="job" type="text" class="form-control" name="position" value="{{ old('position') }}">

                                    <span class="text-danger">
                                         <strong id="postion-error"></strong>
                                    </span>
                                </div>
                        </div>
                        <div class="form-group row">
                                <label for="new_password" class="col-md-4 col-form-label text-md-right">Password <font color="red">*</font></label>

                                <div class="col-md-8">
                                    <input  type="password" class="form-control" name="password" required>

                                    <span class="text-danger">
                                        <strong id="password-error"></strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="username" class="col-md-4 col-form-label text-md-right">Confirm Password <font color="red">*</font></label>

                                <div class="col-md-8">
                                    <input  type="password" class="form-control" name="password_confirmation" id="password_confirmation"  required>

                                    <span class="text-danger">
                                        <strong id="password-confirmation-error"></strong>
                                    </span>
                                </div>
                            </div>

                        <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }} <font color="red">*</font></label>
                                <div class="col-md-8">
                                        <select class="form-control select2"  class="form-control" id="role" name="role[]"  data-placeholder="Select Role" required data-width="100%">
                                                @foreach(getRoles() as $role)
                                                    <option value="{{$role->id}}" {{ ($role->id == old('role') ? "selected":"") }}>{{$role->name}}</option>
                                                @endforeach
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
