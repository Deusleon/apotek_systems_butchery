@extends("layouts.master")

@section('content-title')
Invoices

@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="#">Accounting / Invoices/ Payments </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <ul class="nav nav-pills mb-3" id="myTab">
            @if (auth()->user()->checkPermission('View Invoices'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('accounting/invoices') }}">Invoices
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View payments'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" href="{{ url('accounting/invoices/payments') }}">Payments
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('View payment history'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('accounting/invoices/payments-history') }}">Payment History
                    </a>
                </li>
            @endif
            
    </ul>
    <div class="card">
        <div class="card-header">
            <h5>Make Invoice Payment</h5>
        </div>
        <div class="card-body">
            <form id="payment_form" action="{{ route('invoice-payments.store') }}" method="post">
                @csrf()
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier">Supplier Name *</label>
                            <select name="supplier_id" class="form-control" id="supplier" required="true">
                                <option selected="true" value="" disabled="disabled">Select Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                @endforeach
                            </select>
                            <span id="supplier_warning" style="display: none; color: red; font-size: 0.9em">Supplier required</span>
                            <div id="supplier_balance_badge" style="display: none; margin-top: 5px;">
                                <small class="text-info"><strong>Total Supplier Balance: <span id="supplier_total_balance" class="badge badge-info" style="font-size: 0.9em; padding: 5px 10px;"></span></strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="invoice">Invoice # *</label>
                            <select name="invoice_id" class="form-control" id="invoice" required="true" disabled>
                                <option selected="true" value="" disabled="disabled">Select Invoice...</option>
                            </select>
                            <span id="invoice_warning" style="display: none; color: red; font-size: 0.9em">Invoice required</span>
                            <div id="balance_badge" style="display: none; margin-top: 5px;">
                                <small class="text-muted">Remaining Balance: <span id="balance_amount" class="badge badge-light" style="font-size: 0.85em; padding: 4px 8px;"></span></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount_paid">Amount Paid *</label>
                            <input type="text" class="form-control" id="amount_paid" name="amount_paid"
                                   aria-describedby="emailHelp" required="true"
                                   onkeypress="return isNumberKey(event,this)">
                            <span id="amount_warning" style="display: none; color: red; font-size: 0.9em">Amount required</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_method">Pay Method *</label>
                            <select name="payment_method" class="form-control" id="payment_method" required="true">
                                <option value="" disabled selected>Select Method</option>
                                <option value="cash">CASH</option>
                                <option value="mobile">MOBILE</option>
                                <option value="bank">BANK</option>
                                <option value="cheque">CHEQUE</option>
                                <option value="others">OTHERS</option>
                            </select>
                            <span id="method_warning" style="display: none; color: red; font-size: 0.9em">Payment method required</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_date">Payment Date *</label>
                            <input type="text" class="form-control" id="payment_date" name="payment_date"
                                   aria-describedby="emailHelp" readonly required="true">
                            <span id="date_warning" style="display: none; color: red; font-size: 0.9em">Payment date required</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary float-right">Make Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push("page_scripts")
