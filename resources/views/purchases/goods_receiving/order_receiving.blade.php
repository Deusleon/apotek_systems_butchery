@extends("layouts.master")

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


@section('content-title')
    Goods Receiving
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Goods Receiving</a></li>
@endsection

@section("content")
    <style>
        /* Wrapper to control table height */
        .receive-items-wrapper {
            max-height: 350px;   /* adjust if needed */
            overflow-y: auto;    /* enable vertical scroll only */
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        /* Compact modal table */
        .receive-items-table th,
        .receive-items-table td {
            padding: 6px 10px !important;  
            vertical-align: middle !important;
            white-space: nowrap;
            font-size: 13px;
        }

        /* Fix table header without background color */
        .receive-items-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: transparent; /* no color */
            border-bottom: 2px solid #dee2e6; /* keep bottom border for separation */
        }

        /* Inputs compact */
        .receive-items-table input {
            height: 30px;
            padding: 2px 6px;
            font-size: 13px;
        }

        .edit-mode-hidden {
            display: none !important;
        }


    </style>
    <div class="col-sm-12">
        @if(Session::has('error'))
            <div class="alert alert-danger alert-top-right mx-auto" style="width: 70%">
                {{ Session::get('error') }}
            </div>
        @endif
        @if(Session::has('alert-success'))
            <div class="alert alert-success alert-top-right mx-auto" style="width: 70%">
                {{ Session::get('alert-success') }}
            </div>
        @endif

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received"
                   href="{{ route('goods-receiving.index') }}">Invoice Receiving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received"
                   href="{{ route('orders-receiving.index') }}">Order Receiving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="material-received"
                   href="{{ url('purchases/material-received') }}">Material Received</a>
            </li>
        </ul>

        <!-- Orders Table -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="order-receive">
                <div class="table-responsive" id="purchases">
                    <table id="orders_table" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Status</th> <!-- Status column renamed to look meaningful -->
                            <th class="text-center">Actions</th> <!-- Actions column -->
                        </tr>
                        </thead>
                        <tbody>
                        <!-- Populated dynamically by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Receive Order Modal -->
    <div class="modal fade" id="receiveOrderModal" tabindex="-1" role="dialog" aria-labelledby="receiveOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document"> <!-- Smaller & centered -->
            <div class="modal-content shadow-lg rounded">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiveOrderModalLabel">
                        <i class="feather icon-package mr-1"></i>Purchase Order:
                        <span id="modal_order_number"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Supplier:</strong> <span id="modal_supplier_name"></span>
                    </div>
                    <form id="receiveOrderForm" action="{{ route('goods-receiving.orderReceive') }}" method="POST">
                        @csrf
                        <input type="hidden" name="supplier_id" id="modal_supplier_id">
                        <input type="hidden" name="order_id" id="modal_order_id">

                       <!-- Order Items Table with fixed header -->
                        <div class="receive-items-wrapper">
                            <table class="table table-striped table-hover table-sm align-middle mb-0 receive-items-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th class="text-right">Ordered</th>
                                        <th class="text-right">Received</th>
                                        <th class="text-right d-none">Remaining</th>
                                        <th class="text-right d-none">Unit Price</th>
                                        <th class="text-right d-none">Total Price</th>
                                        <th class="text-center">Receive</th>
                                        @if($batch_setting === 'YES')
                                            <th class="text-center">Batch #</th>
                                        @endif
                                        @if($expire_date === 'YES')
                                            <th class="text-center">Expiry Date</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody id="order_items_body">
                                    <!-- Injected by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" id="edit-receive-btn">
                            Edit
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="proceed-receive-btn">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('partials.notification')

