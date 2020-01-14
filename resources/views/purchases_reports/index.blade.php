@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Purchases Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Pharmacy Reports / Purchases Reports </a></li>
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
                <form id="inventory_report_form" action="{{route('purchase-report-filter')}}"
                      method="get" target="_blank">
                    @csrf()
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="report_option">Select Purchase Report</label>
                                    <select id="report_option" name="report_option" onchange="reportOption()"
                                            class="js-example-basic-single form-control drop">
                                        <option selected="true" value="0" disabled="disabled">Select report</option>
                                        <option value="1">Material Received Report</option>
                                        <option value="2">Invoice Summary Report</option>
                                        {{--                                        <option value="3">Invoice Details Report</option>--}}
                                        <option value="4">List of Supplier</option>
                                        {{--                                        <option value="5">Supplier Price Comparison</option>--}}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2">
                        </div>
                    </div>
                    {{--material received option--}}
                    <div id="material_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="supplier">Supplier Name</label><font color="red">*</font>
                                    <select name="supplier" class="js-example-basic-single form-control drop"
                                            onchange="filterInvoiceBySupplier()" id="supplier_ids">
                                        <option value="" selected disabled>Select Supplier...</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="text" name="expire_dates" class="form-control" id="receive_date2">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="code">Invoice #</label>
                                    <select name="invoice_no" class="form-control js-example-basic-single"
                                            id="invoice_id">
                                        <option selected="true" value="" disabled="disabled">Select Invoice..</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--Invoice--}}
                    <div id="invoice_options" style="display: none">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="supplier">Supplier Name</label><font color="red">*</font>
                                    <select name="suppliers" class="js-example-basic-single form-control drop"
                                            id="suppliers">
                                        <option value="" selected disabled>Select Supplier...</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">

                                    <label>Date</label>
                                    <input type="text" name="expire_date" class="form-control" id="receive_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="status">Received Status</label>
                                        <select id="status" name="received_status"
                                                class="js-example-basic-single form-control drop">
                                            <option value="" selected disabled>Select Status</option>
                                            <option>All Received</option>
                                            <option>Partial Received</option>
                                        </select>
                                        <span id="warning"
                                              style="color: #ff0000; display: none">Please select a category</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="period">Grace Period</label>
                                        <select id="period" name="period" onchange=""
                                                class="js-example-basic-single form-control drop">
                                            <option value="" selected disabled>Select Period...</option>
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="7">7</option>
                                            <option value="14">14</option>
                                            <option value="21">21</option>
                                            <option value="28">28</option>
                                            <option value="30">30</option>
                                            <option value="60">60</option>
                                            <option value="60">90</option>
                                        </select>
                                        <span id="warning"
                                              style="color: #ff0000; display: none">Please select a category</span>
                                    </div>
                                </div>
                            </div>
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
            <image id="loading-image" src=""></image>
        </div>
    </div>

@endsection
@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>
    <script src="{{asset("assets/apotek/js/goods-receiving.js")}}"></script>

    @include('partials.notification')

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            routes: {
                goodsreceiving: '{{route('receiving-price-category')}}',
                filterBySupplier: '{{route('filter-invoice')}}',
                itemFormSave: '{{route('goods-receiving.itemReceive')}}'
            }
        };


        $(function () {
            var start = moment().startOf('month');
            var end = moment();

            function cb(start, end) {
                $('#receive_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#receive_date').daterangepicker({
                startDate: start,
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
        $(function () {
            var start = moment().startOf('month');
            var end = moment();

            function cb(start, end) {
                $('#receive_date span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#receive_date2').daterangepicker({
                startDate: start,
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


        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            //if invoice
            if (Number(report_option_index) === Number(2)) {
                document.getElementById('invoice_options').style.display = 'block';
                $("#suppliers").prop("required", true);
                $("#supplier_ids").val("");
                $("#supplier_ids").change();
                $("#supplier_ids").prop("required", false);
            } else {
                document.getElementById('invoice_options').style.display = 'none';

            }

            //if Material Received
            if (Number(report_option_index) === Number(1)) {
                document.getElementById('material_options').style.display = 'block';
                $("#suppliers").prop("required", false);
                $("#suppliers").val("");
                $("#suppliers").change();
                $("#supplier_ids").prop("required", true);
            } else {
                document.getElementById('material_options').style.display = 'none';

            }

            if (Number(report_option_index) === Number(3)) {
                $("#suppliers").prop("required", false);
                $("#supplier_ids").prop("required", false);
            }

            if (Number(report_option_index) === Number(4)) {
                $("#suppliers").prop("required", false);
                $("#supplier_ids").prop("required", false);
            }

            if (Number(report_option_index) === Number(5)) {
                $("#suppliers").prop("required", false);
                $("#supplier_ids").prop("required", false);
            }

        }

        $('#inventory_report_form').on('submit', function () {
            // var report_option = document.getElementById("report_option");
            // var report_option_index = report_option.options[report_option.selectedIndex].value;
            //
            // var product_option = document.getElementById("product");
            // var product_option_index = product_option.options[product_option.selectedIndex].value;
            //
            //
            // if (Number(report_option_index) === Number(8) && Number(product_option_index) !== Number(0)) {
            //     document.getElementById('warning').style.display = 'none';
            //     //make request
            //
            // } else if (Number(report_option_index) === Number(8) && Number(product_option_index) === Number(0)) {
            //     document.getElementById('warning').style.display = 'block';
            //     return false;
            // }

        });

    </script>

@endpush
