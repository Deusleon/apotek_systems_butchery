<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form action="{{route('invoice-management.update', isset($invoice) ? $invoice->id : 1 )}}" method="post">
                        @csrf()
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">Invoice Number</label><font color="red">*</font>
                                        <input type="text" class="form-control" id="number_edit" name="invoice_number"
                                               aria-describedby="emailHelp"
                                               required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="code">Invoice Date</label><font color="red">*</font>
                                        <input type="text" class="form-control" id="date_edit" name="invoice_date"
                                               aria-describedby="emailHelp"
                                               autocomplete="off" required="true" onchange="editdueDate()">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="supplier">Supplier Name</label><font color="red">*</font>
                                        <select name="supplier" class="form-control" id="supplier_edit" required="true"
                                                required="true">
                                            <option value=""></option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Amount">Invoice Amount</label><font color="red">*</font>
                                        <input type="text" class="form-control" id="amount_edit" name="invoice_amount"
                                               aria-describedby="emailHelp" onchange="editSubtract()"
                                               required="true" onkeypress="return isNumberKey(event,this)">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Amount">Amount Paid</label><font color="red">*</font>
                                        <input type="text" class="form-control" id="amount_paid_edit"
                                               name="paid_amount" aria-describedby="emailHelp"
                                               onchange=" editSubtract()"
                                               required="true" onkeypress="return isNumberKey(event,this)">
                                        <span class="help-inline">
                                        <div class="text text-danger" id="amount_error"></div>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Amount">Remain Balance</label>
                                        <input type="text" class="form-control" id="balance_edit" name="balance"
                                               aria-describedby="emailHelp" readonly value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="Amount">Grace Period (In Days)</label><font color="red">*</font>
                                        <select type="number" class="form-control" id="period_edit" name="grace_period"
                                                aria-describedby="emailHelp"
                                                required="true" onchange="editdueDate()">
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="7">7</option>
                                            <option value="14">14</option>
                                            <option value="21">21</option>
                                            <option value="30">30</option>
                                            <option value="60">60</option>
                                            <option value="60">90</option>
                                <option value="28">28</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Payment Due Date</label>
                                        <input type="text" name="payment_due_date" class="form-control"
                                               id="due_date_edit" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="received_status">Received Status</label><font color="red">*</font>
                                        <select name="received_status" class="form-control" required="true"
                                                id="received_status_edit">
                                            <option value=""></option>
                                            <option>All Received</option>
                                            <option>Partial Received</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="code">Remarks</label>
                                        <textarea name="remarks" id="remarks_edit" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="id_edit" name="id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
//Validating amount paid and invoice amount

    function editSubtract() {

        var invoice_amounts = document.getElementById("amount_edit").value;
        var paid_amounts = document.getElementById("amount_paid_edit").value;

        var paid_amount = parseFloat(paid_amounts.replace(/\,/g, ''), 10);
        var invoice_amount = parseFloat(invoice_amounts.replace(/\,/g, ''), 10);

        document.getElementById("amount_edit").value = formatMoney(invoice_amount);
        document.getElementById("amount_paid_edit").value = formatMoney(paid_amount);

        if ((Number(invoice_amount) >= Number(paid_amount)) && (Number(invoice_amount) && Number(paid_amount) !== Number(0))) {

            //not zero then sub
            var remain_amount = invoice_amount - paid_amount;
            document.getElementById("balance_edit").value = formatMoney(remain_amount);
            document.getElementById('amount_error').style.display = 'none';

        } else if ((Number(invoice_amount) < Number(paid_amount)) && (Number(invoice_amount) && Number(paid_amount) !== Number(0))) {

            //do no substract
            document.getElementById("amount_paid_edit").value = 0;
            document.getElementById('amount_error').style.display = 'block';
            $('#edit').find('.modal-body #amount_error').text('Cannot exceed invoice amount');
        }


    }

    function editdueDate() {

        var grace_period = Number(document.getElementById("period_edit").value);
        var date_string = document.getElementById("date_edit").value;
        invoice_date = new Date(date_string);

        var payment_due_date = invoice_date.setDate(invoice_date.getDate() + grace_period);

        // alert(invoice_date.toString());
        var month = Number(invoice_date.getMonth()) + 1;
        if (typeof month == 'number') {
            document.getElementById("due_date_edit").value = invoice_date.getFullYear() + '-' + month + '-' + invoice_date.getDate();
        }

    }

</script>
