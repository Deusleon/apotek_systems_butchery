@extends("layouts.master")

@section('content-title')
    Cash Sales
@endsection
@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Cash Sales</a></li>
@endsection


@section("content")
    <style>
        .datepicker>.datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
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
                <div class="tab-content" id="myTabContent">
                    <form id="sales_form">
                        @if (auth()->user()->checkPermission('Add Customers'))
                            <div class="row">
                                <div class="col-md-12">
                                    <button style="float: right;margin-bottom: 2%;" type="button"
                                        class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create"> Add
                                        New Customer
                                    </button>
                                </div>
                            </div>
                        @endif
                        @csrf()
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label id="cat_label">Sales Type<font color="red">*</font></label>
                                    <select id="price_category" class="js-example-basic-single form-control" required>
                                        <option value="" selected="true" disabled>Select Type</option>
                                        @foreach($price_category as $price)
                                            <option value="{{$price->id}}" {{$default_sale_type === $price->id ? 'selected' : ''}}>{{$price->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <input type="text" id="barcode_input" style="position:absolute; left:-9999px;" autofocus>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Products<font color="red">*</font></label>
                                    <select id="products" class="form-control">
                                        <option value="" disabled selected style="display:none;">Select Product</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="code">Customer Name </label>
                                    <select name="customer_id" id="customer_id"
                                        class="js-example-basic-single form-control">
                                        <option value="" disabled>Select Customer</option>
                                        @foreach($customers as $customer)
                                            <!-- <option value="{{$customer->id}}">{{$customer->name}}</option> -->
                                            <option value="{{$customer->id}}" {{$default_customer === $customer->id ? 'selected' : ''}}>{{$customer->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ajax loading gif -->
                        <div id="loading" style="display: none; z-index: 60;">
                            <img id="loading-image" src="{{asset('assets/images/spinner.gif')}}" />
                        </div>

                        <div class="row" id="detail">
                            <hr>
                            <div class="table teble responsive" style="width: 100%;">
                                <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
                            </div>

                        </div>
                        <hr>
                        <input type="hidden" name="" id="is_backdate_enabled" value="{{$back_date}}">
                        @if($back_date == "NO")
                            <div class="row">
                                <div class="col-md-4">
                                    @if($enable_discount === "YES")
                                        <div style="width: 99%">
                                            <label>Discount</label>
                                            <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                value="0.00" />
                                            <span class="help-inline">
                                                <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                    Discount</div>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div style="width: 99%" hidden>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="sub_total" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total_vat" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Total
                                                Amount:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                                value="0.00" />

                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="price_cat" name="price_category_id">
                                <input type="hidden" id="discount_value" name="discount_amount">
                                <input type="hidden" id="order_cart" name="cart">
                                <input type="hidden" value="{{$vat}}" id="vat">
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-4">
                                    <div style="width: 99%">
                                        <label>Sales Date<font color="red">*</font></label>
                                        <input type="text" name="sale_date" class="form-control" id="cash_sale_date"
                                            autocomplete="off" required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if($enable_discount === "YES")
                                        <div style="width: 99%">
                                            <label>Discount</label>
                                            <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                                value="0.00" />
                                        </div>
                                        <span class="help-inline">
                                            <div class="text text-danger" style="display: none;" id="discount_error">Invalid
                                                Discount</div>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="sub_total" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total_vat" class="form-control-plaintext text-md-right"
                                                readonly value="0.00" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-6 col-form-label text-md-right"><b>Total
                                                Amount:</b></label>
                                        <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                            <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                                value="0.00" />

                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="price_cat" name="price_category_id">
                                <input type="hidden" id="discount_value" name="discount_amount">
                                <input type="hidden" id="order_cart" name="cart">
                                <input type="hidden" value="{{$vat}}" id="vat">
                                <input type="hidden" value="" id="total_vat">
                            </div>
                        @endif
                        <input type="hidden" value="{{$price_category}}" id="category">
                        <input type="hidden" value="{{$customers}}" id="customers">
                        <input type="hidden" value="{{$fixed_price}}" id="fixed_price">
                        <input type="hidden" value="{{$enable_discount}}" id="enable_discount">

                        @if($enable_paid === "YES")
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div style="width: 99%">
                                        <label><b>Paid</b></label>
                                        <input type="text" onchange="discount()" id="sale_paid" class="form-control"
                                            value="0.00" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div style="width: 99%">
                                        <label><b>Change</b></label>
                                        <input type="text" id="change_amount" class="form-control" value="0.00" readonly />
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-danger" id="deselect-all" onclick="return false">Cancel
                                    </button>
                                    <button class="btn btn-primary" id="save_btn">Save</button>
                                </div>
                            </div>
                        </div>

                    </form>


                </div>

            </div>
        </div>
    </div>

    @include('sales.customers.create')
@endsection
@push("page_scripts")
    @include('partials.notification')
    <script type="text/javascript">

        // Connect to QZ Tray when page loads
        // qz.websocket.connect().then(function() {
        //     console.log("Connected to QZ Tray");
        // }).catch(function(err) {
        //     console.error("Error connecting to QZ Tray:", err);
        // });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                selectProducts: '{{route('selectProducts')}}',
                storeCashSale: '{{route('cash-sales.storeCashSale')}}',
                filterProductByWord: '{{route('filter-product-by-word')}}'

            }
        };

    </script>
    <script src="{{asset('assets/apotek/js/notification.js')}}"></script>
    <script src="{{asset('assets/apotek/js/sales.js')}}"></script>
    <script src="{{asset('assets/apotek/js/customer.js') }}"></script>
    <script src="{{asset('assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script>

@endpush