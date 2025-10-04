@extends("layouts.master")

@section('content-title')
    Sales Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Sales / Sales Order</a></li>
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

        #input_products_b {
            position: absolute;
            opacity: 0;
            z-index: 1;
        }
    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            @if(auth()->user()->checkPermission('View Sales Orders'))
                <li class="nav-item">
                    <a class="nav-link" id="new-order" data-toggle="pill" href="{{ route('sale-quotes.index') }}" role="tab"
                        aria-controls="pills-home" aria-selected="true">New Order</a>
                </li>
            @endif
            @if(auth()->user()->checkPermission('View Order List'))
                <li class="nav-item">
                    <a class="nav-link active" id="order-list" data-toggle="pill" href="{{ route('sale-quotes.order_list') }}"
                        role="tab" aria-controls="pills-profile" aria-selected="false">Order List</a>
                </li>
            @endif
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
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
                                <label id="cat_label">Sales Type</label>
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
                                <label>Products</label>
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
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="detail">
                        <hr>
                        <div class="table-responsive" style="width: 100%;">
                            <table id="cart_table" class="table nowrap table-striped table-hover" width="100%"></table>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            @if ($enable_discount === 'YES')
                                <div style="width: 99%">
                                    <label>Discount</label>
                                    <input type="text" onchange="discount()" id="sale_discount" class="form-control"
                                        value="0" />
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
                                        value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>VAT:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total_vat" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-6 col-form-label text-md-right"><b>Total
                                        Amount:</b></label>
                                <div class="col-md-6" style="display: flex; justify-content: flex-end">
                                    <input type="text" id="total" class="form-control-plaintext text-md-right" readonly
                                        value="0.00" />
                                </div>
                                <span class="help-inline text text-danger" style="display: none; margin-left: 63%"
                                    id="discount_error">Invalid Amount</span>
                            </div>
                        </div>


                        <input type="hidden" value="{{ $vat }}" id="vat">
                        <input type="hidden" value="0" id="sale_paid">
                        <input type="hidden" value="Yes" id="quotes_page">
                        <input type="hidden" value="0" id="change_amount">
                        <input type="hidden" id="price_cat" name="price_category_id">
                        <input type="hidden" id="discount_value" name="discount_amount">
                        <input type="hidden" id="order_cart" name="cart">
                        <input type="hidden" value="" id="fixed_price">

                        <input type="hidden" value="" id="category">
                        <input type="hidden" value="" id="customers">
                        <input type="hidden" value="" id="print">
                        <input type="hidden" value="{{ $enable_discount }}" id="enable_discount">

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remark" id="remark" class="form-control"></textarea>
                            </div>

                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="btn-group" style="float: right;">
                                <button type="button" class="btn btn-danger" id="deselect-all-quote">Cancel</button>
                                <button id="save_btn" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

                <div class="d-flex justify-content-end mb-2 align-items-center">
                    <label class="mr-2" for="">Date:</label>
                    <input type="text" id="date_range" class="form-control w-auto" onchange="getQuotes()">
                </div>
                <div class="table-responsive" id="sales">
                    <table id="sale_quotes-Table" class="display table nowrap table-striped table-hover"
                        style="width:100%; font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Order#</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                                {{-- <th>Sale Type</th>--}}
                                {{-- <th>VAT</th>--}}
                                {{-- <th>Discount</th>--}}
                                {{-- <th>Amount</th>--}}
                                {{-- <th>id</th>--}}
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('sales.sale_quotes.details')
    @include('sales.customers.create')
@endsection

@include('partials.notification')

