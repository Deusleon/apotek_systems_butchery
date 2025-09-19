@extends("layouts.master")

@section('page_css')
    <style>


    </style>
@endsection

@section('content-title')
    Inventory Reports
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Reports / Inventory Reports </a></li>
@endsection

@section("content")

    <style>
        .datepicker>.datepicker-days {
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
                <form id="inventory_report_form" action="{{route('inventory-report-filter')}}" method="get" target="_blank">
                    @csrf()
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="report_option">Select Inventory Report</label>
                                    <div id="border" style="border: 2px solid white; border-radius: 6px;">
                                        <select id="report_option" name="report_option" onchange="reportOption()"
                                            class="js-example-basic-single form-control drop">
                                            <option selected="true" value="0" disabled="disabled">Select report</option>
                                            <option value="1">Current Stock</option>
                                            <option value="2">Product Details Report</option>
                                            <option value="3">Product Ledger Report</option>
                                            <option value="4">Expired Product Report</option>
                                            <option value="5">Out Of Stock Report</option>
                                            <option value="6">Outgoing Tracking Report</option>
                                            <option value="7">Stock Adjustment Report</option>
                                            <option value="8">Stock Issue Report</option>
                                            <option value="9">Stock Transfer Report</option>
                                            <option value="10">Stock Above Maximum Level Report</option>
                                            <option value="11">Stock Below Minimum Level Report</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2">

                        </div>
                    </div>
                    {{--product ledger--}}
                    <div class="row" id="product_ledger" style="display: none">
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="product">Products<font color="red">*</font></label>
                                    <select id="product" name="product" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{$product->product_id}}">
                                                {{$product->product_name.' ' ?? ''}}{{$product->brand.' ' ?? ''}}{{$product->pack_size ?? ''}}{{$product->sales_uom ?? ''}}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span id="warning" style="color: #ff0000; display: none">Please select a product</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--current stock--}}
                    <div id="current-stock" style="display: none">
                        <div class="row">
                            @if (is_all_store())
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="store">Branch<font color="red">*</font></label>
                                        <select id="store-name" name="store_name" onchange=""
                                            class="js-example-basic-single form-control drop">
                                            <option value="0" selected="true" disabled="disabled">Select Branch</option>
                                            @foreach($stores as $store)
                                                <option value="{{$store->id}}">
                                                    {{$store->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span id="warning-store" style="color: #ff0000; display: none">Please select a
                                            Branch</span>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category">Product Category</label>
                                    <select id="category-name" name="category_name" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select category</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">
                                                {{$category->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--product details--}}
                    <div class="row" id="product-detail" style="display: none">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category-detail">Product Category</label>
                                <select id="category-name-detail" name="category_name_detail" onchange=""
                                    class="js-example-basic-single form-control drop">
                                    <option value="0" selected="true" disabled="disabled">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">
                                            {{$category->name}}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="warning-detail" style="color: #ff0000; display: none">Please select a
                                    category</span>
                            </div>
                        </div>
                    </div>
                    {{--stock issue--}}
                    <div id="stock-issue" style="display: none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-issue-date">Date<font color="red">*</font></label>
                                    <div id="issue_date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="issue_date" class="form-control" id="d_auto_912"
                                            autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-issue">Status</label>
                                    <select id="stock-issues" name="stock_issue" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select status</option>
                                        <option value="1">Issued</option>
                                        <option value="2">Returned</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--stock transfer--}}
                    <div id="stock-transfer" style="display: none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-transfer-date">Date<font color="red">*</font></label>
                                    <div id="transfer_date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="transfer_date" class="form-control" id="d_auto_9121"
                                            autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-transfer">Status</label>
                                    <select id="stock-transfers" name="stock_transfer" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select status</option>
                                        <option value="2">Completed</option>
                                        <option value="1">Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--stock adjustment--}}
                    <div id="stock-adjustment" style="display: none">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-adjustment">Adjustment Type<font color="red">*</font></label>
                                    <select id="stock-adjustments" name="stock_adjustment" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select type</option>
                                        <option value="Negative">Negative</option>
                                        <option value="Positive">Positive</option>
                                    </select>
                                    <span id="warning-details" style="color: #ff0000; display: none">Please select a
                                        type</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-adjustment-date">Date<font color="red">*</font></label>
                                    <div id="date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="adjustment_date" class="form-control" id="d_auto_91"
                                            autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-adjustment-reason">Adjustment Type</label>
                                    <select id="stock-adjustments-reason" name="stock_adjustment_reason" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select reason</option>
                                        @foreach($reasons as $reason)
                                            <option value="{{$reason->reason}}">{{$reason->reason}}</option>
                                        @endforeach
                                    </select>
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
            <image id="loading-image" src="{{asset('assets/images/spinner.gif')}}"></image>
        </div>


    </div>
    </div>
    </div>
    </div>

@endsection


