@extends("layouts.master")



@section('page_css')
    <style>
        #zoom:hover {
            -ms-transform: scale(1.02);
            /* IE 9 */
            -webkit-transform: scale(1.02);
            /* Safari 3-8 */
            transform: scale(1.02);
        }

        #zoom {
            transition: transform .2s;
            height: 45vh;
        }

        #daily_sales {
            width: 100%;
            height: 500px;
        }

        #monthly_sales {
            width: 100%;
            height: 400px;
        }

        #sales_by_category {
            width: 100%;
            height: 400px;
        }

        #daily_purchase {
            width: 100%;
            height: 500px;
        }

        #monthly_purchase {
            width: 100%;
            height: 400px;
        }

        #purchase_by_category {
            width: 100%;
            height: 400px;
        }

        #daily_expense {
            width: 100%;
            height: 500px;
        }

        #monthly_expense {
            width: 100%;
            height: 400px;
        }

        #expense_by_category {
            width: 100%;
            height: 400px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }


        /**
                          Component
                        **/

        label {
            width: 100%;
        }

        .card-input-element {
            display: none;
        }

        .card-input {
            margin: 10px;
            padding: 00px;
        }

        .card-input:hover {
            cursor: pointer;
        }

        .card-input-element:checked+.card-input {
            box-shadow: 0 0 1px 1px #04a9f5;
        }
    </style>
@endsection

