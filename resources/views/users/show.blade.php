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
                            <label for="name1" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-8">
                                <input id="name1" type="text" class="form-control" name="name1"
                                       value="{{ old('name1') }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email1" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                            <div class="col-md-8">
                                <input id="email1" type="email" class="form-control" name="email1"
                                       value="{{ old('email1') }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mobile1" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>
                            <div class="col-md-8">
                                <input id="mobile1" type="text" class="form-control"
                                       name="mobile1" value="{{ old('mobile1') }}" disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="position1" class="col-md-4 col-form-label text-md-right">{{ __('Position') }}</label>
                            <div class="col-md-8">
                                <input id="position1" type="text" class="form-control" name="position1"
                                       value="{{ old('position1') }}" disabled>
                            </div>
                        </div>

                        {{-- ✅ User Role (read-only) --}}
                        <div class="form-group row">
                            <label for="role1" class="col-md-4 col-form-label text-md-right">{{ __('User Role') }}</label>
                            <div class="col-md-8">
                                <select class="form-control select2" id="role1" name="role1"
                                        data-placeholder="Select Role" data-width="100%" disabled>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role1') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input id="UserID" name="UserID" type="hidden">
                        </div>

                        {{-- ✅ User Branch (read-only) --}}
                        <div class="form-group row">
                            <label for="store" class="col-md-4 col-form-label text-md-right">{{ __('User Branch') }}</label>
                            <div class="col-md-8">
                                <select class="form-control select2" id="store" name="store_id"
                                        data-placeholder="Select Branch" data-width="100%" disabled>
                                    <option value="">Select Store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}"
                                            {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!--/.modal -->