@push("page_scripts")
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    @include('partials.notification')


    <script>

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_91').daterangepicker({
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
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
            });
        });

        $('input[name="adjustment_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_912').daterangepicker({
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
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
            });
        });

        $('input[name="issue_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_9121').daterangepicker({
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
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
            });
        });

        $('input[name="transfer_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });

        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            if (Number(report_option_index) !== Number(0)) {
                document.getElementById('border').style.borderColor = 'white';
            }

            //if product ledger
            if (Number(report_option_index) === Number(3)) {
                document.getElementById('product_ledger').style.display = 'block';
            } else {
                document.getElementById('product_ledger').style.display = 'none';
                document.getElementById('warning').style.display = 'none';

            }

            //if current stock
            if (Number(report_option_index) === Number(1)) {
                document.getElementById('current-stock').style.display = 'block';
            } else {
                document.getElementById('current-stock').style.display = 'none';
                document.getElementById('warning-store').style.display = 'none';

            }

            /*product detail*/
            if (Number(report_option_index) === Number(2)) {
                document.getElementById('product-detail').style.display = 'block';
            } else {
                document.getElementById('product-detail').style.display = 'none';
                document.getElementById('warning-detail').style.display = 'none';

            }

            /*stock issue*/
            if (Number(report_option_index) === Number(8)) {
                document.getElementById('stock-issue').style.display = 'block';
            } else {
                document.getElementById('stock-issue').style.display = 'none';
            }

            /*stock transfer*/
            if (Number(report_option_index) === Number(9)) {
                document.getElementById('stock-transfer').style.display = 'block';
            } else {
                document.getElementById('stock-transfer').style.display = 'none';
            }

            /*stock adjustment*/
            if (Number(report_option_index) === Number(7)) {
                document.getElementById('stock-adjustment').style.display = 'block';
            } else {
                document.getElementById('stock-adjustment').style.display = 'none';
            }

        }

        $('#inventory_report_form').on('submit', function () {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            /*product ledger*/
            var product_option = document.getElementById("product");
            var product_option_index = product_option.options[product_option.selectedIndex].value;

            /*current stock*/
            var store_option = document.getElementById("store-name");
            var store_option_index = store_option.options[store_option.selectedIndex].value;

            /*product detail*/
            var category_option = document.getElementById("category-name-detail");
            var category_option_index = category_option.options[category_option.selectedIndex].value;

            /*stock issue*/
            var issue_option = document.getElementById("stock-issues");
            var issue_option_index = issue_option.options[issue_option.selectedIndex].value;

            /*stock transfer*/
            var transfer_option = document.getElementById("stock-transfers");
            var transfer_option_index = transfer_option.options[transfer_option.selectedIndex].value;

            /*stock adjustment*/
            var adj_option = document.getElementById("stock-adjustments");
            var adj_option_index = adj_option.options[adj_option.selectedIndex].value;

            if (Number(report_option_index) === Number(0)) {
                document.getElementById('border').style.borderColor = 'red';
                return false;
            }

            document.getElementById('border').style.borderColor = 'white';

            /*if product ledger*/
            if (Number(report_option_index) === Number(3) && Number(product_option_index) !== Number(0)) {
                document.getElementById('warning').style.display = 'none';
                //make request
                location.reload();

            } else if (Number(report_option_index) === Number(3) && Number(product_option_index) === Number(0)) {
                document.getElementById('warning').style.display = 'block';
                return false;
            }

            /*if current stock*/
            if (Number(report_option_index) === Number(1) && Number(store_option_index) !== Number(0)) {
                document.getElementById('warning-store').style.display = 'none';
                //make request
                location.reload();

            } else if (Number(report_option_index) === Number(1) && Number(store_option_index) === Number(0)) {
                document.getElementById('warning-store').style.display = 'block';
                return false;
            }

            /*if product detail*/
            if (Number(report_option_index) === Number(2) && Number(category_option_index) !== Number(0)) {
                document.getElementById('warning-detail').style.display = 'none';
                //make request
                location.reload();

            } else if (Number(report_option_index) === Number(2) && Number(category_option_index) === Number(0)) {
                // document.getElementById('warning-detail').style.display = 'block';
                // return false;
            }

            /*if stock issue*/
            var issue_date = document.getElementById('d_auto_912').value;
            if (Number(report_option_index) === Number(8) && Number(issue_option_index) === Number(0)) {
                //make request
                if (issue_date === '') {
                    document.getElementById('issue_date').style.borderColor = 'red';
                    return false;
                }
                document.getElementById('issue_date').style.borderColor = 'white';
                location.reload();
            }

            /*if stock transfer*/
            var transfer_date = document.getElementById('d_auto_9121').value;
            if (Number(report_option_index) === Number(9) && Number(transfer_option_index) === Number(0)) {
                //make request
                console.log(transfer_date);
                if (transfer_date === '') {
                    document.getElementById('transfer_date').style.borderColor = 'red';
                    return false;
                }
                document.getElementById('transfer_date').style.borderColor = 'white';
                location.reload();

            }

            /*if stock adjustment*/
            var date = document.getElementById('d_auto_91').value;

            if (Number(report_option_index) === Number(7) && Number(adj_option_index) !== Number(0)) {
                // document.getElementById('date').style.borderColor = 'red';
                document.getElementById('warning-details').style.display = 'none';
                //make request
                if (date === '') {
                    document.getElementById('date').style.borderColor = 'red';
                    return false;
                }
                location.reload();

            } else if (Number(report_option_index) === Number(7) && Number(adj_option_index) === Number(0)) {
                document.getElementById('warning-details').style.display = 'block';
                document.getElementById('date').style.borderColor = 'white';

                return false;
            }


        });

    </script>
@endpush