<div class="modal fade" id="showUser" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Details </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{route('users.update') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name1" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-8">
                                <input id="name1" type="text" class="form-control" name="name1"
                                       value="{{ old('name') }}" disabled>

                                <span class="text-danger">
                                    <strong id="name-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email1" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}
                                </label>

                            <div class="col-md-8">
                                <input id="email1" type="email" class="form-control" name="email1"
                                       value="{{ old('email1') }}" disabled>

                                <span class="text-danger">
                                     <strong id="email-error1"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mobile1"
                                   class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>

                            <div class="col-md-8">
                                <input id="mobile1" type="text"
                                       class="form-control{{ $errors->has('mobile1') ? ' is-invalid' : '' }}"
                                       name="mobile1" value="{{ old('mobile1') }}"
                                       data-inputmask='"mask": "0999-999-9999"' data-mask disabled>

                                <span class="text-danger">
                                         <strong id="mobile-error1"></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="job1" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}</label>

                            <div class="col-md-8">
                                <input id="position1" type="text" class="form-control" name="position1"
                                       value="{{ old('position1') }}" disabled>

                                <span class="text-danger">
                                         <strong id="position-error1"></strong>
                                    </span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="role1" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }} <font
                                    color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control select2" class="form-control" id="role1" name="role1[]"
                                        data-placeholder="Select Role" required data-width="100%" disabled="true">
                                    @foreach(getRoles() as $role)
                                        <option
                                            value="{{$role->name}}" {{ ($role->name == old('role1') ? "selected":"") }}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    <strong id="role-error1"></strong>
                                </span>
                            </div>
                            <input id="UserID" name="UserID" type="hidden">
                        </div>
                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('User Branch') }} <font color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control select2"  class="form-control" id="store" name="store_id"  data-placeholder="Select Branch" required data-width="100%" disabled="true">
                                    @foreach(getStores() as $role)
                                        <option value="{{$role->id}}" {{ ($role->id == old('store') ? "selected":"") }}>{{$role->name}}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">
                                    <strong id="role-error"></strong>
                                </span>
                            </div>
                        </div>

                    </form>
                </div>

            </div>


        </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!--/.modal -->