@if(auth()->user()->checkPermission('View Dashboard'))
    @section('content-title')
        Dashboard

    @endsection

    @section('content-sub-title')


    @endsection



    @section("content")

        <div class="col-sm-12">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @if(auth()->user()->checkPermission('View Sales Summary'))
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#pills-home" role="tab" aria-selected="true">Sales
                            Summary</a>
                    </li>
                @endif

                @if(auth()->user()->checkPermission('View Purchasing Summary'))
                    <li class="nav-item">
                        @if(!auth()->user()->checkPermission('View Sales Summary'))
                            <a class="nav-link active" data-toggle="pill" href="#pills-purchase" role="tab"
                                aria-selected="false">Purchasing
                                Summary</a>
                        @endif

                        @if(auth()->user()->checkPermission('View Sales Summary'))
                            <a class="nav-link" data-toggle="pill" href="#pills-purchase" role="tab" aria-selected="false">Purchasing
                                Summary</a>
                        @endif

                    </li>
                @endif

                @if(auth()->user()->checkPermission('View Inventory Summary'))
                    <li class="nav-item">
                        @if(!auth()->user()->checkPermission('View Sales Summary') && !auth()->user()->checkPermission('View Purchasing Summary'))
                            <a class="nav-link active" data-toggle="pill" href="#pills-stock" role="tab" aria-selected="false">Inventory
                                Summary</a>
                        @endif

                        @if(auth()->user()->checkPermission('View Sales Summary') || auth()->user()->checkPermission('View Purchasing Summary'))
                            <a class="nav-link" data-toggle="pill" href="#pills-stock" role="tab" aria-selected="false">Inventory
                                Summary</a>
                        @endif
                    </li>
                @endif

                @if(auth()->user()->checkPermission('View Transport Summary'))
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#pills-transport" role="tab" aria-selected="false">Transport
                            Summary</a>
                    </li>
                @endif

                @if(auth()->user()->checkPermission('View Accounting Summary'))
                    <li class="nav-item">
                        @if(!auth()->user()->checkPermission('View Sales Summary') && !auth()->user()->checkPermission('View Purchasing Summary') && !auth()->user()->checkPermission('View Inventory Summary'))
                            <a class="nav-link active" data-toggle="pill" href="#pills-expense" role="tab"
                                aria-selected="false">Accounting
                                Summary</a>
                        @endif

                        @if(auth()->user()->checkPermission('View Sales Summary') || auth()->user()->checkPermission('View Purchasing Summary') || auth()->user()->checkPermission('View Inventory Summary'))
                            <a class="nav-link" data-toggle="pill" href="#pills-expense" role="tab" aria-selected="false">Accounting
                                Summary</a>
                        @endif

                    </li>
                @endif


            </ul>


            <div class="tab-content" id="pills-tabContent">
                {{-- Tab 1 --}}
                @if(auth()->user()->checkPermission('View Sales Summary'))
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        {{-- row 1 start --}}
                        <div class="row">
                            <!-- [ Today sales section ] start -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-block">
                                        <h6 class="mb-4">Average Daily Sales</h6>
                                        <div class="row d-flex align-items-center">
                                            <div class="col-9">
                                                <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                    Tshs {{ number_format($pharmacy_data['avgDailySales'], 2) }}
                                                </h3>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- [ Today sales section ] end -->

                            <!-- [ This week sales section ] start -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-block">
                                        <h6 class="mb-4">Today Sales</h6>
                                        <div class="row d-flex align-items-center">
                                            <div class="col-9">
                                                <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                    @if ($pharmacy_data['todaySales'] > $pharmacy_data['avgDailySales'])
                                                        <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                                    @else
                                                        <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                                    @endif

                                                    Tshs {{ number_format($pharmacy_data['todaySales'], 2) }}

                                                </h3>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- [ This week  sales section ] end -->

                            <!-- [ This month sales section ] start -->
                            <div class="col-md-6 col-xl-4">
                                <div class="card">
                                    <div class="card-block">
                                        <h6 class="mb-4">Average Monthly Sales</h6>
                                        <div class="row d-flex align-items-center">
                                            <div class="col-9">
                                                <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                    Tshs {{ number_format($pharmacy_data['avgDailySales'] * 30, 2) }}</h3>
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- [ this month  sales section ] end -->

                        </div>
                        {{-- row 1 end --}}


                        {{-- row 2 start --}}
                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <div id='monthly_sales'></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-xl-12">
                                <div id='sales_by_category'></div>
                            </div>
                        </div>

                        {{-- row 3 start --}}
                        <div class="row">
                            <div id='daily_sales'></div>
                        </div>

                    </div>
                @endif
                {{-- end Tab 1 --}}

                {{-- Tab 2 --}}
                @if(auth()->user()->checkPermission('View Inventory Summary'))
                    @if(!auth()->user()->checkPermission('View Sales Summary') && !auth()->user()->checkPermission('View Purchasing Summary'))
                        <div class="tab-pane fade show active" id="pills-stock" role="tabpanel" aria-labelledby="pills-stock-tab">
                            {{-- row starts --}}
                            <div class="row">
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="out_of_stock" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Out of Stock</h5>
                                                    </div>
                                                    <div class="panel-body">
                                                        <h3 class="text-c-red">{{$outOfStock}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="below" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Fast Moving</h5>
                                                    </div>
                                                    <div class="panel-body text-c-green">
                                                        <h3 class="text-c-green">{{$fast_moving}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="expired" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Expired</h5>
                                                    </div>
                                                    <div class="panel-body text-c-red">
                                                        <h3 class="text-c-red">{{$expired}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <!-- [ Out of stock start -->
                                <div class="col-xl-12 col-md-12">
                                    <div class="table-responsive" style="display: none" id="stock_items_table">
                                        <table id="stock_items" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive" style="display: none" id="stock_items_fast_table">
                                        <table id="stock_items_fast" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Sold Times</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive" style="display: none" id="stock_items_expired_table">
                                        <table id="stock_items_expired" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Expiry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->checkPermission('View Sales Summary') || auth()->user()->checkPermission('View Purchasing Summary'))
                        <div class="tab-pane fade" id="pills-stock" role="tabpanel" aria-labelledby="pills-stock-tab">
                            {{-- row starts --}}
                            <div class="row">
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="out_of_stock" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Out of Stock</h5>
                                                    </div>
                                                    <div class="panel-body">
                                                        <h3 class="text-c-red">{{$outOfStock}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="below" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Fast Moving</h5>
                                                    </div>
                                                    <div class="panel-body text-c-green">
                                                        <h3 class="text-c-green">{{$fast_moving}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                                <div class="col-md-4 col-lg-4 col-sm-4">

                                    <label>
                                        <input type="radio" name="stock" id="expired" class="card-input-element" />

                                        <div class="panel panel-default card-input">
                                            <div class="card">
                                                <div class="card-block">
                                                    <div class="panel-heading">
                                                        <h5>Expired</h5>
                                                    </div>
                                                    <div class="panel-body text-c-red">
                                                        <h3 class="text-c-red">{{$expired}}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                <!-- [ Out of stock start -->
                                <div class="col-xl-12 col-md-12">
                                    <div class="table-responsive" style="display: none" id="stock_items_table">
                                        <table id="stock_items" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive" style="display: none" id="stock_items_fast_table">
                                        <table id="stock_items_fast" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Sold Times</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="table-responsive" style="display: none" id="stock_items_expired_table">
                                        <table id="stock_items_expired" class="display table nowrap table-striped table-hover"
                                            style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width: 1%;">#</th>
                                                    <th>Product Name</th>
                                                    <th>Quantity</th>
                                                    <th>Expiry Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>
                    @endif
                @endif

                {{-- end Tab 2 --}}

                {{-- Tab 3 --}}
                @if(auth()->user()->checkPermission('View Purchasing Summary'))
                    @if(!auth()->user()->checkPermission('View Sales Summary'))
                        <div class="tab-pane fade show active" id="pills-purchase" role="tabpanel" aria-labelledby="pills-purchase-tab">
                            {{-- row 1 start--}}
                            <div class="row">
                                <!-- [ Today purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Daily Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                        Tshs {{ number_format($purchase_data['avgDailyPurchases'], 2) }}</h3>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ Today purchase section ] end -->

                                <!-- [ This week purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Today Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        @if ($purchase_data['todayPurchases'] > $purchase_data['avgDailyPurchases'])
                                                            <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                                        @else
                                                            <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                                        @endif

                                                        Tshs {{ number_format($purchase_data['todayPurchases'], 2) }}

                                                    </h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ This week  purchase section ] end -->

                                <!-- [ This month purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Monthly Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        Tshs {{ number_format($purchase_data['avgDailyPurchases'] * 30, 2) }}</h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ this month  purchase section ] end -->

                            </div>
                            {{-- row 1 end--}}


                            {{-- row 2 start--}}
                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='monthly_purchase'></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='purchase_by_category'></div>
                                </div>
                            </div>

                            {{-- row 3 start--}}
                            <div class="row">
                                <div id='daily_purchase'></div>
                            </div>

                        </div>
                    @endif

                    @if(auth()->user()->checkPermission('View Sales Summary'))
                        <div class="tab-pane fade" id="pills-purchase" role="tabpanel" aria-labelledby="pills-purchase-tab">
                            {{-- row 1 start--}}
                            <div class="row">
                                <!-- [ Today purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Daily Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                        Tshs {{ number_format($purchase_data['avgDailyPurchases'], 2) }}</h3>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ Today purchase section ] end -->

                                <!-- [ This week purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Today Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        @if ($purchase_data['todayPurchases'] > $purchase_data['avgDailyPurchases'])
                                                            <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                                        @else
                                                            <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                                        @endif

                                                        Tshs {{ number_format($purchase_data['todayPurchases'], 2) }}

                                                    </h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ This week  purchase section ] end -->

                                <!-- [ This month purchase section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Monthly Purchases</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        Tshs {{ number_format($purchase_data['avgDailyPurchases'] * 30, 2) }}</h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ this month  purchase section ] end -->

                            </div>
                            {{-- row 1 end--}}


                            {{-- row 2 start--}}
                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='monthly_purchase'></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='purchase_by_category'></div>
                                </div>
                            </div>

                            {{-- row 3 start--}}
                            <div class="row">
                                <div id='daily_purchase'></div>
                            </div>

                        </div>
                    @endif
                @endif

                {{-- end Tab 3 --}}

                {{-- Tab 4 --}}
                @if(auth()->user()->checkPermission('View Accounting Summary'))
                    @if(!auth()->user()->checkPermission('View Sales Summary') && !auth()->user()->checkPermission('View Purchasing Summary') && !auth()->user()->checkPermission('View Inventory Summary'))
                        <div class="tab-pane fade show active" id="pills-expense" role="tabpanel" aria-labelledby="pills-expense-tab">
                            {{-- row 1 start--}}
                            <div class="row">
                                <!-- [ Today expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Daily Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                        Tshs {{ number_format($expense_data['avgDailyExpenses'], 2) }}</h3>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ Today expense section ] end -->

                                <!-- [ This week expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Today Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        @if ($expense_data['todayExpenses'] > $expense_data['avgDailyExpenses'])
                                                            <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                                        @else
                                                            <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                                        @endif

                                                        Tshs {{ number_format($expense_data['todayExpenses'], 2) }}

                                                    </h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ This week  expense section ] end -->

                                <!-- [ This month expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Monthly Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        Tshs {{ number_format($expense_data['avgDailyExpenses'] * 30, 2) }}</h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ this month  expense section ] end -->

                            </div>
                            {{-- row 1 end--}}


                            {{-- row 2 start--}}
                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='monthly_expense'></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='expense_by_category'></div>
                                </div>
                            </div>

                            {{-- row 3 start--}}
                            <div class="row">
                                <div id='daily_expense'></div>
                            </div>

                        </div>
                    @endif

                    @if(auth()->user()->checkPermission('View Sales Summary') || auth()->user()->checkPermission('View Purchasing Summary') || auth()->user()->checkPermission('View Inventory Summary'))
                        <div class="tab-pane fade" id="pills-expense" role="tabpanel" aria-labelledby="pills-expense-tab">
                            {{-- row 1 start--}}
                            <div class="row">
                                <!-- [ Today expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Daily Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                        Tshs {{ number_format($expense_data['avgDailyExpenses'], 2) }}</h3>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ Today expense section ] end -->

                                <!-- [ This week expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Today Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        @if ($expense_data['todayExpenses'] > $expense_data['avgDailyExpenses'])
                                                            <i class="feather icon-arrow-up text-c-green f-30 m-r-10"></i>
                                                        @else
                                                            <i class="feather icon-arrow-down text-c-red f-30 m-r-10"></i>
                                                        @endif

                                                        Tshs {{ number_format($expense_data['todayExpenses'], 2) }}

                                                    </h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ This week  expense section ] end -->

                                <!-- [ This month expense section ] start -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card">
                                        <div class="card-block">
                                            <h6 class="mb-4">Average Monthly Expenses</h6>
                                            <div class="row d-flex align-items-center">
                                                <div class="col-9">
                                                    <h3 class="f-w-300 d-flex align-items-center  m-b-0">
                                                        Tshs {{ number_format($expense_data['avgDailyExpenses'] * 30, 2) }}</h3>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <!-- [ this month  expense section ] end -->

                            </div>
                            {{-- row 1 end--}}


                            {{-- row 2 start--}}
                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='monthly_expense'></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-xl-12">
                                    <div id='expense_by_category'></div>
                                </div>
                            </div>

                            {{-- row 3 start--}}
                            <div class="row">
                                <div id='daily_expense'></div>
                            </div>

                        </div>
                    @endif
                @endif
                {{-- end Tab 4 --}}

                {{-- Tab 5 --}}
                <div class="tab-pane fade" id="pills-transport" role="tabpanel" aria-labelledby="pills-transport-tab">
                    <div class="row">
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-block">
                                    <h6 class="mb-4">Trips</h6>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-9">
                                            <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                {{ number_format($transport_data['total_trips']) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-block">
                                    <h6 class="mb-4">Revenue</h6>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-9">
                                            <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                Tshs {{ number_format($transport_data['total_revenue'], 2) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
                            <div class="card">
                                <div class="card-block">
                                    <h6 class="mb-4">Pending</h6>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-9">
                                            <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                {{ number_format($transport_data['pending_trips']) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
                            <div class="card">
                                <div class="card-block">
                                    <h6 class="mb-4">In Transit</h6>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-9">
                                            <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                {{ number_format($transport_data['in_transit_trips']) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-2">
                            <div class="card">
                                <div class="card-block">
                                    <h6 class="mb-4">Delivered</h6>
                                    <div class="row d-flex align-items-center">
                                        <div class="col-9">
                                            <h3 class="f-w-300 d-flex align-items-center m-b-0">
                                                {{ number_format($transport_data['delivered_trips']) }}
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end Tab 5 --}}

            </div>

        </div>
        </div>


    @endsection

    @push("page_scripts")

        <script src="{{asset("assets/plugins/amcharts4/core.js")}}"></script>
        <script src="{{asset("assets/plugins/amcharts4/charts.js")}}"></script>
        <script src="{{asset("assets/plugins/amcharts4/themes/animated.js")}}"></script>
        <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

        <script>

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function () {
                var a = document.getElementById('out_of_stock');
                a.click();
            });

            $('#out_of_stock').on('click', function () {

                document.getElementById('stock_items_table').style.display = 'block';
                document.getElementById('stock_items_expired_table').style.display = 'none';
                document.getElementById('stock_items_fast_table').style.display = 'none';

                $('#stock_items').DataTable().clear().destroy();
                $('#stock_items').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('stock-summary') }}",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": {
                            _token: "{{csrf_token()}}",
                            summary_no: 1
                        }
                    },
                    'columns': [
                        {
                            'data': null,
                            'render': function (data, type, row, meta) {
                                return meta.row + 1 + ('.');
                            }
                        },
                        { 'data': 'name' },
                        { 'data': 'category' }
                    ],
                    'searching': false,
                    "columnDefs": [
                        {
                            "targets": 0,
                            "orderable": false,
                        }
                    ]

                });

            });

            $('#below').on('click', function () {

                document.getElementById('stock_items_table').style.display = 'none';
                document.getElementById('stock_items_expired_table').style.display = 'none';
                document.getElementById('stock_items_fast_table').style.display = 'block';

                $('#stock_items_fast').DataTable().clear().destroy();
                $('#stock_items_fast').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('stock-summary') }}",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": {
                            _token: "{{csrf_token()}}",
                            summary_no: 2
                        }
                    },
                    'columns': [
                        {
                            'data': null,
                            'render': function (data, type, row, meta) {
                                return meta.row + 1 + ('.');
                            }
                        },
                        { 'data': 'product_name' },
                        { 'data': 'occurrence' }
                    ],
                    'searching': false,
                    "columnDefs": [
                        {
                            "targets": 0,
                            "orderable": false,
                        }
                    ]

                });
            });

            $('#expired').on('click', function () {
                document.getElementById('stock_items_table').style.display = 'none';
                document.getElementById('stock_items_expired_table').style.display = 'block';
                document.getElementById('stock_items_fast_table').style.display = 'none';

                $('#stock_items_expired').DataTable().clear().destroy();
                $('#stock_items_expired').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('stock-summary') }}",
                        "dataType": "json",
                        "type": "post",
                        "cache": false,
                        "data": {
                            _token: "{{csrf_token()}}",
                            summary_no: 3
                        }
                    },
                    'columns': [
                        {
                            'data': null,
                            'render': function (data, type, row, meta) {
                                return meta.row + 1 + ('.');
                            }
                        },
                        { 'data': 'name' },
                        { 'data': 'quantity' },
                        { 'data': 'expiry_date' }
                    ],
                    'searching': false,
                    "columnDefs": [
                        {
                            "targets": 0,
                            "orderable": false,
                        }
                    ]

                });
            });

            function numberWithCommas(digit) {
                return String(parseFloat(digit)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

        </script>

        <script>
            $(document).ready(function () {

                totalByMonthChart(@json(($pharmacy_data['total_monthly'])), "Total Sales By Month", "monthly_sales");
                totalByMonthChart(@json(($purchase_data['total_monthly'])), "Total Purchases By Month", "monthly_purchase");
                totalByMonthChart(@json(($expense_data['total_monthly'])), "Total Expenses By Month", "monthly_expense");

                byCategoryChart(@json($pharmacy_data['salesByCategory']), "Total Sales By Category", "sales_by_category");
                byCategoryChart(@json($purchase_data['purchasesByCategory']), "Total Purchases By Category", "purchase_by_category");
                byCategoryChart(@json($expense_data['expensesByCategory']), "Total Expenses By Category", "expense_by_category");

                totalDailyChart(@json($pharmacy_data['totalDailySales']), "Total Daily Sales", "daily_sales");
                totalDailyChart(@json($purchase_data['totalDailyPurchases']), "Total Daily Purchases", "daily_purchase");
                totalDailyChart(@json($expense_data['totalDailyExpenses']), "Total Daily Expenses", "daily_expense");
            });
        </script>

        <script>
            function totalByMonthChart(data, chart_title, chart_id) {
                am4core.ready(function () {

                    // Themes begin
                    am4core.useTheme(am4themes_animated);
                    // Themes end

                    // Create chart instance
                    var chart = am4core.create(chart_id, am4charts.XYChart);
                    chart.scrollbarX = new am4core.Scrollbar();

                    // Add data
                    chart.data = data;


                    //title
                    var title = chart.titles.create();
                    title.text = chart_title;
                    title.fontSize = 16;
                    title.marginBottom = 15;
                    // Create axes
                    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                    categoryAxis.dataFields.category = "month";
                    categoryAxis.renderer.grid.template.location = 0;
                    categoryAxis.renderer.minGridDistance = 30;
                    categoryAxis.renderer.labels.template.horizontalCenter = "right";
                    categoryAxis.renderer.labels.template.verticalCenter = "middle";
                    categoryAxis.renderer.labels.template.rotation = 270;
                    categoryAxis.tooltip.disabled = true;
                    categoryAxis.renderer.minHeight = 110;

                    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis.renderer.minWidth = 50;

                    // Create series
                    var series = chart.series.push(new am4charts.ColumnSeries());
                    series.sequencedInterpolation = true;
                    series.dataFields.valueY = "amount";
                    series.dataFields.categoryX = "month";
                    series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
                    series.columns.template.strokeWidth = 0;

                    series.tooltip.pointerOrientation = "vertical";

                    series.columns.template.column.cornerRadiusTopLeft = 10;
                    series.columns.template.column.cornerRadiusTopRight = 10;
                    series.columns.template.column.fillOpacity = 0.8;

                    // on hover, make corner radiuses bigger
                    var hoverState = series.columns.template.column.states.create("hover");
                    hoverState.properties.cornerRadiusTopLeft = 0;
                    hoverState.properties.cornerRadiusTopRight = 0;
                    hoverState.properties.fillOpacity = 1;

                    series.columns.template.adapter.add("fill", function (fill, target) {
                        return chart.colors.getIndex(target.dataItem.index);
                    });

                    // Cursor
                    chart.cursor = new am4charts.XYCursor();

                });
            }

            function byCategoryChart(data, chart_title, chart_id) {
                am4core.ready(function () {

                    // Themes begin
                    am4core.useTheme(am4themes_animated);
                    // Themes end

                    // Create chart instance
                    var chart = am4core.create(chart_id, am4charts.PieChart);

                    // Add and configure Series
                    var pieSeries = chart.series.push(new am4charts.PieSeries());
                    pieSeries.dataFields.value = "amount";
                    pieSeries.dataFields.category = "category";

                    // Let's cut a hole in our Pie chart the size of 30% the radius
                    chart.innerRadius = am4core.percent(30);

                    //title
                    var title = chart.titles.create();
                    title.text = chart_title;
                    title.fontSize = 16;
                    title.marginBottom = 15;

                    // Put a thick white border around each Slice
                    pieSeries.slices.template.stroke = am4core.color("#fff");
                    pieSeries.slices.template.strokeWidth = 2;
                    pieSeries.slices.template.strokeOpacity = 1;
                    pieSeries.slices.template
                        // change the cursor on hover to make it apparent the object can be interacted with
                        .cursorOverStyle = [
                            {
                                "property": "cursor",
                                "value": "pointer"
                            }
                        ];

                    pieSeries.alignLabels = true;
                    pieSeries.labels.template.bent = true;
                    pieSeries.labels.template.radius = 3;
                    pieSeries.labels.template.padding(0, 0, 0, 0);

                    pieSeries.ticks.template.disabled = false;

                    // Create a base filter effect (as if it's not there) for the hover to return to
                    var shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
                    shadow.opacity = 0;

                    // Create hover state
                    var hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

                    // Slightly shift the shadow and make it more prominent on hover
                    var hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
                    hoverShadow.opacity = 0.7;
                    hoverShadow.blur = 5;


                    chart.data = data;

                });
            }

            function totalDailyChart(data, chart_title, chart_id) {
                // Themes begin
                am4core.useTheme(am4themes_animated);
                // Themes end

                // Create chart instance
                var chart = am4core.create(chart_id, am4charts.XYChart);

                // Add data
                chart.data = data;

                // Set input format for the dates
                chart.dateFormatter.inputDateFormat = "dd-MM-yyyy";

                // Create axes
                var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
                var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

                //title
                var title = chart.titles.create();
                title.text = chart_title;
                title.fontSize = 16;
                title.marginBottom = 15;

                // Create series
                var series = chart.series.push(new am4charts.LineSeries());
                series.dataFields.valueY = "value";
                series.dataFields.dateX = "date";
                series.tooltipText = "{value}";
                series.strokeWidth = 2;
                series.minBulletDistance = 5;
                series.fillOpacity = 0.3;


                // Drop-shaped tooltips
                series.tooltip.background.cornerRadius = 20;
                series.tooltip.background.strokeOpacity = 0;
                series.tooltip.pointerOrientation = "vertical";
                series.tooltip.label.minWidth = 40;
                series.tooltip.label.minHeight = 40;
                series.tooltip.label.textAlign = "middle";
                series.tooltip.label.textValign = "middle";

                // Make bullets grow on hover
                var bullet = series.bullets.push(new am4charts.CircleBullet());
                bullet.circle.strokeWidth = 2;
                bullet.circle.radius = 4;
                bullet.circle.fill = am4core.color("#fff");

                var bullethover = bullet.states.create("hover");
                bullethover.properties.scale = 1.3;

                // Make a panning cursor
                chart.cursor = new am4charts.XYCursor();
                chart.cursor.behavior = "panXY";
                chart.cursor.xAxis = dateAxis;
                chart.cursor.snapToSeries = series;

                // Create vertical scrollbar and place it before the value axis
                chart.scrollbarY = new am4core.Scrollbar();
                chart.scrollbarY.parent = chart.leftAxesContainer;
                chart.scrollbarY.toBack();

                // Create a horizontal scrollbar with previe and place it underneath the date axis
                chart.scrollbarX = new am4charts.XYChartScrollbar();
                chart.scrollbarX.series.push(series);
                chart.scrollbarX.parent = chart.bottomAxesContainer;

                chart.events.on("ready", function () {
                    dateAxis.zoom({ start: 0.79, end: 1 });
                });
            }

        </script>

    @endpush

@else
    @include('home_welcome')
@endif