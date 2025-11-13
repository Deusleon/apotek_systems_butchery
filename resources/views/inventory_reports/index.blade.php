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
                                            @if(auth()->user()->checkPermission('Current Stock Summary Report'))
                                                <option value="1">Current Stock Summary Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Current Stock Detailed Report'))
                                                <option value="12">Current Stock Detailed Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Product Details Report'))
                                                <option value="2">Product Details Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Product Ledger Summary Report'))
                                                <option value="3">Product Ledger Summary Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Product Ledger Detailed Report'))
                                                <option value="17">Product Ledger Detailed Report</option>
                                            @endif
                                            @if ($expireEnabled)
                                                @if(auth()->user()->checkPermission('Expired Products Report'))
                                                    <option value="4">Expired Products Report</option>
                                                @endif
                                            @endif
                                            @if ($expireEnabled)
                                                @if(auth()->user()->checkPermission('Products Expiry Date Report'))
                                                    <option value="13">Products Expiry Date Report</option>
                                                @endif
                                            @endif
                                            @if(auth()->user()->checkPermission('Out Of Stock Report'))
                                                <option value="5">Out Of Stock Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Outgoing Stock Summary Report'))
                                                <option value="14">Outgoing Stock Summary Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Outgoing Stock Detailed Report'))
                                                <option value="6">Outgoing Stock Detailed Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Fast Moving Products Report'))
                                                <option value="15">Fast Moving Products Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Dead Stock Report'))
                                                <option value="16">Dead Stock Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Stock Adjustment Report'))
                                                <option value="7">Stock Adjustment Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Stock Issue Report'))
                                                <option value="8">Stock Issue Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Stock Transfer Report'))
                                                <option value="9">Stock Transfer Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Stock Above Max. Level'))
                                                <option value="10">Stock Above Maximum Level Report</option>
                                            @endif
                                            @if(auth()->user()->checkPermission('Stock Below Min. Level'))
                                                <option value="11">Stock Below Minimum Level Report</option>
                                            @endif
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
                                        class="js-example-basic-single form-control drop" required>
                                        <option value="0" selected="true" disabled="disabled">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{$product->product_id}}">
                                                {{$product->product_name . ' ' ?? ''}}{{$product->brand . ' ' ?? ''}}{{$product->pack_size ?? ''}}{{$product->sales_uom ?? ''}}
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
                                        <select id="store_name" name="store_name" onchange=""
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
                                    <label for="stock-adjustment-date">Date<font color="red">*</font></label>
                                    <div id="date" style="border: 2px solid white; border-radius: 6px;">
                                        <input type="text" name="adjustment_date" class="form-control" id="d_auto_91"
                                            autocomplete="off" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="stock-adjustment">Adjustment Type</label>
                                    <select id="stock-adjustments" name="stock_adjustment" onchange=""
                                        class="js-example-basic-single form-control drop">
                                        <option value="0" selected="true" disabled="disabled">Select type</option>
                                        <option value="">All</option>
                                        <option value="decrease">Negative</option>
                                        <option value="increase">Positive</option>
                                    </select>
                                    <span id="warning-details" style="color: #ff0000; display: none">Please select a
                                        type</span>
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
                autoUpdateInput: true,
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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
            });
        });

        $('input[name="adjustment_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_912').daterangepicker({
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: true,
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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
            });
        });

        $('input[name="issue_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#d_auto_9121').daterangepicker({
                showDropdowns: true,
                maxDate: end,
                autoUpdateInput: true,
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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
            });
        });

        $('input[name="transfer_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
        });

        function reportOption() {
            var report_option = document.getElementById("report_option");
            var report_option_index = report_option.options[report_option.selectedIndex].value;

            if (Number(report_option_index) !== 0) {
                document.getElementById('border').style.borderColor = 'white';
            }

            // product ledger
            let ledgerDiv = document.getElementById('product_ledger');
            let warning = document.getElementById('warning');
            if (Number(report_option_index) === 3 || Number(report_option_index) === 17) {
                if (ledgerDiv) ledgerDiv.style.display = 'block';
            } else {
                if (ledgerDiv) ledgerDiv.style.display = 'none';
                if (warning) warning.style.display = 'none';
            }

            // current stock
            let stockDiv = document.getElementById('current-stock');
            let warningStore = document.getElementById('warning-store');
            if (Number(report_option_index) === 1 || Number(report_option_index) === 12) {
                if (stockDiv) stockDiv.style.display = 'block';
            } else {
                if (stockDiv) stockDiv.style.display = 'none';
                if (warningStore) warningStore.style.display = 'none';
            }

            // product detail
            let detailDiv = document.getElementById('product-detail');
            let warningDetail = document.getElementById('warning-detail');
            if (Number(report_option_index) === 2) {
                if (detailDiv) detailDiv.style.display = 'block';
            } else {
                if (detailDiv) detailDiv.style.display = 'none';
                if (warningDetail) warningDetail.style.display = 'none';
            }

            // stock issue
            let issueDiv = document.getElementById('stock-issue');
            if (Number(report_option_index) === 8) {
                if (issueDiv) issueDiv.style.display = 'block';
            } else {
                if (issueDiv) issueDiv.style.display = 'none';
            }

            // stock transfer
            let transferDiv = document.getElementById('stock-transfer');
            if (Number(report_option_index) === 9) {
                if (transferDiv) transferDiv.style.display = 'block';
            } else {
                if (transferDiv) transferDiv.style.display = 'none';
            }

            // stock adjustment
            let adjustDiv = document.getElementById('stock-adjustment');
            if (Number(report_option_index) === 7) {
                if (adjustDiv) adjustDiv.style.display = 'block';
            } else {
                if (adjustDiv) adjustDiv.style.display = 'none';
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
            var store_option_index = store_option ? Number(store_option.value || 0) : 0;

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
                return true;
            } else if (Number(report_option_index) === Number(3) && Number(product_option_index) === Number(0)) {
                document.getElementById('warning').style.display = 'block';
                return false;
            }

            /*if current stock*/
            if (Number(report_option_index) === Number(1) && Number(store_option_index) !== Number(0)) {
                document.getElementById('warning-store').style.display = 'none';
                //make request
                return true;

            } else if (Number(report_option_index) === Number(1) && Number(store_option_index) === Number(0)) {
                document.getElementById('warning-store').style.display = 'block';
                return false;
            }
            
            /*if current stock*/
            if (Number(report_option_index) === Number(12) && Number(store_option_index) !== Number(0)) {
                document.getElementById('warning-store').style.display = 'none';
                //make request
                return true;

            } else if (Number(report_option_index) === Number(12) && Number(store_option_index) === Number(0)) {
                document.getElementById('warning-store').style.display = 'block';
                return false;
            }

            /*if product detail*/
            if (Number(report_option_index) === Number(2) && Number(category_option_index) !== Number(0)) {
                document.getElementById('warning-detail').style.display = 'none';
                //make request
                return true;

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
                return true;
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
                return true;

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
                return true;

            }


        });

    </script>
@endpush