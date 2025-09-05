@extends("layouts.master")

@section('content-title')
    Sales Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Edit Sales Order</a></li>
@endsection


@section('content')
    <style>
        .ms-container {
            background: transparent url('../assets/plugins/multi-select/img/switch.png') no-repeat 50% 50%;
            width: 100%;
        }

        .ms-selectable,
        .ms-selection {
            background: #fff;
            color: #555555;
            float: left;
            width: 45%;
        }

        /* Chrome, Safari, Edge, Opera */
        #edit_sales_order input[type=number]::-webkit-outer-spin-button,
        #edit_sales_order input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        #edit_sales_order input[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        /* Optional: ensure input fits cell and looks tidy */
        #edit_sales_order input[type=number] {
            width: 100%;
            box-sizing: border-box;
            padding: 4px 6px;
            height: 36px;
        }

    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="new-order" href="{{ route('sale-quotes.index') }}" aria-controls="pills-home"
                    aria-selected="false">New Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="edit-order" href="#" aria-controls="pills-edit" aria-selected="true">Edit
                    Order</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="order-list" href="{{ route('sale-quotes.order_list') }}"
                    aria-controls="pills-profile" aria-selected="false">Order List</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <form id="quote_sale_form">
                    @if (auth()->user()->checkPermission('Manage Customers'))
                        <div class="row">
                            <div class="col-md-12">
                                <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                                    data-toggle="modal" data-target="#create"> Add
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
                                <select id="price_category" class="js-example-basic-single form-control">
                                    <option value="">Select Sales Type</option>
                                    @foreach ($price_category as $price)
                                        <!-- <option value="{{ $price->id }}">{{ $price->name }}</option> -->
                                        <option value="{{ $price->id }}" {{ $default_sale_type === $price->id ? 'selected' : '' }}>{{ $price->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                                <label for="code">Customer Name <font color="red">*</font></label>
                                <select id="customer_id" name="customer_id" class="js-example-basic-single form-control"
                                    required>
                                    <option value="" disabled selected="true">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ ($customer->id == $customer_id ? "selected" : "") }}>
                                            {{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="detail">
                        <hr>
                        <div class="table-responsive" style="width: 100%;">
                            <table id="edit_sales_order" class="table nowrap table-striped table-hover dataTable no-footer"
                                width="100%" role="grid" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>VAT</th>
                                        <th>Amount</th>
                                        <th hidden>Discount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <input type="hidden" name="" id="quoted_id" value="{{ $quote_id }}">
                                <input type="hidden" name="" id="sales_details" value="{{ $sales_details->count() }}">
                                <tbody>
                                    @foreach($sales_details as $saleData)
                                        <tr data-id="{{ $saleData->id }}">
                                            <td>{{ $saleData->name }}</td>
                                            <td class="quantity">{{ $saleData->quantity }}</td>
                                            <td class="price">{{ number_format($saleData->price, 0) }}</td>
                                            <td>{{ number_format($saleData->vat, 0) }}</td>
                                            <td class="amount">{{ number_format($saleData->amount, 0) }}</td>
                                            <td hidden>{{ number_format($saleData->discount, 0) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm btn-edit btn-rounded">Edit</button>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete btn-rounded" data-quote-id="{{ $saleData->quote_id }}" data-quote-item-id="{{ $saleData->id }}">Delete</button>
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            @if ($enable_discount === 'YES')
                                <div style="width: 99%">
                                    <label>Discount</label>
                                    <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                        value="{{ number_format($discount, 2) ?? '0.00' }}" />
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4">

                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Sub Total:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="sub_total" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($sub_total, 2) ?? '0.0' }}" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_vat" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($vat, 2) ?? '0.0' }}" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total
                                        Amount:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                        value="{{ number_format($total, 2) ?? '0.0' }}" />
                                </div>
                                <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                    id="discount_error">Invalid Amount</span>
                            </div>
                        </div>


                        <input type="hidden" value="{{ $vat }}" id="vat">
                        <input type="hidden" value="0.00" id="sale_paid">
                        <input type="hidden" value="Yes" id="quotes_page">
                        <input type="hidden" value="0.00" id="change_amount">
                        <input type="hidden" id="price_cat" name="price_category_id">
                        <input type="hidden" id="discount_value" name="discount_amount">
                        <input type="hidden" id="order_cart" name="cart">
                        <input type="hidden" value="" id="fixed_price">

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="{{$quote_id}}" id="quote_id">
                        <input type="hidden" value="{{$customer_id}}" id="customer_id">
                        <input type="hidden" value="" id="print">
                        <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">

                    </div>
                    <hr>
                    <div class="row" id="save_buttons">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <a href="{{ url('sales/sales-order-list') }}" class="btn btn-danger">Back</a>
                                <button data-id="{{$quote_id}}" data-customer="{{$customer_id}}" class="btn btn-primary btn-save"
                                    id="save_order_changes" data-target="convert_to_sales">Save</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    {{-- @include('sales.sale_quotes.modal.update') --}}
    @include('sales.customers.create')

