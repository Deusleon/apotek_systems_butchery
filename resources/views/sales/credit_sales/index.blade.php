@extends("layouts.master")
@section('content-title')
    Credit Sales
@endsection

@php
    // Get the active tab from the session or default to "new"
    $activeTab = session('alert-success', '');
    $activeTabView = session('activeTabView', '');
@endphp

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Credit Sales</a></li>
@endsection

@section("content")

    <style>
        .iti__flag {
            background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags.png")}}");
        }

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags@2x.png")}}");
            }
        }

        .iti {
            width: 100%;
        }

        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
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
    </style>

    <div class="col-sm-12">
        <div class="card-block">

            <div class="col-sm-12">
                <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
                    @if(auth()->user()->checkPermission('View Credit Sales'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-sale-receiving-tablist" data-toggle="pill"
                                href="#credit-sale-receiving" role="tab" aria-controls="credit_sales" aria-selected="true">New
                                sale</a>
                        </li>
                    @endif

                    @if(auth()->user()->checkPermission('View Credit Tracking'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-tracking-tablist" data-toggle="pill"
                                href="#credit-tracking" role="tab" aria-controls="credit_tracking"
                                aria-selected="false">Tracking
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->checkPermission('View Credit Payment'))
                        {{-- @if(!auth()->user()->checkPermission('View Credit Sales') && !auth()->user()->checkPermission('View
                        Credit Tracking')) --}}
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-payment-tablist" data-toggle="pill"
                                href="#credit-payment" role="tab" aria-controls="credit_payment" aria-selected="false">Payments
                            </a>
                        </li>
                        {{-- @endif --}}

                        {{-- @if(auth()->user()->checkPermission('View Credit Tracking'))
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="credit-payment-tablist" data-toggle="pill"
                                href="#credit-payment" role="tab" aria-controls="credit_payment" aria-selected="false">Payments
                            </a>
                        </li>
                        @endif --}}
                    @endif
                </ul>
                <div class="tab-content" id="myTabContent">
                    {{-- Credit Sales--}}
                    @if(auth()->user()->checkPermission('View Credit Sales'))
                        <div class="tab-pane fade" id="credit-sale-receiving" role="tabpanel"
                            aria-labelledby="credit_sales-tab">
                            <form id="credit_sales_form">
                                @csrf()
                                @if(auth()->user()->checkPermission('Add Customers'))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button style="float: right;margin-bottom: 2%;" type="button"
                                                class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create"> Add
                                                New Customer
                                            </button>
                                        </div>

                                    </div>
                                @endif

                                <div id="sale-panel">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label id="cat_label">Sales Type<font color="red">*</font></label>
                                                <select id="price_category" class="js-example-basic-single form-control"
                                                    required>
                                                    <option value="">Select Type</option>
                                                    @foreach($price_category as $price)
                                                        <!-- <option value="{{$price->id}}">{{$price->name}}</option> -->
                                                        <option value="{{$price->id}}" {{$default_sale_type === $price->id ? 'selected' : ''}}>{{$price->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <input type="text" id="credit_barcode_input" style="position:absolute; left:-9999px;"
                                            autofocus>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Products<font color="red">*</font></label>
                                                <select id="products" class="form-control">
                                                    <option value="" disabled selected style="display:none;">Select Product
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="code">Customer Name<font color="red">*</font></label>
                                                <select name="customer_id" id="customer_id"
                                                    class="js-example-basic-single form-control" title="Customer" required>
                                                    <option value="">Select Customer</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{$customer}}">{{$customer->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="detail">
                                        <hr>
                                        <div class="table table responsive" style="width: 100%;">
                                            <table id="cart_table" class="table nowrap table-striped table-hover" width="100%">
                                            </table>
                                        </div>

                                    </div>
                                    <hr>
                                    <input type="hidden" name="" id="is_backdate_enabled" value="{{$back_date}}">
                                    @if($back_date == "NO")
                                        <div class="row">
                                            @if($enable_discount === "YES")
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Discount</label>
                                                        <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                    <span class="help-inline">
                                                        <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                            Discount!</div>
                                                    </span>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Grace Period(In Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21">21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-6">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div style="width: 99%">
                                                        <label>Grace Period(In Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period
                                                            </option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21">21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            @endif
                                            <input type="hidden" id="price_cat" name="price_category_id">
                                            <input type="hidden" id="discount_value" name="discount_amount">
                                            <input type="hidden" id="paid_value" name="paid_amount">
                                            <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                            <input type="hidden" id="order_cart" name="cart">
                                            <input type="hidden" value="{{$vat}}" id="vat">
                                            <input type="hidden" value="{{$fixed_price}}" id="fixed_price">
                                            <input type="hidden" value="{{$enable_discount}}" id="enable_discount">
                                        </div>
                                    @else
                                        <div class="row">
                                            @if($enable_discount === "YES")
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Sales Date<font color="red">*</font></label>
                                                        <input type="text" name="sale_date" class="form-control" id="credit_sale_date"
                                                            autocomplete="off" required="true">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Discount</label>
                                                        <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                    <span class="help-inline">
                                                        <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                            Discount!</div>
                                                    </span>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 99%">
                                                        <label>Grace Period(In Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21">21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Sales Date<font color="red">*</font></label>
                                                        <input type="text" name="sale_date" class="form-control" id="credit_sale_date"
                                                            autocomplete="off" required="true">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Paid</label>
                                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                                            value="0.00" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div style="width: 99%">
                                                        <label>Grace Period(In Days)<font color="red">*</font></label>
                                                        <select class="js-example-basic-single form-control" name="grace_period"
                                                            id="grace_period" required>
                                                            <option value="">Select period</option>
                                                            <option value="1">1</option>
                                                            <option value="7">7</option>
                                                            <option value="14">14</option>
                                                            <option value="21">21</option>
                                                            <option value="30">30</option>
                                                            <option value="60">60</option>
                                                            <option value="90">90</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            @endif
                                            <input type="hidden" id="price_cat" name="price_category_id">
                                            <input type="hidden" id="discount_value" name="discount_amount">
                                            <input type="hidden" id="paid_value" name="paid_amount">
                                            <input type="hidden" id="credit_sale" name="credit" value="Yes">
                                            <input type="hidden" id="order_cart" name="cart">
                                            <input type="hidden" value="{{$vat}}" id="vat">
                                            <input type="hidden" value="{{$fixed_price}}" id="fixed_price">
                                            <input type="hidden" value="{{$enable_discount}}" id="enable_discount">
                                        </div>
                                    @endif
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group"><textarea id="remark" name="remark" class="form-control"
                                                    rows="3" placeholder="Enter Remarks Here"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Sub Total:</b>
                                                </div>
                                                <div class="sub-total col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>VAT:</b>
                                                </div>
                                                <div class="tax-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Total:</b>
                                                </div>
                                                <div class="total-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Balance:</b>
                                                </div>
                                                <div class="balance-amount col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Max. Credit:</b>
                                                </div>
                                                <div class="credit_max col-md-6"
                                                    style="display: flex; justify-content: flex-end">0.00
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="total">
                                        <input type="hidden" id="sub_total">
                                        <input type="hidden" id="total_vat" value="0.00">
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div class="btn-group" style="float: right;">
                                                <button type="button" class="btn btn-danger" id="deselect-all-credit-sale">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" value="" id="category">
                                    <input type="hidden" value="" id="customers">
                                    <input type="hidden" value="" id="print">

                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- ajax loading gif -->
                    <div id="loading">
                        <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
                    </div>

                    @if(!auth()->user()->checkPermission('View Credit Sales') && !auth()->user()->checkPermission('View Credit Payment') && !auth()->user()->checkPermission('View Credit Tracking'))
                        <div class="tab-pane fade show" id="credit-sale-receiving" role="tabpanel"
                            aria-labelledby="credit_sales-tab">
                            <div class="row">

                                {{-- <p>You do not have permission to View New Credit Sales</p> --}}

                            </div>
                        </div>
                    @endif
                    {{-- End Credit Sales--}}

                    {{-- Credit Tracking--}}
                    @if(auth()->user()->checkPermission('View Credit Tracking'))
                        <div class="tab-pane fade" id="credit-tracking" role="tabpanel" aria-labelledby="credit_tracking-tab">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label id="cat_label">Customer<font color="red">*</font></label>
                                        <select name="customer_id" id="cust_id" class="js-example-basic-single form-control">
                                            <option value="" selected="true" disabled>Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->name }}">{{$customer->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="cat_label">Status:<font color="red">*</font></label>
                                        <select name="status" id="payment-status" class="js-example-basic-single form-control">
                                            <option value="" selected="true" disabled>Select Status</option>
                                            <option value="all">All</option>
                                            <option value="not_paid">Not Paid</option>
                                            <option value="partial_paid">Partial Paid</option>
                                            <option value="full_paid">Full Paid</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label id="cat_label">Date<font color="red">*</font></label>
                                        <input style="width: 110%;" type="text" name="date_of_sale" class="form-control"
                                            id="sales_date" value="" />
                                    </div>
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
                                @if(auth()->user()->checkPermission('View Credit Payment'))
                                    <div id="can_pay"></div>
                                @endif
                                <div class="table teble responsive" style="width: 100%;">
                                    <table id="credit_payment_table" class="display table nowrap table-striped table-hover"
                                        style="width:100%">

                                        <thead>
                                            <tr>
                                                <th>Receipt #</th>
                                                <th>Customer</th>
                                                <th>Sales Date</th>
                                                <th>Total</th>
                                                <th>Paid</th>
                                                <th>Balance</th>
                                                @if(auth()->user()->checkPermission('Add Credit Payment'))
                                                    <th>Action</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                            @include('sales.credit_sales.create_payment')
                        </div>
                    @endif

                    @if(!auth()->user()->checkPermission('View Credit Tracking'))
                        <div class="tab-pane fade" id="credit-sale-receiving" role="tabpanel"
                            aria-labelledby="credit_sales-tab">
                            <div class="row">

                                {{-- <p>You do not have permission to View Credit Tracking</p> --}}

                            </div>
                        </div>
                    @endif
                    {{-- End Credit Tracking--}}

                    {{-- Start Credit Payment--}}
                    @if(auth()->user()->checkPermission('View Credit Payment'))
                        <div class="tab-pane fade" id="credit-payment" role="tabpanel" aria-labelledby="credit_payment-tab">
                            <div class="form-group row">
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3" style="margin-left: 2.5%">
                                    <label style="margin-left: 62%" for=""
                                        class="col-form-label text-md-right">Customer:</label>
                                </div>
                                <div class="col-md-3" style="margin-left: -3.2%;">
                                    <select name="customer_id" id="customer_payment"
                                        class="js-example-basic-single form-control" onchange="filterPaymentHistory()">
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
                                <div class="col-md-3" style="margin-left: 1.4%">
                                    <label style="margin-left: 80%" for="" class="col-form-label text-md-right">Date:</label>
                                </div>
                                <div class="col-md-3" style="margin-left: -3%;">
                                    <input style="width: 107%;" type="text" name="date_of_sale" class="form-control"
                                        id="sales_date_payment" value="" autocomplete="off" />
                                </div>
                            </div>

                            <div class="table-responsive" id="main_table">
                                <table id="fixed-header-main" class="display table nowrap table-striped table-hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Receipt #</th>
                                            <th>Customer Name</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <div class="table-responsive" id="filter_history" style="display: none">
                                <table id="fixed-header-filter" class="display table nowrap table-striped table-hover"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Receipt #</th>
                                            <th>Customer Name</th>
                                            <th>Payment Date</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>

                                </table>
                            </div>

                            <input type="hidden" value="" id="category">
                            <input type="hidden" value="" id="customers">
                            <input type="hidden" value="" id="print">
                        </div>
                    @endif

                    @if(!auth()->user()->checkPermission('View Credit Payment'))
                        <div class="tab-pane fade" id="credit-payment" role="tabpanel" aria-labelledby="credit_payment-tab">
                            <div class="row">

                                {{-- <p>You do not have permission to View Credit Payment</p> --}}

                            </div>
                        </div>
                    @endif
                    {{-- End Credit Payment--}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let activeTabView = localStorage.getItem("creditActiveTab");

            if (activeTabView) {
                // Ondoa active kwa zote
                document.querySelectorAll(".nav-link").forEach(el => el.classList.remove("active"));
                document.querySelectorAll(".tab-pane").forEach(el => el.classList.remove("active", "show"));

                // Ongeza active kwenye tab iliyohifadhiwa
                let tabBtn = document.getElementById(activeTabView + "-tablist");
                let tabPane = document.getElementById(activeTabView);

                tabBtn?.classList.add("active");
                tabPane?.classList.add("active", "show");
            }
        });

    </script>


    @include('sales.customers.create')

@endsection

@push("page_scripts")

    {{-- For credit sales --}}
    @include('partials.notification')


    <script type="text/javascript">



        var page_no = 1;//sales page
        var normal_search = 0;//search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                selectProducts: '{{route('selectProducts')}}',
                storeCreditSale: '{{route('credit-sales.storeCreditSale')}}',
                filterProductByWord: '{{route('filter-product-by-word')}}',
                getCreditSale: '{{route('getCreditSale')}}'
            }
        };
        var canAddCreditPayment = {{ auth()->user()->checkPermission('Add Credit Payment') ? 'true' : 'false' }};

    </script>
    <script src="{{asset("assets/plugins/moment/js/moment.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{asset("assets/apotek/js/sales/credit.js")}}"></script>
    <script src="{{asset("assets/apotek/js/customer.js")}}"></script>
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    {{-- For credit tracking --}}
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () { $('#credit_barcode_input').focus(); }, 150);
            var start = moment();
            var end = moment();


            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sales_date').daterangepicker({
                startDate: moment().startOf('month'),
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
    <script type="text/javascript">

        // Listen for the click event on the tab
        $('#credit-sale-receiving-tablist').on('click', function () {
            // console.log('New credit tab clicked');
            localStorage.setItem('creditActiveTab', 'credit-sale-receiving');
            setTimeout(function () { $('#credit_barcode_input').focus(); }, 150);

        });

        // Listen for the click event on the tab
        $('#credit-tracking-tablist').on('click', function () {
            // console.log('Credit Tracking tab clicked');
            localStorage.setItem('creditActiveTab', 'credit-tracking');
            getCredits();

        });

    </script>

    {{-- For credit payment --}}
    <script>
        $('#fixed-header-main').DataTable({
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false
        });

        let payment_history_filter_table = $('#fixed-header-filter').DataTable({
            columns: [
                { 'data': 'receipt_number' },
                { 'data': 'name' },
                {
                    'data': 'created_at', render: function (date) {
                        return moment(date).format('YYYY-MM-DD');
                    }
                },
                {
                    'data': 'paid_amount', render: function (amount) {
                        return formatMoney(amount);
                    }
                }
            ],
            columnDefs: [
                {
                    type: 'date',
                    targets: [1]
                }
            ],
            ordering: false,
            // aaSorting: [[1, "desc"]]
        });

        function filterPaymentHistory() {
            let customer_id = document.getElementById('customer_payment').value;
            let date = document.getElementById('sales_date_payment').value;

            if (customer_id === '') {
                customer_id = null;
            }

            if (date === '') {
                date = null;
            }

            /*make ajax call for more*/
            $.ajax({
                url: '{{route('payment-history-filter')}}',
                type: "get",
                dataType: "json",
                data: {
                    customer_id: customer_id,
                    date: date
                },
                success: function (data) {
                    console.log('This is data', data)
                    document.getElementById('main_table').style.display = 'none';
                    document.getElementById('filter_history').style.display = 'block';

                    data = data.filter(function (el) {
                        return Number(el.paid_amount) !== Number(0);
                    });

                    payment_history_filter_table.clear();
                    payment_history_filter_table.rows.add(data);
                    payment_history_filter_table.draw();


                }
            });


        }

        $(function () {

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
            }

            $('#sales_date_payment').daterangepicker({
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
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

            $('input[name="date_of_sale"]').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
                filterPaymentHistory();
            });

            $('input[name="date_of_sale"]').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

        });

        function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
            try {
                decimalCount = Math.abs(decimalCount);
                decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
            }
        }

        //Payment Clicked
        $('#credit-payment-tablist').on('click', function () {
            // console.log('Credit Payment tab clicked');
            localStorage.setItem('creditActiveTab', 'credit-payment');

            filterPaymentHistory();

        });

    </script>

    <script>
        $('#cust_id').on('change', function (e) {
            // e.preventDefault();

            const selectedValue = $(this).val();
            console.log("DataSelected", selectedValue);


            if (selectedValue === 'Select Customer') {
                credit_payment_table.column(1).search('').draw();
            }

            // Check if nothing is selected and reset the filter
            if (selectedValue && selectedValue !== 'Select Customer') {
                credit_payment_table.column(1).search(selectedValue).draw();
            } else {
                credit_payment_table.column(1).search('').draw();
            }
        });
    </script>

@endpush