@extends("layouts.master")

@section('content-title')

    Goods Receiving

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchases / Goods Receiving</a></li>
@endsection
@section("content")

    <style>
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }
    </style>

    <style type="text/css">
        .iti__flag {
            background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags.png")}}");
        }

        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{asset("assets/plugins/intl-tel-input/img/flags@2x.png")}}");
            }
        }

        .iti {
            width: 100%;
        }
    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="quotes_invoicelist-tab" data-toggle="pill"
                   href="#invoice-receiving" role="tab"
                   aria-controls="quotes_list" aria-selected="true">Invoice Receiving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="new_quotes-tab" data-toggle="pill" href="#order-receive"
                   role="tab" aria-controls="new_quotes" aria-selected="false">Order Receiving
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link  text-uppercase" id="quotes_list-tab" data-toggle="pill"
                   href="#item-receive" role="tab"
                   aria-controls="quotes_list" aria-selected="true">Product Receiving</a>
            </li> -->
            
        </ul>
        <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="invoice-receiving" role="tabpanel"
                 aria-labelledby="quotes_invoicelist-tab">
                <form name="item" id="invoiceFormId">
                    @csrf()
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="code">Supplier Name <font color="red">*</font></label>
                                <select name="supplier" class="js-example-basic-single form-control"
                                        id="good_receiving_supplier_ids" required="true" onchange="goodReceivingFilterInvoiceBySupplier()">
                                    <option selected="true" value="" disabled="disabled">Select Supplier...</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        <!-- <option
                                                value="{{$supplier->id}}" {{$default_supplier->id === $supplier->id  ? 'selected' : ''}}>{{$supplier->name}}</option> -->
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Products <font color="red">*</font></label>
                                <select id="invoiceselected-product" class="js-example-basic-single form-control">
                                    <option selected="true" value="" disabled="disabled">Select Product...</option>
                                    @foreach($current_stock as $stock)
                                        <option
                                            value="{{$stock['product_name'].'#@'.$stock['product_id'].'#@'.$stock['unit_cost']}}">{{$stock['product_name']}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                @if($invoice_setting === 'YES')
                                    <label for="code">Invoice # <font color="red">*</font></label>
                                    <select name="invoice_no" class="form-control js-example-basic-single"
                                            id="goodreceving_invoice_id" required>
                                        <option selected="true" value="" disabled="disabled">Select Invoice..</option>
                                    </select>
                                @else
                                    <label for="code">Invoice #</label>
                                    <select name="invoice_no" class="form-control js-example-basic-single"
                                            id="goodreceving_invoice_id">
                                        <option selected="true" value="" disabled="disabled">Select Invoice..</option>

                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                @if($batch_setting === 'YES')
                                    <label for="code">Batch # <font color="red">*</font></label>
                                    <input type="text" name="batch_number" class="form-control" id="invoicing_batch_n"
                                           required="true" value="{{session('batch_number')}}"/>
                                @else
                                    <label for="code">Batch #</label>
                                    <input type="text" name="batch_number" class="form-control" id="invoicing_batch_n"
                                           value="{{session('batch_number')}}"/>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row" id="detail">
                        <hr>
                        <div class="table teble responsive" style="width: 100%;">
                            <table id="invoicecart_table" class="table nowrap table-striped table-hover" width="100%"></table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group" style="padding-top: 10px">
                                <div style="width: 99%">
                                    <label for="invoiceprice_category">Price Category <font color="red">*</font></label>
                                    <select name="price_category" class="form-control"
                                            id="invoiceprice_category" required="true" onchange="priceByCategory()">
                                        @foreach($price_categories as $price_category)
                                            <option value="{{$price_category->id}}">{{$price_category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            @if($back_date=="YES")
                                <div class="form-group" style="padding-top: 10px">
                                    <label>Purchase Date <font color="red">*</font></label>
                                    <input type="text" name="purchase_date" class="form-control" id="invoicing_purchase_date"
                                            autocomplete="off" required="true">

                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-3"></div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Buy:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_buying_price"
                                        class="form-control-plaintext text-md-right" readonly value="0.00"/>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Sell:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_selling_price"
                                        class="form-control-plaintext text-md-right" readonly value="0.00"/>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Profit:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total"
                                        class="form-control-plaintext text-md-right" readonly value="0.00"/>
                                </div>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" id="invoice_received_cart" name="cart">
                    <input type="hidden" name="store" id="store_id" value = "{{$default_store_id}}">
                    <input type="hidden" id="expire_date_enabler" value = "{{$expire_date}}">
                    <input type="hidden" name="invoice_price_category" id="price_category_for_all">
                    <input type="hidden" name="" id="buy">
                    <input type="hidden" name="batch_setting" id="batch_setting" value="{{$batch_setting}}">
                    <input type="hidden" name="invoice_setting" id="invoice_setting" value="{{$invoice_setting}}">
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-danger" id="cancel-all" onclick="resetForms()">
                                    Clear
                                </button>
                                <button id="invoicesave_id"
                                        class="btn btn-primary">Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade" id="order-receive" role="tabpanel" aria-labelledby="new_quotes-tab">
                <div class="table-responsive" id="items" style="display: none;">
                    {{--                    <h4>Ordered Products List</h4>--}}
                    <table id="items_table" style="width: 100%" class="table nowrap table-striped table-hover"></table>
                    <div style="margin-right:6%;  margin-top: 2%; float: right;">
                        <button class="btn btn-sm btn-danger btn-rounded" onclick="return false" id="cancel">Back
                        </button>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6"></div>
                    <div class="col-md-3" style="margin-left: 2.5%" id="dates_1">
                        <label style="margin-left: 80%" for="filter" class="col-form-label text-md-right">Date:</label>
                    </div>
                    <div class="col-md-3" id="dates" style="margin-left: -3.4%">
                        <input style="width: 103.4%;" type="text" autocomplete="off" class="form-control"
                               id="daterange"/>
                    </div>

                </div>

                <div class="table-responsive" id="purchases">

                    <table id="fixed-header-2" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>id</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="item-receive" role="tabpanel"
                 aria-labelledby="quotes_list-tab">
                <form name="item" id="myFormId">
                    @csrf()
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="code">Supplier Name <font color="red">*</font></label>
                                <select name="supplier" class="js-example-basic-single form-control"
                                        id="supplier_ids" required="true" onchange="filterInvoiceBySupplier()">
                                    <option selected="true" value="" disabled="disabled">Select Supplier...</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Products <font color="red">*</font></label>
                                <select id="selected-product" class="js-example-basic-single form-control">
                                    <option selected="true" value="" disabled="disabled">Select Product...</option>
                                    @foreach($current_stock as $stock)
                                        <option
                                            value="{{$stock['product_name'].'#@'.$stock['product_id'].'#@'.$stock['unit_cost']}}">{{$stock['product_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                @if($invoice_setting === 'YES')
                                    <label for="code">Invoice # <font color="red">*</font></label>
                                    <select name="invoice_no" class="form-control js-example-basic-single"
                                            id="invoice_id" required>
                                        <option selected="true" value="" disabled="disabled">Select Invoice..</option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{$invoice->id}}">{{$invoice->invoice_no}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <label for="code">Invoice #</label>
                                    <select name="invoice_no" class="form-control js-example-basic-single"
                                            id="invoice_id">
                                        <option selected="true" value="" disabled="disabled">Select Invoice..</option>
                                        @foreach($invoices as $invoice)
                                            <option value="{{$invoice->id}}">{{$invoice->invoice_no}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                @if($batch_setting === 'YES')
                                    <label for="code">Batch # <font color="red">*</font></label>
                                    <input type="text" name="batch_number" class="form-control" id="batch_n"
                                           required="true" value="{{session('batch_number')}}"/>
                                @else
                                    <label for="code">Batch #</label>
                                    <input type="text" name="batch_number" class="form-control" id="batch_n"
                                           value="{{session('batch_number')}}"/>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row" id="detail">
                        <hr>
                        <div class="table teble responsive" style="width: 100%;">
                            <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        @if($expire_date === "YES")
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 10px">
                                    <div style="width: 99%">
                                        <label for="price_category">Price Category <font color="red">*</font></label>
                                        <select name="price_category" class="form-control js-example-basic-single"
                                                id="price_category" required="true" onchange="priceByCategory()">
                                            @foreach($price_categories as $price_category)
                                                <option
                                                    value="{{$price_category->id}}">{{$price_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 10px">
                                    <label for="code">Buy Price<font color="red">*</font></label>
                                    <input type="text" id="buy_price" name="unit_cost" class="form-control" min="0"
                                           value="0" required="true" onchange="amountCheck()"
                                           onkeypress="return isNumberKey(event,this)">
                                    <span class="help-inline"></span>
                                    <div class="text text-danger" class="price_error"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 10px">
                                    <label for="code">Sell Price<font color="red">*</font></label>
                                    <input type="text" name="sell_price" class="form-control" min="0" value="0"
                                           required="true" id="sell_price_id" onchange="amountCheck()"
                                           onkeypress="return isNumberKey(event,this)">
                                    <span class="help-inline"></span>
                                    <div class="amount_error text text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 10px">
                                    <label>Expire Date <font color="red">*</font></label>
                                    <input type="text" name="expire_date" class="form-control" id="expire_date_21"
                                           autocomplete="off" required="true">

                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="expire_check"
                                               style="padding:10px" value="true" onchange="findselected()">
                                        <label class="form-check-label" for="expire_check">No Expire Date</label>
                                    </div>
                                </div>

                            </div>
                        @else
                            <div class="col-md-4">
                                <div class="form-group" style="padding-top: 10px">
                                    <div style="width: 99%">
                                        <label for="price_category">Price Category <font color="red">*</font></label>
                                        <select name="price_category" class="form-control js-example-basic-single"
                                                id="price_category" required="true" onchange="priceByCategory()">
                                            @foreach($price_categories as $price_category)
                                                <option
                                                    value="{{$price_category->id}}">{{$price_category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="padding-top: 10px">
                                    <label for="code">Buy Price<font color="red">*</font></label>
                                    <input type="text" id="buy_price" name="unit_cost" class="form-control" min="0"
                                           value="0" required="true" onchange="amountCheck()"
                                           onkeypress="return isNumberKey(event,this)">
                                    <span class="help-inline"></span>
                                    <div class="text text-danger" class="price_error"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" style="padding-top: 10px">
                                    <label for="code">Sell Price<font color="red">*</font></label>
                                    <input type="text" name="sell_price" class="form-control" min="0" value="0"
                                           required="true" id="sell_price_id" onchange="amountCheck()"
                                           onkeypress="return isNumberKey(event,this)">
                                    <span class="help-inline"></span>
                                    <div class="amount_error text text-danger"></div>
                                </div>
                            </div>
                        @endif

                    </div>

                    @if($back_date=="YES")
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 10px">
                                    <label>Purchase Date <font color="red">*</font></label>
                                    <input type="text" name="purchase_date" class="form-control" id="purchase_date"
                                           autocomplete="off" readonly required="true">
                                </div>
                            </div>
                        </div>
                    @endif

                    <input type="hidden" id="received_cart" name="cart">
                    <input type="hidden" name="" id="sell">
                    <input type="hidden" name="" id="buy">
                    <input type="hidden" name="batch_setting" id="batch_setting" value="{{$batch_setting}}">
                    <input type="hidden" name="invoice_setting" id="invoice_setting" value="{{$invoice_setting}}">
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-danger" id="cancel-all" onclick="resetForms()">
                                    Clear
                                </button>
                                <button id="save_id"
                                        class="btn btn-primary">Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
        </div>
    </div>

    @include('purchases.goods_receiving.receive')
    @include('purchases.goods_receiving.editselectedproduct')
@endsection
@push("page_scripts")
    @include('partials.notification')

    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

    <script>

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var datas = @json($orders);

        let order_id = localStorage.getItem("order_id");

        datas = datas.filter(function (data) {
            return data.id == order_id;
        });

        if (datas.length != 0) {
            localStorage.setItem("items", JSON.stringify(datas[0].details));
        }
        // console.log(datas[0].details);

        var config = {
            routes: {
                goodsreceiving: '{{route('receiving-price-category')}}',
                invoiceItemPrice: '{{route('receiving-item-prices')}}',
                filterBySupplier: '{{route('filter-invoice')}}',
                filterPrice: '{{route('filter-price')}}',
                itemFormSave: '{{route('goods-receiving.itemReceive')}}',
                orderFormSave: '{{route('goods-receiving.orderReceive')}}',
                invoiceFormSave: '{{route('goods-receiving.invoiceitemReceive')}}',
                goodReceivingFilterInvoiceBySupplier: '{{route('filter-invoice')}}',

            }
        };

        $(document).ready(function () {
            resetForms();
        });

        function resetForms() {
            invoicecart_table.clear();
            invoicecart_table.draw();
            invoice_cart = [];
            invoice_cart_receiveds = [];
            try {
                document.getElementById('myFormId').reset();
                document.getElementById("selected-product").value = '';
                document.getElementById("invoicing_batch_n").value = '';
                document.getElementById("invoicing_purchase_date").value = '';
                document.getElementById("total_selling_price").value = '0.00';
                document.getElementById("total_buying_price").value = '0.00';
                document.getElementById("sub_total").value = '0.00';
            } catch (e) {
                // console.log(e)
             }
            
            // $('#store_id').val('').change();
            $('#supplier_ids').val('').change();
            // $('#good_receiving_supplier_ids').val('').change();
            $('#invoice_id').val('').change();
        }

        var a = 1;

        function findselected() {

            a = -a;
            if (a < 1) {
                document.getElementById("expire_date_21").setAttribute('disabled', false);
            } else {
                document.getElementById("expire_date_21").removeAttribute('disabled');
            }
        }

        var b = 1;

        function findchecked() {
            b = -b;
            if (b < 1) {
                document.getElementById("expire_date_1").setAttribute('disabled', false);
            } else {
                document.getElementById("expire_date_1").removeAttribute('disabled');
                $('#expire_date_1').prop('readonly', true);
            }
        }

        function invoiceamountCheck() {

            var unit_price = document.getElementById('edit_buying_price').value;
            var sell_price = document.getElementById('edit_selling_price').value;
            var unit_price_parse = (parseFloat(unit_price.replace(/\,/g, ''), 10));
            var sell_price_parse = (parseFloat(sell_price.replace(/\,/g, ''), 10));

            document.getElementById('edit_selling_price').value = formatMoney(parseFloat(sell_price.replace(/\,/g, ''), 10));
            document.getElementById('edit_buying_price').value = formatMoney(parseFloat(unit_price.replace(/\,/g, ''), 10));

            if (Number(sell_price_parse) < Number(unit_price_parse) && Number(sell_price_parse) !== Number(0)
                && Number(unit_price_parse) !== Number(0)) {

                $('#invoicesave_id').prop('disabled', true);
                notify('Cannot be less than Buy Price', 'top', 'right', 'warning');
            } else if (Number(sell_price_parse) === Number(unit_price_parse)) {
                // $('#invoicesave_id').prop('disabled', true);
                // notify('Cannot be equal to Buy Price', 'top', 'right', 'warning');
            } else {

                $('#invoicesave_id').prop('disabled', false);
                $('div.amount_error').text('');

            }

        }
        function amountCheck() {

            var unit_price = document.getElementById('buy_price').value;
            var sell_price = document.getElementById('sell_price_id').value;
            var unit_price_parse = (parseFloat(unit_price.replace(/\,/g, ''), 10));
            var sell_price_parse = (parseFloat(sell_price.replace(/\,/g, ''), 10));

            document.getElementById('sell_price_id').value = formatMoney(parseFloat(sell_price.replace(/\,/g, ''), 10));
            document.getElementById('buy_price').value = formatMoney(parseFloat(unit_price.replace(/\,/g, ''), 10));

            if (Number(sell_price_parse) < Number(unit_price_parse) && Number(sell_price_parse) !== Number(0)
                && Number(unit_price_parse) !== Number(0)) {

                $('#save_id').prop('disabled', true);
                $('div.amount_error').text('Cannot be less than Buy Price');
            } else if (Number(sell_price_parse) === Number(unit_price_parse)) {
                $('#save_id').prop('disabled', true);
                $('div.amount_error').text('Cannot be equal to Buy Price');
            } else {

                $('#save_id').prop('disabled', false);
                $('div.amount_error').text('');

            }
        }

        function isNumberKey(evt, obj) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            var value = obj.value;
            var dotcontains = value.indexOf(".") !== -1;
            if (dotcontains)
                if (charCode === 46) return false;
            if (charCode === 46) return true;
            return !(charCode > 31 && (charCode < 48 || charCode > 57));

        }

        $(function () {
            var start = moment();
            var end = moment();

            $('#expire_date_21').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: start,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#expire_date_1').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: start,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#purchase_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        $(function () {
            var start = moment();
            var end = moment();

            $('#invoicing_purchase_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD-M-YYYY'
                }
            });
        });

        // $("datetimepicker2").datepicker({ changeYear: true, dateFormat: 'dd/mm/yy', showOn: 'none', showButtonPanel: true,  minDate:'0d' });

        //Expire Date
        $('input[name="expire_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY'));
        });

        $('input[name="expire_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        //Purchase Date
        $('input[name="purchase_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('input[name="purchase_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        //Invoice Expire Date
        $('input[name="edit_expire_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY'));
        });

        $('input[name="edit_expire_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        //invoicing_purchase_date
        $('input[name="invoicing_purchase_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('input[name="invoicing_purchase_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        $('input[name="invoicing_purchase_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY'));
        });

        $('input[name="invoicing_purchase_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        $('#invoice_ids').select2({
            dropdownParent: $("#receive")
        });

    </script>

    <script src="{{asset("assets/apotek/js/goods-receiving.js")}}"></script>
    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>

@endpush
