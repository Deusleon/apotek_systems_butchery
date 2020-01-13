<div class="modal fade" id="create" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Expense</h5>
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
                    <form id="expense_form" action="{{route('expense.store')}}" method="post">
                        @csrf()
                        <div class="modal-body">
                            <div class="form-group row">
                                <label for="expense_date" class="col-md-4 col-form-label text-md-right">{{ __('Expense
                                            Date') }}<span style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="expense_date" class="form-control"
                                               id="d_auto_91" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="payment_method"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Payment Method') }}<span
                                        style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="method" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="payment_method" name="payment_method" class="form-control"
                                                required="true">
                                            <option selected="true" value="0" disabled="disabled">Select method</option>
                                            <option value="1">CASH</option>
                                            <option value="2">BILL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expense_amount" class="col-md-4 col-form-label text-md-right">{{ __('Expense
                                    Amount') }}<span style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="expense" class="form-control" onkeypress="return isNumberKey(event,this)" name="expense_amount" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expense_category"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Expense Category') }}<span
                                        style="color: red;">*</span></label>
                                <div class="col-md-8">
                                    <div id="category" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="expense_category" name="expense_category" class="form-control">
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
                                    <input type="text" class="form-control" name="expense_description" required>
                                </div>
                            </div>

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