@push("page_scripts")
<script>
    $(document).ready(function() {
        const orders = @json($orders);
        const batchSetting = @json($batch_setting);
        const expireDate   = @json($expire_date);


        // Initialize DataTable
        const ordersTable = $('#orders_table').DataTable({
            "data": orders,
            "columns": [
                { "data": "order_number" },
                { "data": "supplier.name" },
                {
                    "data": "ordered_at",
                    "render": function(data) {
                        if (!data) return '';
                        const date = new Date(data);
                        return `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
                    }
                },
                {
                    "data": "total_amount", "className": "text-right",
                    "render": function(data) {
                        return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 2 });
                    }
                },
                {
                    "data": "status", 
                    "className": "text-center",
                    "render": function(data) {
                        const badgeClass = "badge btn-rounded btn-sm"; // Rounded & small like buttons
                        const style = "display:inline-block; min-width:110px;"; // maintain fixed width
                        if (data == '2' || data == '1') return `<span class='${badgeClass} badge-warning' style='${style}'>Pending</span>`;
                        if (data == '3') return `<span class='${badgeClass} badge-info' style='${style}'>Partial</span>`;
                        if (data == '4') return `<span class='${badgeClass} badge-success' style='${style}'>Completed</span>`;
                        return `<span class='${badgeClass} badge-secondary' style='${style}'>Rejected</span>`;
                    }
                },
                {
                "data": null, "className": "text-center", "orderable": false,
                "render": function(data, type, row) {
                    if (row.status == '4') {
                        return `<button class="btn btn-success btn-rounded btn-sm" disabled>
                                     Received
                                </button>`;
                    }
                    if (row.status == '1') {
                        return `<button class="btn btn-primary btn-rounded btn-sm receive-btn" data-id="${row.id}">
                                 Receive
                                </button>`;

                    }
                    if (row.status == '2') {
                        return `<button class="btn btn-primary btn-rounded btn-sm receive-btn" data-id="${row.id}">
                                 Receive
                                </button>`;

                    }
                    if (row.status == '3') {
                        return `<button class="btn btn-primary btn-rounded btn-sm receive-btn" data-id="${row.id}">
                                 Receive
                                </button>`;

                    }
                    return `<button class="btn btn-success btn-rounded btn-sm" disabled>
                                     Rejected
                                </button>`;
                }
            }
            ],
            "responsive": true,
            "pageLength": 10,
            "order": []
        });

        // Modal Logic
        $('#orders_table tbody').on('click', '.receive-btn', function() {
            const orderId = $(this).data('id');
            const orderData = orders.find(order => order.id === orderId);
            const detailsBody = $('#order_items_body');
            detailsBody.empty();

            if (!orderData) return;

            $('#modal_order_id').val(orderData.id);
            $('#modal_order_number').text(orderData.order_number);
            $('#modal_supplier_id').val(orderData.supplier_id);
            $('#modal_supplier_name').text(orderData.supplier.name);

            let hasReceivableItems = false;
            orderData.details.forEach((item, index) => {
                const orderedQty = parseFloat(item.quantity) || 0;
                const receivedQty = parseFloat(item.received_quantity) || 0;
                const remaining = orderedQty - receivedQty;
                const isFullyReceived = remaining <= 0;
                const unitPrice = parseFloat(item.price) || 0;
                const totalPrice = parseFloat(item.amount) || 0;

                if (!isFullyReceived) hasReceivableItems = true;

                detailsBody.append(`
                <tr>
                    <td>
                    ${item.product 
                        ? `${item.product.name} ${item.product.pack_size || ''} ${item.product.brand || ''} ${item.product.sales_uom || ''}`
                        : 'N/A'}
                    </td>

                    <td class="text-right ordered-qty">${orderedQty}</td>
                    <td class="text-right received-qty-cell" data-initial-received="${receivedQty}">${receivedQty}</td>
                    <td class="text-right remaining-qty-cell d-none">${remaining}</td>
                    <td class="text-right d-none">${unitPrice.toLocaleString('en-US',{ minimumFractionDigits:2 })}</td>
                    <td class="text-right d-none">${totalPrice.toLocaleString('en-US',{ minimumFractionDigits:2 })}</td>
                    <td class="receive-cell text-center">
                        <span class="plain-receive">${remaining}</span>
                        <input type="text" name="items[${index}][quantity]" 
                            class="form-control form-control-sm text-center receive-qty-input edit-mode-hidden"
                            min="0" max="${remaining}" value="${remaining}" ${isFullyReceived?'readonly':''}>
                    </td>

                    ${batchSetting === 'YES' ? `
                    <td>
                        <input type="text" name="items[${index}][batch_number]" 
                            class="form-control form-control-sm edit-mode-hidden" ${isFullyReceived?'disabled':''}>
                    </td>` : ''}

                    ${expireDate === 'YES' ? `
                    <td>
                        <input type="text" placeholder="YYYY-MM-DD" name="items[${index}][expiry_date]" 
                            class="form-control form-control-sm edit-mode-hidden" ${isFullyReceived?'disabled':''}>
                    </td>` : ''}
                    
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][purchase_order_detail_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][cost_price]" value="${unitPrice}">
                </tr>
                `);


            });

            $('#proceed-receive-btn').prop('disabled', !hasReceivableItems);
            $('#receiveOrderModal').modal('show');
        });

        // Qty Updates
        $('#order_items_body').on('input', '.receive-qty-input', function() {
            const $row = $(this).closest('tr');
            const receiveQtyInput = parseFloat($(this).val()) || 0;
            const orderedQty = parseFloat($row.find('.ordered-qty').text()) || 0;
            const initialReceivedQty = parseFloat($row.find('.received-qty-cell').data('initial-received')) || 0;
            const remainingBeforeInput = orderedQty - initialReceivedQty;

            const currentReceiveQty = receiveQtyInput > remainingBeforeInput ? remainingBeforeInput : receiveQtyInput;
            if (receiveQtyInput > remainingBeforeInput) $(this).val(remainingBeforeInput);

            $row.find('.received-qty-cell').text(initialReceivedQty + currentReceiveQty);
            $row.find('.remaining-qty-cell').text(orderedQty - (initialReceivedQty + currentReceiveQty));
        });

        // Submit
        $('#proceed-receive-btn').on('click', function() {
            const form = $('#receiveOrderForm');
            const formData = form.serializeArray();
            let hasQuantity = formData.some(f => f.name.includes('[quantity]') && parseFloat(f.value) > 0);

            if (!hasQuantity) return alert('Please enter a quantity for at least one item to receive.');

            $(this).prop('disabled', true).text('Saving...');
            form.submit();
        });

        // Auto fade alerts
        setTimeout(() => $('.alert').fadeOut('slow'), 3000);

        // Reset modal
        $('#receiveOrderModal').on('hidden.bs.modal', function() {
            $('#receiveOrderForm')[0].reset();
            $('#order_items_body').empty();
            $('#proceed-receive-btn').prop('disabled', false).text('Save');
        });

        // Initialize flatpickr on Expiry Date inputs
        function initExpiryDatePickers() {
            flatpickr("input[name*='[expiry_date]']", {
                dateFormat: "Y-m-d",
                allowInput: true,    // allows typing if needed
                defaultDate: null    // starts empty
            });
        }

        // Call this every time the modal opens
        $('#receiveOrderModal').on('shown.bs.modal', function() {
            initExpiryDatePickers();
        });

        // Edit button click
        $('#edit-receive-btn').on('click', function() {
            $('#order_items_body tr').each(function() {
                const $row = $(this);

                // Show inputs
                $row.find('.receive-qty-input, [name*="[batch_number]"], [name*="[expiry_date]"]').removeClass('edit-mode-hidden');
                // Hide plain text
                $row.find('.plain-receive').hide();

                // Extract values
                const orderedQty = parseFloat($row.find('.ordered-qty').text()) || 0;
                const initialReceivedQty = parseFloat($row.find('.received-qty-cell').data('initial-received')) || 0;
                const remaining = orderedQty - initialReceivedQty;

                // Pre-fill receive input with the remaining (not ordered) ✅
                $row.find('.receive-qty-input').val(remaining > 0 ? remaining : 0);

                // Update received cell = initial already received + remaining
                $row.find('.received-qty-cell').text(initialReceivedQty + (remaining > 0 ? remaining : 0));

                // Update remaining cell (so it goes to 0 if all filled)
                $row.find('.remaining-qty-cell').text(orderedQty - (initialReceivedQty + remaining));
            });

            // Focus first receive input
            $('#order_items_body tr:first .receive-qty-input').focus();
        });


    });
</script>
@endpush
