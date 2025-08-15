<div class="modal fade" id="editOrder" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="panel-body">
                    <form method="POST" action="{{route('updateQuote') }}">
                        @csrf

                        <input id="id" type="hidden" class="form-control" name="id"
                               value="{{ old('id') }}" required>

                        <input id="price" type="hidden" class="form-control" name="price"
                               value="{{ old('price') }}" required>

                        <div class="form-group row">
                            <label for="name1" class="col-md-4 col-form-label text-md-right">{{ __('Name') }} <font
                                    color="red">*</font></label>

                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control" name="name"
                                       value="{{ old('name') }}" required>

                                <span class="text-danger">
                                    <strong id="name-error1"></strong>
                                </span>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="email1" class="col-md-4 col-form-label text-md-right">{{ __('Quantity') }}
                                <font color="red">*</font></label>

                            <div class="col-md-8">
                                <input id="quantity" type="text" class="form-control" name="quantity"
                                       value="{{ old('quantity') }}">

                                <span class="text-danger">
                                     <strong id="email-error1"></strong>
                                </span>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" id="update" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>

            </div>


        </div><!-- /.modal-content -->
    </div><!--/.modal-dialog -->
</div><!--/.modal -->
