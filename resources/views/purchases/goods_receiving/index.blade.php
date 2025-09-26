@extends("layouts.master")

@section('content-title')

    Goods Receiving

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Goods Receiving</a></li>
@endsection
@section("content")

    <style>
        .datepicker>.datepicker-days {
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

        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
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
                <a class="nav-link active text-uppercase" id="invoice-received" data-toggle="pill"
                    href="{{ route('goods-receiving.index') }}" role="tab" aria-controls="quotes_list"
                    aria-selected="true">Invoice Receiving</a>
            </li>
            @if (auth()->user()->checkPermission('View Order Receiving'))
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="order-received" data-toggle="pill"
                    href="{{ route('orders-receiving.index') }}" role="tab" aria-controls="new_quotes"
                    aria-selected="false">Order Receiving
                </a>
            </li>
            @endif
            @if (auth()->user()->checkPermission('View Material Received'))
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="material-received" data-toggle="pill"
                    href="{{ url('purchases/material-received') }}" role="tab" aria-controls="new_quotes"
                    aria-selected="false">Material Received
                </a>
            </li>
            @endif
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
                                    id="good_receiving_supplier_ids" required="true"
                                    onchange="goodReceivingFilterInvoiceBySupplier()">
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
                                <select id="invoiceselected-product" class="js-example-basic-single form-control" style="width: 100%">
                                    <option selected="true" value="" disabled="disabled">Select Product...</option>
                                    @foreach($current_stock as $stock)
                                        <option
                                            value="{{ 
                                                $stock['product_name'] . '#@' . 
                                                $stock['product_id'] . '#@' . 
                                                $stock['brand'] . '#@' . 
                                                $stock['pack_size'] . '#@' . 
                                                $stock['unit_cost'] . '#@' . 
                                                $stock['sales_uom']
                                            }}"
                                            data-brand="{{ $stock['brand'] ?? '' }}"
                                            data-pack="{{ $stock['pack_size'] ?? '' }}"
                                        >
                                            {{ $stock['product_name'] . ' ' . $stock['brand'] . ' ' . $stock['pack_size'] . $stock['sales_uom'] }}
                                        </option>
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
                                    @foreach($invoices as $invoice)
                                        <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }} - {{ optional($invoice->supplier)->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <label for="code">Invoice #</label>
                                <select name="invoice_no" class="form-control js-example-basic-single"
                                    id="goodreceving_invoice_id">
                                    <option selected="true" value="" disabled="disabled">Select Invoice..</option>
                                    @foreach($invoices->sortByDesc('id') as $invoice)
                                        <option value="{{ $invoice->id }}">
                                            {{ $invoice->invoice_no }} - {{ optional($invoice->supplier)->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                @if($batch_setting === 'YES')
                                    <label for="code">Batch # <font color="red">*</font></label>
                                    <input type="text" name="batch_number" class="form-control" id="invoicing_batch_n" autocomplete="off"
                                        required="true" value="{{session('batch_number')}}" />
                                @else
                                    
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row" id="detail">
                        <hr>
                        <div class="table teble responsive" style="width: 100%;">
                            <table id="invoicecart_table" class="table nowrap table-striped table-hover" width="100%">
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group" style="padding-top: 10px">
                                <div style="width: 99%">
                                    <label for="invoiceprice_category">Price Category <font color="red">*</font></label>
                                    <select name="price_category" class="form-control" id="invoiceprice_category"
                                        required="true" onchange="priceByCategory()">
                                        @foreach($price_categories as $price_category)
                                            <option value="{{$price_category->id}}">{{$price_category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            @if($back_date == "YES")
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
                                    <input type="text" id="total_buying_price" class="form-control-plaintext text-md-right"
                                        readonly value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Sell:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_selling_price" class="form-control-plaintext text-md-right"
                                        readonly value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total Profit:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <input type="hidden" id="invoice_received_cart" name="cart">
                    <input type="hidden" name="store" id="store_id" value="{{$default_store_id}}">
                    <input type="hidden" name="expire_date" id="expire_date_enabler" value="{{$expire_date}}">
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
                                <button id="invoicesave_id" class="btn btn-primary">Save
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
            vals: {
                expire_date: @json($expire_date)
            },
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
            // Clear invoice table
            invoicecart_table.clear();
            invoicecart_table.draw();

            // Reset cart arrays
            invoice_cart = [];
            invoice_cart_receiveds = [];

            try {
                // Reset form fields except product & batch #
                document.getElementById('myFormId').reset();

                // Keep selected product untouched
                // document.getElementById("selected-product").value = '';

                // Preserve batch number
                document.getElementById("invoicing_batch_n").value = "{{session('batch_number')}}";

                // Clear dates & totals
                document.getElementById("invoicing_purchase_date").value = '';
                document.getElementById("total_selling_price").value = '0.00';
                document.getElementById("total_buying_price").value = '0.00';
                document.getElementById("sub_total").value = '0.00';
            } catch (e) {
                console.error(e);
            }

            // Reset select elements properly
            $('#good_receiving_supplier_ids').val('').trigger('change');
            $('#goodreceving_invoice_id').val('').trigger('change');
            
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
                    format: 'YYYY-MM-DD'
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
                    format: 'YYYY-MM-DD'
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
                    format: 'YYYY-MM-DD'
                }
            });
        });

        $(function () {
            $('#invoicing_purchase_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                maxDate: moment(), // ✅ prevents choosing tomorrow or future dates
                locale: {
                    format: 'YYYY-MM-DD'
                },
                drops: "up"
            });
        });


        $(function () {
            var start = moment();
            var end = moment();

            $('#edit_expire_date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minDate: +0,
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });

        // $("datetimepicker2").datepicker({ changeYear: true, dateFormat: 'dd/mm/yy', showOn: 'none', showButtonPanel: true,  minDate:'0d' });

        //Expire Date
        $('input[name="expire_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD'));
        });

        $('input[name="expire_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        //Purchase Date
        $('input[name="purchase_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        $('input[name="purchase_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });


        //invoicing_purchase_date
        $('input[name="invoicing_purchase_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD'));
        });

        $('input[name="invoicing_purchase_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

        $('input[name="edit_expire_date"]').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY/MM/DD'));
        });

        $('input[name="edit_expire_date"]').on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });


        $('#invoice_ids').select2({
            dropdownParent: $("#receive")
        });

    </script>

    <script src="{{asset("assets/apotek/js/notification.js")}}"></script>
    <script src="{{asset("assets/apotek/js/goods-receiving.js")}}"></script>
    <script src="{{asset("assets/plugins/bootstrap-datetimepicker/js/bootstrap-datepicker.min.js")}}"></script>
    <script src="{{asset("assets/js/pages/ac-datepicker.js")}}"></script>

   <script>
    // Step 1: Store all invoices in a JS array
    const allInvoices = @json($invoices->map(function($inv) {
        return [
            'id' => $inv->id,
            'invoice_no' => $inv->invoice_no,
            'supplier_id' => $inv->supplier_id
        ];
    }));

    // Step 2: Function to filter invoices when supplier changes
    function goodReceivingFilterInvoiceBySupplier() {
        const supplierSelect = document.getElementById('good_receiving_supplier_ids');
        const invoiceSelect = document.getElementById('goodreceving_invoice_id');
        const supplierId = supplierSelect.value;

        // Clear current invoice options
        invoiceSelect.innerHTML = '<option selected disabled>Select Invoice...</option>';

        // Filter invoices based on selected supplier
        const filteredInvoices = allInvoices.filter(inv => inv.supplier_id == supplierId);

         // Sort descending by ID (latest first)
        filteredInvoices.sort((a, b) => b.id - a.id);
        
        // Populate invoice dropdown (only invoice_no now)
        filteredInvoices.forEach(inv => {
            const option = document.createElement('option');
            option.value = inv.id;
            option.text = inv.invoice_no; // <-- removed the supplier name
            invoiceSelect.appendChild(option);
        });

        // Reset Select2 display
        $(invoiceSelect).val('').trigger('change');
    }

    // Step 3: Initialize Select2 for invoice dropdown
    $(document).ready(function () {
        $('#goodreceving_invoice_id').select2({
            width: '100%',
            placeholder: 'Select Invoice...'
        });

        $('#good_receiving_supplier_ids').select2({
            width: '100%',
            placeholder: 'Select Supplier...'
        });
    });
</script>



    <script>
        $(document).ready(function () {
            // Listen for the click event on the Transfer History tab
            $('#material-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#order-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#invoice-received').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

        });
    </script>

@endpush