@endsection

@push('page_scripts')
    @include('partials.notification')
    <script type="text/javascript">
        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                changePriceCategory: '{{ route('change-price-category') }}',
                changeCustomer: '{{ route('change-quote-customer') }}',
                addQuoteItem: '{{ route('add-qoute-item') }}',
                selectProducts: '{{ route('selectProducts') }}',
                storeQuote: '{{ route('storeQuote') }}',
                filterProductByWord: '{{ route('filter-product-by-word') }}'
            }
        };
        
    var cartData = parseInt(document.getElementById("sales_details").value, 10) || 0;
    var priceSelect = document.getElementById("price_category");
    if (cartData > 0) {
        priceSelect.disabled = true;
    } else {
        priceSelect.disabled = false;
    }

        // helper functions
        function unformatNumber(str) {
            if (str === undefined || str === null) return 0;
            // remove commas and spaces, replace non-breaking spaces too
            var s = String(str).replace(/\u00A0/g, '').replace(/,/g, '').trim();
            return s === '' ? 0 : parseFloat(s);
        }
        function formatNumber(num, fractionDigits = 0) {
            if (isNaN(num)) num = 0;
            return Number(num).toLocaleString(undefined, { minimumFractionDigits: fractionDigits, maximumFractionDigits: fractionDigits });
        }

                
        $(document).ready(function() {

            // Toggle Edit Mode (Edit <-> Close)
            $('#edit_sales_order').on('click', '.btn-edit', function(e) {
                var tr = $(this).closest('tr');

                if (tr.hasClass('editing')) {
                    var qInput = tr.find('td.quantity input');
                    var pInput = tr.find('td.price input');

                    if (qInput.length && pInput.length) {
                        var qVal = unformatNumber(qInput.val());
                        var pVal = unformatNumber(pInput.val());

                        tr.find('td.quantity').text(formatNumber(qVal, 0));
                        tr.find('td.price').text(formatNumber(pVal, 0));
                        var newAmount = qVal * pVal;
                        tr.find('td.amount').text(formatNumber(newAmount, 0));
                    }

                    tr.removeClass('editing');
                    $(this).text('Edit').removeClass('btn-close').addClass('btn-edit');
                    return;
                }

                // Enter edit mode
                tr.addClass('editing');

                // get current displayed text and unformat to raw numbers for input values
                var quantityText = tr.find('td.quantity').text().trim();
                var priceText = tr.find('td.price').text().trim();

                var quantityRaw = unformatNumber(quantityText);
                var priceRaw = unformatNumber(priceText);

                // Replace cells with inputs
                tr.find('td.quantity').html('<input type="number" min="0" step="1" class="form-control input-quantity" value="' + quantityRaw + '" />');
                tr.find('td.price').html('<input type="number" min="0" step="0.01" class="form-control input-price" value="' + priceRaw + '" />');

                // focus on quantity
                tr.find('td.quantity input').focus();

                // change button label to Close
                $(this).text('Close').removeClass('btn-edit').addClass('btn-close');
            });

            // When input loses focus (blur) update amount immediately and send AJAX
            $('#edit_sales_order').on('blur', 'td.quantity input, td.price input', function(e) {
                var tr = $(this).closest('tr');
                // if row is not in editing mode, ignore
                if (!tr.hasClass('editing')) return;

                // get raw numeric values
                var qVal = unformatNumber(tr.find('td.quantity input').val());
                var pVal = unformatNumber(tr.find('td.price input').val());

                // update amount on UI (formatted)
                var newAmount = qVal * pVal;
                tr.find('td.amount').text(formatNumber(newAmount, 0));

                // prepare data to send to server (unformatted numbers)
                var id = tr.data('id');
                var _token = $('meta[name="csrf-token"]').attr('content');
                var quote_id = document.getElementById('quoted_id').value;

                $.ajax({
                    url: '{{ route("update-qoute-item") }}',
                    method: 'POST',
                    data: {
                        _token: config.token,
                        id: id,
                        quote_id: quote_id,
                        quantity: qVal,
                        price: pVal
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            console.log('response', response);
                            // notify(response.message, 'top', 'right', 'success');
                            document.getElementById('sub_total').value = formatNumber(Number(response.data.sub_total), 2);
                            document.getElementById('total_vat').value = formatNumber(Number(response.data.vat), 2);
                            document.getElementById('total').value = formatNumber(Number(response.data.total), 2);
                        }
                    },
                    error: function(xhr) {
                        // notify('Update failed', 'top', 'right', 'danger');
                    }
                });
            });

            // Click outside row closes editing (and restores formatted text)
            $(document).on('click', function(e) {
                $('#edit_sales_order tr.editing').each(function() {
                    var tr = $(this);
                    if (!$(e.target).closest(tr).length) {
                        var qInput = tr.find('td.quantity input');
                        var pInput = tr.find('td.price input');

                        if (qInput.length && pInput.length) {
                            var qVal = unformatNumber(qInput.val());
                            var pVal = unformatNumber(pInput.val());

                            tr.find('td.quantity').text(formatNumber(qVal, 0));
                            tr.find('td.price').text(formatNumber(pVal, 0));
                            tr.find('td.amount').text(formatNumber(qVal * pVal, 0));
                        }

                        tr.removeClass('editing');
                        tr.find('.btn-close').text('Edit').removeClass('btn-close').addClass('btn-edit');
                    }
                });
            });

            //When Delete Button Clicked
            $('#edit_sales_order').on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var id = $(this).data('quote-item-id');
                var quoteId = $(this).data('quote-id')
                $.ajax({
                    url: '{{ route("delete-qoute-item") }}',
                    method: 'POST',
                    data: {
                        _token: config.token,
                        id: id,
                        quote_id: quoteId
                    },
                    success: function(response) {
                        refreshSalesTable(response.data);
                        isCartEmpty(response.data.sales_details.length);
                        // console.log('response', response);
                    },
                    error: function(xhr) {
                        notify('Failed', 'top', 'right', 'danger');
                    }
                });
            })

            $('#save_buttons').on('click', '.btn-save', function(e) {
                var discount = document.getElementById('sale_discount').value;
                var quoteId = $(this).data('id');
                $.ajax({
                    url: '{{ route("save-final-qoute") }}',
                    method: 'POST',
                    data: {
                        _token: config.token,
                        id: quoteId,
                        discount: discount
                    },
                    success: function(response) {
                        // console.log('response', response);
                        if (response.status === 'success') {
                            notify(response.message, 'top', 'right', 'success');
                            window.location.href = response.redirect;
                        }else{
                            notify(response.message, 'top', 'right', 'danger');
                        }
                    },
                    error: function(xhr) {
                        notify('An error occured!', 'top', 'right', 'danger');
                    }
                });
            })
        });

        function backToOrders() {
            window.location.href = "{{ route('sale-quotes.index') }}";  // Replace 'orders' with your route name
        }

    </script>
    <script src="{{ asset('assets/apotek/js/notification.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/edit_quote.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/customer.js') }}"></script>


@endpush