@push('page_scripts')
    <script type="text/javascript">
        // Clear invalid localStorage entries immediately
        (function () {
            var lastPill = localStorage.getItem('lastPill');
            if (lastPill && (lastPill.includes('http') || !lastPill.startsWith('#'))) {
                localStorage.removeItem('lastPill');
                console.log('Cleared invalid localStorage entry:', lastPill);
            }
        })();

        $(document).ready(function () {
            $('#new-order').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

            $('#order-list').on('click', function (e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });
        });

        var page_no = 1; //sales page
        var normal_search = 0; //search by word

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let quotes_table = null;

        var config = {
            token: '{{ csrf_token() }}',
            routes: {
                selectProducts: '{{ route('selectProducts') }}',
                storeQuote: '{{ route('storeQuote') }}',
                filterProductByWord: '{{ route('filter-product-by-word') }}'
            }
        };

        /*normal product search box*/
        $('#products').on('select2:open', function (e) {
            // select2 is opened, handle event
            normal_search = 1;
        });
        $('#products').on('select2:close', function (e) {
            // select2 is opened, handle event
            normal_search = 0;
        });

        /*hide barcode search*/
        $.fn.toggleSelect2 = function (state) {
            return this.each(function () {
                $.fn[state ? 'show' : 'hide'].apply($(this).next('.select2-container'));
            });
        };

        $(document).ready(function () {

            var sale_type_id = localStorage.getItem('sale_type');
            $('#products_b').toggleSelect2(false);

            if (sale_type_id) {
                $('#products_b').select2('close');
                setTimeout(function () {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            }

            $('#price_category').on('change', function () {
                setTimeout(function () {
                    $('input[name="input_products_b"]').focus()
                }, 30);
            });

        });

        $('#customer_id').on('change', function () {
            setTimeout(function () {
                $('input[name="input_products_b"]').focus()
            }, 30);
        });

        //setup before functions
        var typingTimer; //timer identifier
        var doneTypingInterval = 500; //time in ms (5 seconds)

        //on keyup, start the countdown
        $('#input_products_b').keyup(function () {
            clearTimeout(typingTimer);
            if ($('#input_products_b').val()) {
                typingTimer = setTimeout(doneTyping, doneTypingInterval);
            }
        });

        function doneTyping() {
            $("#products_b").data('select2').$dropdown.find("input").val(document.getElementById('input_products_b').value)
                .trigger('keyup');
            $('#products_b').select2('close');
            document.getElementById('input_products_b').value = '';
        }

        function getQuotes() {
            $.ajax({
                url: "{{ route('sale-quotes.get-quotes') }}",
                dataType: "json",
                data: {
                    date: $('#date_range').val()
                },
                type: 'GET',
                success: function (data) {
                    // console.log('QuotesData', data);
                    quotes_table.clear();
                    quotes_table.rows.add(data)
                    quotes_table.draw();
                }
            });
        }

        $(document).ready(function () {

            $(function () {

                var start = moment();
                var end = moment();

                function cb(start, end) {
                    $('#daterange').val(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
                }

                $('#date_range').daterangepicker({
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
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment()
                            .subtract(1, 'month').endOf('month')
                        ],
                        'This Year': [moment().startOf('year'), moment()]
                    }
                }, cb);

                cb(start, end);

            });

            quotes_table = $('#sale_quotes-Table').DataTable({
                columns: [
                    {
                        data: 'quote_number',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'customer.name',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'date',
                        render: function (date) {
                            return date ? moment(date).format('YYYY-MM-DD') : '';
                        }
                    },
                    {
                        data: 'cost.amount',
                        render: function (data) {
                            return formatMoney(data);
                        }
                    }, {
                        data: null,
                        render: function (data, type, row) {
                            let receipt_url = '{{ route('receiptReprint', 'receipt_id') }}'.replace('receipt_id', row.id);
                            let update_url = '{{ route('updateSale', 'receipt_id') }}'.replace('receipt_id', row.id);

                            let buttons = ``;

                            buttons += `
                                                                                                    <button class="btn btn-sm btn-rounded btn-success" type="button"
                                                                                                            onclick="showQuoteDetails(event)"
                                                                                                            id="quote_details">Show
                                                                                                    </button>`;
                            @if(auth()->user()->checkPermission('Print Sales Orders'))
                                buttons += `
                                                                                                                                    <a href="${receipt_url}" target="_blank">
                                                                                                                                        <button class="btn btn-sm btn-rounded btn-secondary" type="button">
                                                                                                                                            <span class="fa fa-print" aria-hidden="true"></span>
                                                                                                                                            Print
                                                                                                                                        </button>
                                                                                                                                    </a>`;
                            @endif

                                                                             if (parseInt(row.status, 10) === 1) {
                                @if(auth()->user()->checkPermission('Edit Sales Orders'))
                                    buttons += `
                                                                                                                                <a class="btn btn-sm btn-rounded btn-info" href="${update_url}">
                                                                                                                                    Edit
                                                                                                                                </a>`;
                                @endif
                                @if(auth()->user()->checkPermission('Convert Sales Orders'))
                                    buttons += `
                                                                                                                            <button class="btn btn-sm btn-rounded btn-warning"
                                                                                                                                    type="button"
                                                                                                                                    onclick="convertQuoteToSale(${row.id})">
                                                                                                                                Convert
                                                                                                                            </button>`;
                                @endif
                                                                                        } else {
                                buttons += `
                                                                                        <button class="btn btn-sm btn-rounded btn-primary opacity-75"
                                                                                                type="button" disabled>
                                                                                            Sold
                                                                                        </button>`;
                            }

                            return buttons;
                        }
                    },
                    {
                        data: 'cost.name',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'cost.vat',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'cost.discount',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'cost.amount',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                    {
                        data: 'id',
                        render: function (data) {
                            return data ?? '';
                        }
                    },
                ],
                language: {
                    emptyTable: "No Sales Order Data Available in the Table"
                },
                aaSorting: [
                    [2, 'desc']
                ],
                columnDefs: [{
                    targets: [5, 6, 7, 8, 9],
                    visible: false
                }]
            });

        });


        function showQuoteDetails(event) {
            let data = quotes_table.row($(event.target).parents('tr')).data();
            // console.log('Data', data);
            quoteDetails(data.remark, data.details, data);
        }


        //Maintain the current Pill on reload
        $(function () {
            // Clear any invalid localStorage entries first
            var lastPill = localStorage.getItem('lastPill');
            if (lastPill && (!lastPill.startsWith('#') || lastPill.includes('http'))) {
                localStorage.removeItem('lastPill');
                lastPill = null;
            }

            $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
                // Store the tab ID instead of the full URL
                var tabId = $(this).attr('href');
                if (tabId && tabId.startsWith('#') && !tabId.includes('http')) {
                    localStorage.setItem('lastPill', tabId);
                }
            });

            // Only activate if it's a valid tab selector (starts with # and no http)
            if (lastPill && lastPill.startsWith('#') && !lastPill.includes('http')) {
                try {
                    var tabElement = $('[href="' + lastPill + '"]');
                    if (tabElement.length > 0) {
                        tabElement.tab('show');
                    }
                } catch (e) {
                    console.warn('Invalid tab selector:', lastPill);
                    localStorage.removeItem('lastPill');
                }
            }
        });

    </script>
    <script src="{{ asset('assets/apotek/js/notification.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/sales.js') }}"></script>
    <script src="{{ asset('assets/apotek/js/customer.js') }}"></script>

    <!-- Professional Conversion Modal -->
    <div class="modal fade" id="professionalConvertModal" tabindex="-1" role="dialog"
        aria-labelledby="professionalConvertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Convert Sales Order to Sale</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="professionalSaleType" class="font-weight-bold">Sale Type <span
                                class="text-danger">*</span></label>
                        <select class="form-control" id="salesType" onchange="salesType(this)" required>
                            <option value="cash" selected>Cash Sale</option>
                            <option value="credit">Credit Sale</option>
                        </select>
                    </div>
                    {{-- <div class="form-group" id="gracePeriodDiv">
                        <label for="gracePeriod">Grace Period<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="" id="gracePeriod">
                    </div> --}}
                    <div class="form-group" id="gracePeriodDiv">
                        <label>Grace Period(In Days)<font color="red">*</font></label>
                        <select class="form-control" name="" id="gracePeriod">
                            <option value="">Select grace period</option>
                            <option value="1">1</option>
                            <option value="7">7</option>
                            <option value="14">14</option>
                            <option value="21">21</option>
                            <option value="30">30</option>
                            <option value="60">60</option>
                            <option value="90">90</option>
                        </select>
                    </div>
                    <input type="hidden" name="" id="convert_id">
                    <div class="form-group mt-3">
                        <label for="conversionNotes">Conversion Notes</label>
                        <textarea class="form-control" id="conversionNotes" rows="3"
                            placeholder="(Optional) Add any notes about this conversion..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" title="Close">Close</button>
                    <button type="button" id="convert_to_sale" class="btn btn-primary btn-convert-order"
                        onclick="convertionConfirm()" title="Convert Order">
                        Convert
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle"></i> Conversion Successful!
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h4 class="text-success mb-3">Sales Order Converted Successfully!</h4>

                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Sale Details:</h6>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Sale ID:</strong><br>
                                    <span class="badge badge-primary" id="convertedSaleId">-</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Sale Type:</strong><br>
                                    <span class="badge badge-info" id="convertedSaleType">-</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <strong>Receipt Number:</strong><br>
                                    <span class="badge badge-secondary" id="convertedReceiptNumber">-</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <strong>Tax Invoice #:</strong><br>
                                    <span class="badge badge-success" id="convertedTaxInvoiceNumber">-</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Delivery Note #:</strong><br>
                                    <span class="badge badge-info" id="convertedDeliveryNoteNumber">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Available Documents:</h6>
                        <div class="btn-group-vertical w-100" role="group">
                            <a href="#" id="invoiceLink" class="btn btn-outline-primary" target="_blank">
                                <i class="fas fa-file-invoice"></i> View Tax Invoice
                            </a>
                            <a href="#" id="deliveryNoteLink" class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-truck"></i> View Delivery Note
                            </a>
                            <a href="#" id="receiptLink" class="btn btn-outline-success" target="_blank">
                                <i class="fas fa-receipt"></i> View Receipt
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Markup -->
    <div class="modal fade" id="convertConfirmModal" tabindex="-1" role="dialog" aria-labelledby="convertConfirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="convertConfirmModalLabel">Confirm Conversion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Are you sure you want to convert this Sales Order? <br> This action cannot be
                        undone!.</p>
                    {{-- <p>Stock quantities will be automatically deducted from inventory.</p> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="convertToSale()">Yes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div class="modal fade" id="convertSuccessModal" tabindex="-1" role="dialog" aria-labelledby="convertSuccessModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="convertSuccessModalLabel">Conversion Successful</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Sales Order converted successfully!</p>
                    <div id="conversionLinks" style="display: none;">
                        <a href="#" target="_blank" class="btn btn-primary mb-2" id="invoiceLink">View/Print Tax Invoice</a>
                        <a href="#" target="_blank" class="btn btn-info" id="deliveryNoteLink">View/Print Delivery Note</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    </div>
@endpush

<!-- Place this script at the end of the file so convertQuoteToSale is globally available -->
<script type="text/javascript">
    let pendingConvertQuoteId = null;
    let lastConvertedSaleId = null;
    function convertQuoteToSale(quoteId) {
        // console.log('Convert button clicked for quote ID:', quoteId);
        pendingConvertQuoteId = quoteId;
        document.getElementById('convert_id').value = quoteId;
        document.getElementById('gracePeriodDiv').style.display = "none";
        $('#professionalConvertModal').modal('show');
    }

    function salesType() {
        var type = document.getElementById('salesType').value;
        // console.log('Sale type changed: ', type);
        if (type === 'credit') {
            document.getElementById('gracePeriod').setAttribute('required', 'required');
            document.getElementById('gracePeriodDiv').style.display = "block";
        } else {
            document.getElementById('gracePeriodDiv').style.display = "none";
        }
    };

    $('#gracePeriod').select2({
        dropdownParent: $('#professionalConvertModal')
    });

    function convertionConfirm() {
        $('#professionalConvertModal').off('hidden.bs.modal').one('hidden.bs.modal', function () {
            $('#convertConfirmModal').modal('show');
        });

        $('#professionalConvertModal').modal('hide');
    }

    function convertToSale() {
        const quoteId = document.getElementById('convert_id').value;
        const note = document.getElementById('conversionNotes').value;
        const sale_type = document.getElementById('salesType').value;
        const grace_period = document.getElementById('gracePeriod').value;
        if (sale_type === 'credit' && (grace_period === '' || grace_period == 0)) {
            notify("Grace period is required for credit sales", "top", "right", "warning");
            return;
        }

        $.ajax({
            url: '{{ route('convert-to-sales') }}',
            type: 'POST',
            data: {
                quote_id: quoteId,
                notes: note,
                sale_type: sale_type,
                grace_period: grace_period,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                console.log('Response is: ', response);
                $('#convertConfirmModal').modal('hide');

                if (response.status === 'success' && response.sale_id) {
                    // Show success modal with document links
                    $('#convertedSaleId').text(response.sale_id);
                    $('#convertedReceiptNumber').text(response.receipt_number || 'N/A');

                    // Update modal with additional info if available
                    if (response.tax_invoice_number) {
                        $('#convertedTaxInvoiceNumber').text(response.tax_invoice_number);
                    }
                    if (response.delivery_note_number) {
                        $('#convertedDeliveryNoteNumber').text(response.delivery_note_number);
                    }

                    // Set document links
                    // $('#invoiceLink').attr('href', '{{ route('generate-tax-invoice', '') }}/' + response.quote_id);
                    $('#deliveryNoteLink').attr('href', '{{ route('generate-delivery-note', '') }}/' + response.sale_id);
                    $('#receiptLink').attr('href', '{{ route('getCashReceipt', '') }}/' + response.sale_id);

                    // $('#successModal').modal('show');
                    notify(response.message, "top", "right", "success");

                    // Refresh the table
                    if (typeof getQuotes === 'function') {
                        getQuotes();
                    } else if (quotes_table && quotes_table.ajax) {
                        quotes_table.ajax.reload();
                    }

                    // Open receipt in new tab
                    let receiptUrl = '{{ route('getCashReceipt', '') }}/' + response.sale_id;
                    window.open(receiptUrl, '_blank');

                } else {
                    notify(response.error, "top", "right", "danger");
                }
            },
            error: function (xhr, status, error) {
                $('#convertConfirmModal').modal('hide');
                console.error('Conversion Error:', xhr.responseText);
                notify('An error occurred: ' + error, "top", "right", "danger")
            },
            complete: function () {
                $('#convertConfirmModal').prop('disabled', false).html('Yes, Convert');
                pendingConvertQuoteId = null;
            }
        });
    };

    // Professional alert function
    function showAlert(type, title, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';

        var alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="${iconClass}"></i> <strong>${title}:</strong> ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;

        $('body').append(alertHtml);

        // Auto remove after 5 seconds
        setTimeout(function () {
            $('.alert').fadeOut(500, function () {
                $(this).remove();
            });
        }, 5000);
    }
</script>