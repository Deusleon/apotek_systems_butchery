<div class="modal fade" id="edit" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as  $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>

                        </div>
                    @endif
                    <form id="expense_form" action="{{route('expense.update','id')}}" method="post">
                        @csrf()
                        @method('put')
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="expense_date" class="col-md-4 col-form-label text-md-right">{{ __('Expense
                                            Date') }}<span style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="expense_date_edit" class="form-control"
                                               id="d_auto_91_edit" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="payment_method"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Payment Method') }}<span
                                        style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="method" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="payment_method_edit" name="payment_method_edit" class="form-control"
                                                required="true">
                                            <option selected="true" value="0" disabled="disabled">Select method</option>
                                            <option value="1">CASH</option>
                                            <option value="2">MOBILE</option>
                                            <option value="3">BANK</option>
                                            <option value="4">CHEQUE</option>
                                            <option value="5">OTHERS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expense_amount" class="col-md-4 col-form-label text-md-right">{{ __('Expense
                                    Amount') }}<span style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="expense_amount_edit" class="form-control"
                                           onkeypress="return isNumberKey(event,this)" name="expense_amount_edit"
                                           required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expense_category"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Expense Category') }}<span
                                        style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="category" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="expense_category_edit" name="expense_category_edit"
                                                class="form-control">
                                            <option selected="true" value="0" disabled="disabled">Select category
                                            </option>
                                            @foreach($expense_categories as $expense_category)
                                                <option
                                                    value="{{$expense_category->id}}">{{$expense_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expense_description"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Expense Description') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="expense_description_edit"
                                           name="expense_description_edit">
                                </div>
                            </div>

                            <input type="hidden" id="expense_id" name="expense_id">


                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
