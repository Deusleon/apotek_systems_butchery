@extends("layouts.master")

@section('content-title')
    Goods Receiving
@endsection



@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Purchasing / Goods Receiving</a></li>
@endsection

@section("content")
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
        <ul class="nav nav-pills mb-3" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="invoice-received" data-toggle="pill"
                   href="{{ route('goods-receiving.index') }}" role="tab"
                   aria-controls="quotes_list" aria-selected="false">Invoice Receiving</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-uppercase" id="order-received" data-toggle="pill"
                   href="{{ route('orders-receiving.index') }}"
                   role="tab" aria-controls="new_quotes" aria-selected="true">Order Receiving
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-uppercase" id="material-received" data-toggle="pill"
                   href="{{ url('purchases/material-received') }}"
                   role="tab" aria-controls="new_quotes" aria-selected="false">Material Received
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="order-receive" role="tabpanel" aria-labelledby="new_quotes-tab">
                <div class="table-responsive" id="purchases">
                    <table id="orders_table" class="display table nowrap table-striped table-hover"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-right">Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
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
                    <h5 class="modal-title" id="receiveOrderModalLabel">Receive Purchase Order: <span id="modal_order_number"></span></h5>
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
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Product (Pack Size)</th>
                                    <th class="text-right">Ordered</th>
                                    <th class="text-right">Received</th>
                                    <th class="text-right">Remaining</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Total Price</th>
                                    <th style="width: 120px;">Receive Qty</th>
                                    <th style="width: 150px;">Batch Number</th>
                                    <th style="width: 150px;">Expiry Date</th>
                                </tr>
                                </thead>
                                <tbody id="order_items_body">
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

@include('partials.notification')


@push("page_scripts")
<script>
    $(document).ready(function() {
        const orders = @json($orders);

        // 1. INITIALIZE THE ORDERS DATATABLE
        const ordersTable = $('#orders_table').DataTable({
            "data": orders,
            "columns": [
                { "data": "order_number" },
                { "data": "supplier.name" },
                {
                    "data": "ordered_at",
                    "render": function(data) {
                        return data ? new Date(data).toLocaleDateString('en-GB') : '';
                    }
                },
                {
                    "data": "total_amount", "className": "text-right",
                    "render": function(data) {
                        return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                },
                {
                    "data": "status", "className": "text-center",
                    "render": function(data) {
                        if (data == '1') return "<span class='badge badge-secondary'>Pending</span>";
                        if (data == '2') return "<span class='badge badge-info'>Partial</span>";
                        if (data == '3') return "<span class='badge badge-success'>Received</span>";
                        return "<span class='badge badge-light'>Unknown</span>";
                    }
                },
                {
                    "data": null, "className": "text-center", "orderable": false,
                    "render": function(data, type, row) {
                        if (row.status == '3') { // 3 = Received
                            return `<button class="btn btn-sm btn-success" disabled>Fully Received</button>`;
                        }
                        return `<button class="btn btn-primary btn-sm receive-btn" data-id="${row.id}">Receive Order</button>`;
                    }
                }
            ],
            "responsive": true,
            "pageLength": 10,
            "order": [] // Disable initial sorting, rely on server-side order
        });

        // 2. DYNAMICALLY POPULATE MODAL WITH ORDER DETAILS
        $('#orders_table tbody').on('click', '.receive-btn', function() {
            const orderId = $(this).data('id');
            const orderData = orders.find(order => order.id === orderId);
            const detailsBody = $('#order_items_body');
            detailsBody.empty(); // Clear previous details

            if (!orderData) {
                console.error('Order data not found for ID:', orderId);
                return;
            }

            // Populate modal headers
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

                if (!isFullyReceived) {
                    hasReceivableItems = true;
                }

                const productName = item.product ? item.product.name : 'N/A';
                const packSize = item.pack_size || 'N/A';

                const row = `
                    <tr>
                        <td>${productName} (${packSize})</td>
                        <td class="text-right ordered-qty">${orderedQty}</td>
                        <td class="text-right received-qty-cell" data-initial-received="${receivedQty}">${receivedQty}</td>
                        <td class="text-right remaining-qty-cell">${remaining}</td>
                        <td class="text-right">${unitPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td class="text-right">${totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        <td>
                            <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm text-center receive-qty-input" value="" min="0" max="${remaining}" ${isFullyReceived ? 'disabled' : ''} placeholder="0">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][purchase_order_detail_id]" value="${item.id}">
                            <input type="hidden" name="items[${index}][cost_price]" value="${unitPrice}">
                        </td>
                        <td><input type="text" name="items[${index}][batch_number]" class="form-control form-control-sm" ${isFullyReceived ? 'disabled' : ''}></td>
                        <td><input type="date" name="items[${index}][expiry_date]" class="form-control form-control-sm" ${isFullyReceived ? 'disabled' : ''}></td>
                    </tr>
                `;
                detailsBody.append(row);
            });

            $('#proceed-receive-btn').prop('disabled', !hasReceivableItems);
            $('#receiveOrderModal').modal('show');
        });

        // 3. DYNAMICALLY UPDATE QUANTITIES ON INPUT
        $('#order_items_body').on('input', '.receive-qty-input', function() {
            const $row = $(this).closest('tr');
            const receiveQtyInput = parseFloat($(this).val()) || 0;
            const orderedQty = parseFloat($row.find('.ordered-qty').text()) || 0;
            const initialReceivedQty = parseFloat($row.find('.received-qty-cell').data('initial-received')) || 0;
            const remainingBeforeInput = orderedQty - initialReceivedQty;

            // Prevent received from exceeding ordered
            const currentReceiveQty = receiveQtyInput > remainingBeforeInput ? remainingBeforeInput : receiveQtyInput;
            if (receiveQtyInput > remainingBeforeInput) {
                $(this).val(remainingBeforeInput);
            }

            const newReceivedQty = initialReceivedQty + currentReceiveQty;
            const newRemainingQty = orderedQty - newReceivedQty;

            $row.find('.received-qty-cell').text(newReceivedQty);
            $row.find('.remaining-qty-cell').text(newRemainingQty);
        });

        // 4. CONFIGURE STANDARD FORM SUBMISSION
        $('#proceed-receive-btn').on('click', function() {
            const form = $('#receiveOrderForm');
            const formData = form.serializeArray();
            let hasQuantity = false;

            // Check if at least one quantity has been entered
            for (let i = 0; i < formData.length; i++) {
                if (formData[i].name.includes('[quantity]') && parseFloat(formData[i].value) > 0) {
                    hasQuantity = true;
                    break;
                }
            }

            if (!hasQuantity) {
                alert('Please enter a quantity for at least one item to receive.');
                return; // Stop if no quantity is entered
            }

            // Disable the button and submit the form
            $(this).prop('disabled', true).text('Processing...');
            form.submit();
        });

        // Fade out alerts after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);

        // 5. RESET MODAL ON CLOSE
        $('#receiveOrderModal').on('hidden.bs.modal', function() {
            $('#receiveOrderForm')[0].reset();
            $('#order_items_body').empty();
            $('#proceed-receive-btn').prop('disabled', false).text('Receive Products');
        });

        // 6. Fix Tab navigation
        $('.nav-link').on('click', function(e) {
            const target = $(this).attr('href');
            if (target && target !== '#') {
                e.preventDefault();
                window.location.href = target;
            }
        });
    });
</script>
@endpush