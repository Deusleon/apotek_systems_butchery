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
        .datepicker > .datepicker-days {
            display: block;
        }

        ol.linenums {
            margin: 0 0 0 -8px;
        }
    </style>

    <style type="text/css">
        .iti__flag {
            background-image: url("{{ asset('assets/plugins/intl-tel-input/img/flags.png') }}");
        }

        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .iti__flag {
                background-image: url("{{ asset('assets/plugins/intl-tel-input/img/flags@2x.png') }}");
            }
        }

        .iti {
            width: 100%;
        }
    </style>
    <div class="col-sm-12">
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">

            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received" data-toggle="pill"
                   href="{{ route('goods-receiving.index') }}" role="tab"
                   aria-controls="quotes_list" aria-selected="true">Invoice Receiving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received" data-toggle="pill"
                   href="{{ route('orders-receiving.index') }}"
                   role="tab" aria-controls="new_quotes" aria-selected="false">Order Receiving
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="material-received" data-toggle="pill"
                   href="{{ url('purchasing/material-received') }}"
                   role="tab" aria-controls="new_quotes" aria-selected="false">Material Received
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="order-receive" role="tabpanel" aria-labelledby="new_quotes-tab">
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
                            <th hidden>id</th>
                        </tr>
                        </thead>
                        <tbody>
                          <!-- This will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Receive Order Modal -->
    <div class="modal fade" id="receiveOrderModal" tabindex="-1" role="dialog" aria-labelledby="receiveOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex justify-content-between w-100">
                        <h5 class="modal-title" id="receiveOrderModalLabel">Receive Purchase Order: <span id="modal_order_number"></span></h5>
                        <div>
                            <span class="mr-3">Supplier: <span id="modal_supplier_name"></span></span>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="receiveOrderForm" action="{{ route('goods-receiving.orderReceive') }}" method="POST">
                        @csrf
                        <input type="hidden" name="supplier_id" id="modal_supplier_id">
                        <input type="hidden" name="order_id" id="modal_order_id">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product (Pack Size)</th>
                                    <th class="text-right">Ordered Qty</th>
                                    <th class="text-right">Received Qty</th>
                                    <th class="text-right">Remaining Qty</th>
                                    <th>Batch Number</th>
                                    <th>Expiry Date</th>
                                    <th class="text-center">Receive Qty</th>
                                </tr>
                                </thead>
                                <tbody id="modal_order_details_body">
                                <!-- Dynamic content will be injected here by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="proceed-receive-btn">Receive Products</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push("page_scripts")

    <script>
        $(document).ready(function() {
            // 1. INITIALIZE THE ORDERS DATATABLE
            var ordersTable = $('#fixed-header-2').DataTable({
                "processing": true,
                "serverSide": false,
                "data": JSON.parse('@json($orders)'),
                "columns": [
                    { "data": "order_number", "title": "Order #" },
                    { "data": "supplier.name", "title": "Supplier" },
                    {
                        "data": "ordered_at", "title": "Date",
                        "render": function(data) {
                            return data ? new Date(data).toLocaleDateString('en-GB') : '';
                        }
                    },
                    {
                        "data": "total_amount", "title": "Amount", "className": "text-right",
                        "render": function(data) {
                            return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                    },
                    {
                        "data": "status", "title": "Status",
                        "render": function(data) {
                            if (data == '1') return "<span class='badge badge-secondary'>Pending</span>";
                            if (data == '2') return "<span class='badge badge-info'>Partial</span>";
                            if (data == '3') return "<span class='badge badge-success'>Received</span>";
                            return "<span class='badge badge-light'>Unknown</span>";
                        }
                    },
                    {
                        "data": null, "title": "Action", "orderable": false,
                        "render": function(data, type, row) {
                            if (row.status == '3') {
                                return `<button class="btn btn-sm btn-success" disabled>Fully Received</button>`;
                            }
                            return `<button class="btn btn-primary btn-sm receive-order-btn">Receive Order</button>`;
                        }
                    }
                ],
                "responsive": true,
                "pageLength": 10,
                "order": [[2, 'desc']]
            });

            // 2. CONFIGURE EVENT LISTENER FOR THE 'RECEIVE ORDER' BUTTON
            $('#fixed-header-2 tbody').on('click', '.receive-order-btn', function(e) {
                e.preventDefault();
                const orderData = ordersTable.row($(this).parents('tr')).data();
                const detailsBody = $('#modal_order_details_body');
                detailsBody.empty();

                if (!orderData) {
                    alert('Error: Could not retrieve order data.');
                    return;
                }

                // Populate modal headers
                $('#modal_order_id').val(orderData.id);
                $('#modal_order_number').text(orderData.order_number);
                $('#modal_supplier_id').val(orderData.supplier_id);
                $('#modal_supplier_name').text(orderData.supplier.name);

                // Populate modal table headers
                detailsBody.append(`
                    <tr class="thead-light">
                        <th>Product (Pack Size)</th>
                        <th class="text-right">Ordered</th>
                        <th class="text-right">Received</th>
                        <th class="text-right">Remaining</th>
                        <th>Batch #</th>
                        <th>Expiry</th>
                        <th class="text-center">Receive Qty</th>
                    </tr>`);

                let itemsAdded = 0;
                orderData.details.forEach((item, index) => {
                    const remaining = (item.quantity || 0) - (item.received_quantity || 0);
                    if (remaining <= 0) return;

                    const productName = item.product ? item.product.name : 'N/A';
                    const packSize = item.product ? item.product.pack_size : 'N/A';

                    detailsBody.append(`
                        <tr>
                            <td>${productName} (${packSize})</td>
                            <td class="text-right">${item.quantity}</td>
                            <td class="text-right">${item.received_quantity}</td>
                            <td class="text-right">${remaining}</td>
                            <td><input type="text" name="items[${index}][batch_number]" class="form-control form-control-sm" required></td>
                            <td><input type="date" name="items[${index}][expiry_date]" class="form-control form-control-sm" required></td>
                            <td>
                                <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm text-center" value="${remaining}" min="1" max="${remaining}" required>
                                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                <input type="hidden" name="items[${index}][purchase_order_detail_id]" value="${item.id}">
                                <input type="hidden" name="items[${index}][cost_price]" value="${item.unit_price}">
                            </td>
                        </tr>`);
                    itemsAdded++;
                });

                if (itemsAdded === 0) {
                    detailsBody.append('<tr><td colspan="7" class="text-center">All items are fully received.</td></tr>');
                }

                $('#receiveOrderModal').modal('show');
            });

            // 3. CONFIGURE AJAX FORM SUBMISSION
            $('#proceed-receive-btn').on('click', function() {
                const form = $('#receiveOrderForm');
                const button = $(this);
                const items = [];
                let hasErrors = false;

                // Basic validation
                form.find('tbody tr').each(function() {
                    const quantityInput = $(this).find('input[name*="[quantity]"]');
                    if (quantityInput.length && (!quantityInput.val() || parseInt(quantityInput.val()) <= 0)) {
                        alert('Quantity must be greater than 0.');
                        hasErrors = true;
                        return false; // break loop
                    }
                });

                if (hasErrors) return;

                button.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(response) {
                        alert(response.success || 'Operation successful!');
                        location.reload();
                    },
                    error: function(xhr) {
                        let errorMsg = 'An unknown error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert('Error: ' + errorMsg);
                        console.error('AJAX Error:', xhr.responseText);
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Receive Products');
                    }
                });
            });

            // 4. RESET MODAL ON CLOSE
            $('#receiveOrderModal').on('hidden.bs.modal', function() {
                $('#receiveOrderForm')[0].reset();
                $('#modal_order_details_body').empty();
            });
        });
    </script>

    <script src="{{asset('assets/js/pages/ac-datepicker.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#order-received').on('click', function(e) {
                e.preventDefault();
                var redirectUrl = $(this).attr('href');
                window.location.href = redirectUrl;
            });

            $('#material-received').on('click', function(e) {
                e.preventDefault();
                var redirectUrl = $(this).attr('href');
                window.location.href = redirectUrl;
            });

            $('#invoice-received').on('click', function(e) {
                e.preventDefault(); // Prevent default tab switching behavior
                var redirectUrl = $(this).attr('href'); // Get the URL from the href attribute
                window.location.href = redirectUrl; // Redirect to the URL
            });

        });
    </script>

@endpush
