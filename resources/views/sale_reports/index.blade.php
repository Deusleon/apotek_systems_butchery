@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Sales Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Sales Reports </a></li>
@endsection

@section("content")

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #select1 {
            z-index: 10050;
        }

        #loading {
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            display: none;
            opacity: 0.7;
            background-color: #fff;
            z-index: 99;
            text-align: center;
        }

        #loading-image {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 100;
        }

        input[type=button]:focus {
            background-color: #748892;
            border-color: #748892;
            color: white;
        }

    </style>

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <form id="inventory_report_form" action="{{route('sale-report-filter')}}"
                      method="get" target="_blank">
                    @csrf()
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="report_option">Select Sale Report<font color="red">*</font></label>
                                    <select id="report_option" name="report_option" onchange="reportOption()"
                                            class="js-example-basic-single form-control drop" required>
                                        <option selected="true" value="" disabled="disabled">Select report</option>
                                        <option value="1">Cash Sales Details Report</option>
                                        <option value="2">Cash Sales Summary Report</option>
                                        <option value="3">Credit Sales Details Report</option>
                                        <option value="4">Credit Sales Summary Report</option>
                                        <option value="5">Credit Payments Report</option>
                                        <option value="6">Customer Payments Statement</option>
                                        <!-- <option value="6">Bill Sales Details Report</option>
                                        <option value="7">Company Billing Report</option> -->
                                        <option value="8">Price List Report</option>
                                    {{--                                        <option value="9">Category Price List Report</option>--}}
                                    <!--   <option value="10">Sales Trend Chart</option> -->
                                        <option value="11">Sales Return Report</option>
                                        <option value="12">Sales Comparison Report</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div id="range">
                                <label for="filter">Date<font color="red">*</font></label>
                                <input type="text" class="form-control" name="date_range" id="daterange" readonly/>
                            </div>
                            <div id="price_category" style="display: none">
                                <label for="product">Price Category<font color="red">*</font></label>
                                <select id="product" name="category" onchange=""
                                        class="js-example-basic-single form-control drop">
                                    <option value="" selected="true" disabled="disabled">Select category</option>
                                    @foreach($price_category as $category)
                                        <option value="{{$category->id}}">
                                            {{$category->name}}</option>
                                    @endforeach
                                </select>
                                <span id="warning"
                                      style="color: #ff0000; display: none">Please select a category</span>
                            </div>
                        </div>

                        <div class="col-md-4" id="customer_statement" style="display: none">
                            <label for="code">Customer<font color="red">*</font></label>
                            <select name="customer_id" id="customer_id" class="js-example-basic-single form-control">
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{$customer->customer_id}}">{{$customer->name}}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-5">

                        </div>
                        <div class="col-md-2">
                            {{--<a href="" target="_blank">--}}
                            <button class="btn btn-secondary" style="width: 100%">
                                Show
                            </button>
                            {{--</a>--}}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ajax loading image -->
        <div id="loading">
            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
        </div>


    </div>

@endsection


@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    @include('partials.notification')

    <script>
        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            if ((Number(report_option_index) === Number(1)) || (Number(report_option_index) === Number(2))
                || (Number(report_option_index) === Number(3)) || (Number(report_option_index) === Number(4))
                || (Number(report_option_index) === Number(5)) || (Number(report_option_index) === Number(11))
                || (Number(report_option_index) === Number(12))) {
                $('#customer_id').prop('required', false);
                $("#product").prop("required", false);
            }

            //if product ledger
            if (Number(report_option_index) === Number(8)) {
                document.getElementById('price_category').style.display = 'block';
                $("#product").prop("required", true);
                $('#customer_id').prop('required', false);
                $("#customer_id").val("");
                $("#customer_id").change();
                document.getElementById('range').style.display = 'none';
            } else {
                document.getElementById('range').style.display = 'block';
                document.getElementById('price_category').style.display = 'none';
                document.getElementById('warning').style.display = 'none';

            }


            //if credit Report
            if (Number(report_option_index) === Number(6)) {
                document.getElementById('customer_statement').style.display = 'block';
                $("#product").prop("required", false);
                $("#product").val("");
                $("#product").change();
                $('#customer_id').prop('required', true);
            } else {
                document.getElementById('customer_statement').style.display = 'none';

            }

        }


        $('#inventory_report_form').on('submit', function () {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            var product_option = document.getElementById("product");
            var product_option_index = product_option.options[product_option.selectedIndex].value;


            if (Number(report_option_index) === Number(8) && Number(product_option_index) !== Number(0)) {
                document.getElementById('warning').style.display = 'none';
                //make request

            } else if (Number(report_option_index) === Number(8) && Number(product_option_index) === Number(0)) {
                document.getElementById('warning').style.display = 'block';
                return false;
            }

        });

    </script>
    <script type="text/javascript">
        $(function () {

            var start = moment().startOf('month');
            var end = moment();

            function cb(start, end) {
                // Display format
                $('#reportrange span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: true,
                locale: {
                    format: 'YYYY/MM/DD' 
                },
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

@endpush
