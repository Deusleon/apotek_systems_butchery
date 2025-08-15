@extends("layouts.master")
@section('content-title')
    Credits Tracking
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Credits Tracking</a></li>
@endsection


@section("content")

    <style>
        .select2-container {
            width: 110% !important;
        }
    </style>

    <div class="col-sm-12">
        <div class="card-block">
            <div class="card-body">
                <div class="tab-content" id="myTabContent">

                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 62%" for=""
                                   class="col-form-label text-md-right">Customer:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3.2%;">
                            <select name="customer_id" id="customer_payment"
                                    class="js-example-basic-single form-control">
                                <option value="" selected="true" disabled>Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 74%" for=""
                                   class="col-form-label text-md-right">Status:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3.2%;">
                            <select name="status" id="payment-status" class="js-example-basic-single form-control">
                                <option value="" selected="true" disabled>Select Status</option>
                                <option value="all">All</option>
                                <option value="not_paid">Not Paid</option>
                                <option value="partial_paid">Partial Paid</option>
                                <option value="full_paid">Full Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">

                        </div>
                        <div class="col-md-3" style="margin-left: 2.5%">
                            <label style="margin-left: 80%" for=""
                                   class="col-form-label text-md-right">Date:</label>
                        </div>
                        <div class="col-md-3" style="margin-left: -3%;">
                            <input style="width: 110%;" type="text" name="date_of_sale" class="form-control"
                                   id="sales_date" value=""/>
                        </div>
                    </div>
                    <input type="hidden" id="track" value="1">
                    <input type="hidden" id="vat" value="">
                    <input type="hidden" value="" id="category">
                    <input type="hidden" value="" id="customers">
                    <input type="hidden" value="" id="print">
                    <input type="hidden" value="" id="fixed_price">

                    <div class="row" id="detail">
                        <hr>
                        @if(auth()->user()->checkPermission('Credit Payment'))
                            <div id="can_pay"></div>
                        @endif
                        <div class="table teble responsive" style="width: 100%;">
                            <table id="credit_payment_table" class="display table nowrap table-striped table-hover"
                                   style="width:100%">

                                <thead>
                                <tr>
                                    <th>Receipt#</th>
                                    <th>Customer</th>
                                    <th>Sale Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>
                @include('sales.credit_sales.create_payment')

            </div>
        </div>


        @endsection


        @push("page_scripts")
            @include('partials.notification')
            <script type="text/javascript">
                $(function () {

                    var start = moment();
                    var end = moment();

                    function cb(start, end) {
                        $('#sales_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    }

                    $('#sales_date').daterangepicker({
                        startDate: moment().startOf('month'),
                        endDate: end,
                        autoUpdateInput: true,
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            'This Year': [moment().startOf('year'), moment()]
                        }
                    }, cb);

                    cb(start, end);


                });

            </script>
            <script type="text/javascript">
                var config = {
                    token: '{{ csrf_token() }}',
                    routes: {
                        getCreditSale: '{{route('getCreditSale')}}'

                    }
                };
            </script>
            <script src="{{asset("assets/apotek/js/sales.js")}}"></script>
    @endpush


