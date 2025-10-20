@extends("layouts.master")

@section('content-title')
Invoices

@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="#">Accounting / Invoices/ History </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <ul class="nav nav-pills mb-3" id="myTab">
            @if (auth()->user()->checkPermission('Show Invoices'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('accounting/invoices') }}">Invoices
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('Show payments'))
                <li class="nav-item">
                    <a class="nav-link text-uppercase" href="{{ url('accounting/invoices/payments') }}">Payments
                    </a>
                </li>
            @endif
            @if (auth()->user()->checkPermission('Show payments history'))
                <li class="nav-item">
                    <a class="nav-link active text-uppercase" href="{{ url('accounting/invoices/payments-history') }}">Payment History
                    </a>
                </li>
            @endif
            
    </ul>
    
    <!-- Payment History Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5>Payment History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="payment_history_table" class="display table nowrap table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Invoice #</th>
                            <th>Supplier</th>
                            <th>Amount Paid</th>
                            <th>Payment Method</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
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

    

    // Initialize payment history table
    var paymentHistoryTable = $('#payment_history_table').DataTable({
        searching: true,
        bPaginate: true,
        bInfo: true,
        ordering: true,
        order: [[0, 'desc']], // Sort by payment date descending (latest first)
        ajax: {
            url: '{{ route("invoice-payments.history") }}',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'payment_date',
                render: function(date) {
                    return moment(date).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            { data: 'invoice.invoice_no' },
            { data: 'supplier.name' },
            {
                data: 'amount_paid',
                render: function(amount) {
                    return formatMoney(amount);
                }
            },
            {
                data: 'payment_method',
                render: function(method) {
                    return method.charAt(0).toUpperCase() + method.slice(1).replace('_', ' ');
                }
            },
            { data: 'remarks' }
        ]
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
</script>
@endpush