@include('partials.notification')
<script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
<script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
<script src="{{asset("assets/apotek/js/notification.js")}}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize date picker
    $('#payment_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    }).datepicker('setDate', new Date());

    // Format money function
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
            const negativeSign = amount < 0 ? "-" : "";
            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    }

    // Number validation
    function isNumberKey(evt, obj) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        var value = obj.value;
        var dotcontains = value.indexOf(".") !== -1;
        if (dotcontains)
            if (charCode === 46) return false;
        if (charCode === 46) return true;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    // Format amount paid on blur (when user leaves the input)
    $('#amount_paid').on('blur', function () {
        var newValue = $(this).val();
        if (newValue !== '') {
            $(this).val(formatMoney(parseFloat(newValue.replace(/\,/g, ''))));
        }
    });

    // Load invoices when supplier is selected
    $('#supplier').on('change', function() {
        var supplierId = $(this).val();
        $('#supplier_warning').hide();

        if (supplierId) {
            $.ajax({
                url: '{{ route("get-supplier-invoices") }}',
                method: 'GET',
                data: { supplier_id: supplierId },
                success: function(data) {
                    // Display total supplier balance
                    if (data.total_balance && data.total_balance > 0) {
                        $('#supplier_total_balance').text(formatMoney(data.total_balance));
                        $('#supplier_balance_badge').show();
                    } else {
                        $('#supplier_balance_badge').hide();
                    }

                    // Load individual invoices
                    $('#invoice').empty().append('<option value="" disabled selected>Select Invoice...</option>');
                    if (data.invoices && data.invoices.length > 0) {
                        $.each(data.invoices, function(key, invoice) {
                            $('#invoice').append('<option value="' + invoice.id + '" data-balance="' + (invoice.invoice_amount - invoice.paid_amount) + '">' + invoice.invoice_no + ' - ' + formatMoney(invoice.invoice_amount) + '</option>');
                        });
                        $('#invoice').prop('disabled', false);
                    } else {
                        $('#invoice').append('<option value="" disabled>No unpaid invoices found</option>');
                        $('#invoice').prop('disabled', true);
                    }
                },
                error: function() {
                    notify('Error loading supplier data', 'top', 'right', 'danger');
                    $('#supplier_balance_badge').hide();
                }
            });
        } else {
            $('#invoice').empty().append('<option value="" disabled selected>Select Invoice...</option>').prop('disabled', true);
            $('#balance_badge').hide();
            $('#supplier_balance_badge').hide();
        }
    });

    // Form validation and submission
    $('#payment_form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var isValid = true;

        // Check supplier
        if (!$('#supplier').val()) {
            $('#supplier_warning').show();
            isValid = false;
        } else {
            $('#supplier_warning').hide();
        }

        // Check invoice
        if (!$('#invoice').val()) {
            $('#invoice_warning').show();
            isValid = false;
        } else {
            $('#invoice_warning').hide();
        }

        // Check amount
        if (!$('#amount_paid').val()) {
            $('#amount_warning').show();
            isValid = false;
        } else {
            $('#amount_warning').hide();
        }

        // Check payment method
        if (!$('#payment_method').val()) {
            $('#method_warning').show();
            isValid = false;
        } else {
            $('#method_warning').hide();
        }

        // Check payment date
        if (!$('#payment_date').val()) {
            $('#date_warning').show();
            isValid = false;
        } else {
            $('#date_warning').hide();
        }

        if (!isValid) {
            return false;
        }

        // Format amount before submission
        var amount = $('#amount_paid').val().replace(/\,/g, '');
        $('#amount_paid').val(amount);

        // Submit via AJAX
        var formData = $(this).serialize();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    notify(response.message || 'Payment recorded successfully!', 'top', 'right', 'success');

                    // Reset form
                    $('#payment_form')[0].reset();
                    $('#supplier').val('').trigger('change');
                    $('#invoice').empty().append('<option value="" disabled selected>Select Invoice...</option>').prop('disabled', true);
                    $('#payment_date').datepicker('setDate', new Date());

                    // Reload payment history table
                    paymentHistoryTable.ajax.reload();
                } else {
                    notify(response.error || 'Payment failed!', 'top', 'right', 'danger');
                }
            },
            error: function(xhr, status, error) {
                notify('Network error occurred. Please try again.', 'top', 'right', 'danger');
            }
        });
    });

    // Initialize select2
    $('#supplier').select2({
        dropdownParent: $('#payment_form')
    });

    $('#invoice').select2({
        dropdownParent: $('#payment_form')
    });

    $('#payment_method').select2({
        dropdownParent: $('#payment_form')
    });

    // Show balance badge when invoice is selected
    $('#invoice').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var balance = selectedOption.data('balance');

        if (balance !== undefined) {
            $('#balance_amount').text(formatMoney(balance));
            $('#balance_badge').show();
        } else {
            $('#balance_badge').hide();
        }
        $('#invoice_warning').hide();
    });
</script>
@endpush