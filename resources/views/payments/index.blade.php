@extends("layouts.master")

@section('page_css')
<link rel="stylesheet" href="{{ asset('assets/plugins/data-tables/css/datatables.min.css') }}">
<style>
    .feather.spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    #addPaymentModal .modal-header {
        padding: 0.75rem 1.5rem;
    }

    #paymentFormContainer .form-group {
        margin-bottom: 1.25rem;
    }

    .alert-fixed {
        position: fixed;
        top: 70px;
        right: 20px;
        width: 300px;
        z-index: 9999;
    }

    @media (max-width: 768px) {
        #addPaymentModal .modal-dialog {
            margin: 0.5rem auto;
        }
    }
</style>
@endsection

@section('content-title')
@isset($transportOrder)
Payments for Order #{{ $transportOrder->order_number }}
@else
Payments
@endisset
@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}">Transport Orders</a></li>
@isset($transportOrder)
<li class="breadcrumb-item"><a href="{{ route('transport-orders.show', $transportOrder) }}">Order #{{ $transportOrder->order_number }}</a></li>
@endif
<li class="breadcrumb-item active">Payments</li>
@endsection

@section("content")
<!-- Alerts Container -->
<div id="alertsContainer" class="alert-fixed"></div>

@if (session('error'))
<div class="alert alert-danger alert-top-right mx-auto" style="width: 70%">
    {{ session('error') }}
</div>
@endif
@if (session('success'))
<div class="alert alert-success alert-top-right mx-auto" style="width: 70%">
    {{ session('success') }}
</div>
@endif

<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <!-- <h5 class="mb-0">Payments</h5> -->
                <button type="button" class="btn btn-secondary btn-sm btn-add-payment ml-auto">
                    Add Payment
                </button>
            </div>
        </div>
        <div class="card-body">
            @isset($transportOrder)
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="border p-3 bg-light rounded">
                        <strong>Transport Rate:</strong>
                        <div class="h5">{{ number_format($transportOrder->transport_rate, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 bg-light rounded">
                        <strong>Amount Paid:</strong>
                        <div class="h5">{{ number_format($transportOrder->payments->sum('amount'), 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border p-3 bg-light rounded">
                        <strong>Balance Due:</strong>
                        <div class="h5">{{ number_format($transportOrder->balance(), 2) }}</div>
                    </div>
                </div>
            </div>
            @endisset

            <div class="table-responsive">
                <table id="fixed-header-payments" class="display table nowrap table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Order #</th>
                            <th>Transporter</th>
                            <th>Amount</th>
                            <!-- <th>Payment Type</th> -->
                            <th>Method</th>
                            <!-- <th>Status</th> -->
                            <!-- <th>Receipt Number</th> -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr data-id="{{ $payment->id }}">
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td>{{ $payment->transportOrder->order_number ?? 'N/A' }}</td>
                            <td>{{ $payment->transportOrder->transporter->name ?? 'N/A' }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <!-- <td>{{ $payment->transportOrder ? number_format($payment->transportOrder->balance_due, 2) : 'N/A' }}</td> -->
                            <!-- <td>{{ ucfirst($payment->payment_type) }}</td> -->
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <!-- <td>
                                @if($payment->transportOrder && $payment->amount >= $payment->transportOrder->transport_rate)
                                <span class="badge badge-success">Complete</span>
                                @else
                                <span class="badge badge-warning">Advance</span>
                                @endif
                            </td> -->
                            <!-- <td>{{ $payment->receipt_number }}</td> -->
                            <td style='white-space: nowrap'>
                                <button class="btn btn-success btn-sm btn-rounded btn-show"
                                    data-id="{{$payment->id}}"
                                    data-amount="{{number_format($payment->amount, 2)}}"
                                    data-payment_type="{{ucfirst($payment->payment_type)}}"
                                    data-payment_method="{{ucfirst(str_replace('_', ' ', $payment->payment_method))}}"
                                    data-payment_date="{{$payment->payment_date->format('Y-m-d')}}"
                                    data-transaction_reference="{{$payment->transaction_reference}}"
                                    data-receipt_number="{{$payment->receipt_number}}"
                                    data-notes="{{$payment->notes}}"
                                    data-status="{{$payment->status}}"
                                    data-order_number="{{$payment->transportOrder->order_number ?? 'N/A'}}"
                                    type="button" data-toggle="modal" data-target="#showPayment">
                                    Show
                                </button>

                                <button class="btn btn-primary btn-sm btn-rounded btn-edit"
        data-id="{{ $payment->id }}"
        data-url="{{ route('transport-orders.payments.edit', [$payment->transport_order_id, $payment->id]) }}"
        data-amount="{{ $payment->amount }}"
        data-payment_date="{{ $payment->payment_date->format('Y-m-d') }}"
        data-payment_method="{{ $payment->payment_method }}"
        data-receipt_number="{{ $payment->receipt_number }}"
        data-transaction_reference="{{ $payment->transaction_reference }}"
        data-notes="{{ $payment->notes }}"
        type="button" 
        data-toggle="modal" 
        data-target="#editPaymentModal">
    Edit
</button>
                                <!-- @if(isset($payment->transportOrder))
                                <button type="button" class="btn btn-sm btn-primary btn-rounded edit-payment-btn"
                                        data-url="{{ route('transport-orders.payments.show', [$payment->transportOrder, $payment]) }}"
                                        data-toggle="modal" data-target="#editPaymentModal">
                                    Edit
                                </button>
                                @endif -->
                                @if(!$payment->transportOrder || $payment->transportOrder->balance() + $payment->amount > 0)
                                <button class="btn btn-danger btn-sm btn-rounded btn-delete"
                                    data-id="{{$payment->id}}"
                                    data-receipt_number="{{$payment->receipt_number}}"
                                    data-amount="{{number_format($payment->amount, 2)}}"
                                    type="button" data-toggle="modal" data-target="#deletePaymentModal">
                                    Delete
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title" id="addPaymentModalLabel">New Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Order Lookup -->
                <div id="step1">
                    <div class="form-group">
                        <label for="order_number" class="font-weight-bold">Enter Order Number</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="order_number" name="order_number" placeholder="e.g. TR-000123">
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" id="scanOrderBtn">
                                    <i class="feather icon-maximize"></i> Scan
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Enter or scan the transport order number</small>
                    </div>
                    <div id="lookupError" class="alert alert-danger" style="display:none;"></div>
                </div>

                <!-- Step 2: Payment Form -->
                <div id="step2" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Payment Details</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="backToStep1">
                            Back
                        </button>
                    </div>
                    <div id="paymentFormContainer"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" id="nextBtn" class="btn btn-primary">
                    Next
                </button>
                <button type="button" id="submitPaymentBtn" class="btn btn-primary" style="display:none;">
                   Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Show Payment Modal -->
<div class="modal fade" id="showPayment" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Order Number:</label>
                            <input type="text" class="form-control" id="show_order_number" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Date:</label>
                            <input type="text" class="form-control" id="show_payment_date" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Amount:</label>
                            <input type="text" class="form-control" id="show_amount" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Type:</label>
                            <input type="text" class="form-control" id="show_payment_type" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Payment Method:</label>
                            <input type="text" class="form-control" id="show_payment_method" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Receipt Number:</label>
                            <input type="text" class="form-control" id="show_receipt_number" disabled>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Transaction Ref:</label>
                            <input type="text" class="form-control" id="show_transaction_reference" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status:</label>
                            <div id="show_status" class="pt-2"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Notes:</label>
                            <textarea class="form-control" id="show_notes" rows="2" disabled></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Payment Proof:</label>
                            <div id="show_payment_proof" class="mt-2 text-center text-muted">
                                <em>No payment proof uploaded</em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPaymentForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Order Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order Number</label>
                                <input type="text" class="form-control" id="edit_order_number" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Transporter</label>
                                <input type="text" class="form-control" id="edit_transporter_name" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_amount">Amount *</label>
                                <input type="number" step="0.01" class="form-control" name="amount" id="edit_amount" required>
                                <small class="text-muted" id="edit_max_amount_text"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_payment_date">Payment Date *</label>
                                <input type="date" class="form-control" name="payment_date" id="edit_payment_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_payment_method">Payment Method *</label>
                                <select class="form-control" name="payment_method" id="edit_payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_receipt_number">Receipt Number *</label>
                                <input type="text" class="form-control" name="receipt_number" id="edit_receipt_number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_transaction_reference">Transaction Reference</label>
                                <input type="text" class="form-control" name="transaction_reference" id="edit_transaction_reference">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_payment_proof">Payment Proof (Optional)</label>
                                <input type="file" class="form-control-file" name="payment_proof" id="edit_payment_proof">
                                <small class="text-muted">Leave blank to keep current proof.</small>
                                <div id="edit_payment_proof_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_notes">Notes</label>
                        <textarea class="form-control" name="notes" id="edit_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePaymentModal" tabindex="-1" role="dialog" aria-labelledby="deletePaymentLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePaymentLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this payment? This action cannot be undone.</p>
                <p><strong>Receipt Number:</strong> <span id="delete_receipt_number"></span></p>
                <p><strong>Amount:</strong> <span id="delete_amount"></span></p>
            </div>
            <div class="modal-footer">
                <form id="form_payment_delete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push("page_scripts")
<script src="{{ asset('assets/plugins/data-tables/js/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        if ($('#fixed-header-payments').length) {
            $('#fixed-header-payments').DataTable({
                responsive: true,
                autoWidth: false,
                order: [
                    [0, 'desc']
                ]
            });
        }

        function showAlert(type, message) {
            const alert = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                                ${message}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>`;
            $('#alertsContainer').html(alert);
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        function resetAddPaymentModal() {
            $('#step1').show();
            $('#step2').hide();
            $('#nextBtn').show().prop('disabled', false).html('<i class="feather icon-arrow-right"></i> Next');
            $('#submitPaymentBtn').hide();
            $('#order_number').val('');
            $('#lookupError').hide();
            $('#paymentFormContainer').empty();
        }

        // --- Add Payment Modal Workflow ---

        // 1. Show Modal and Reset
        $('.btn-add-payment').on('click', function() {
            $('#addPaymentModal').modal('show');
        });

        $('#addPaymentModal').on('shown.bs.modal', function() {
            resetAddPaymentModal();
            $('#order_number').focus();
        });

        // 2. Go back to lookup
        $('#backToStep1').on('click', function() {
            resetAddPaymentModal();
        });

        // 3. Look up order number
        $('#nextBtn').on('click', function() {
            const orderNumber = $('#order_number').val().trim();
            if (!orderNumber) {
                showLookupError('Please enter an order number.');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).html('<i class="feather icon-loader spin"></i> Looking up...');
            $('#lookupError').hide();

            $.ajax({
                url: '{{ route("payments.lookup") }}',
                type: 'POST',
                data: {
                    order_number: orderNumber,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#paymentFormContainer').html(response.html);
                        $('#step1').hide();
                        $('#step2').show();
                        $('#nextBtn').hide();
                        $('#submitPaymentBtn').show();
                    } else {
                        showLookupError(response.message);
                    }
                    btn.prop('disabled', false).html('<i class="feather icon-arrow-right"></i> Next');
                },
                error: function(xhr) {
                    const error = xhr.responseJSON ? xhr.responseJSON.message : 'An unexpected error occurred.';
                    showLookupError(error);
                    btn.prop('disabled', false).html('<i class="feather icon-arrow-right"></i> Next');
                }
            });
        });

        // 4. Trigger form submission
        $('#submitPaymentBtn').on('click', function() {
            $('#paymentForm').trigger('submit');
        });

        // 5. Handle AJAX form submission
        $(document).on('submit', '#paymentForm', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = $('#submitPaymentBtn');

            submitBtn.prop('disabled', true).html('<i class="feather icon-loader spin"></i> Processing...');

            let formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addPaymentModal').modal('hide');
                        showAlert('success', response.message);
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showAlert('danger', response.message);
                        submitBtn.prop('disabled', false).html('<i class="feather icon-check"></i> Submit Payment');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            errorMsg = Object.values(xhr.responseJSON.errors).join('<br>');
                        }
                    }
                    showAlert('danger', errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="feather icon-check"></i> Submit Payment');
                }
            });
        });

        function showLookupError(message) {
            $('#lookupError').html(message).show();
            $('#order_number').focus();
        }

        // --- Other Modal Handlers ---

        // Delete Modal
        $('#deletePaymentModal').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let id = button.data('id');
            $('#deletePaymentForm').attr('action', '{{ url("payments") }}/' + id);
        });

        // Show Modal
        $('#showPayment').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            $('#show_order_number').val(button.data('order_number'));
            $('#show_payment_date').val(button.data('payment_date'));
            $('#show_amount').val(button.data('amount'));
            $('#show_payment_type').val(button.data('payment_type'));
            $('#show_payment_method').val(button.data('payment_method'));
            $('#show_receipt_number').val(button.data('receipt_number'));
            $('#show_transaction_reference').val(button.data('transaction_reference') || 'N/A');
            $('#show_notes').val(button.data('notes') || 'N/A');

            var status = button.data('status');
            var statusBadge = '';
            if (status === 'completed') {
                statusBadge = '<span class="badge badge-success">Completed</span>';
            } else if (status === 'confirmed') {
                statusBadge = '<span class="badge badge-info">Confirmed</span>';
            } else {
                statusBadge = '<span class="badge badge-warning">Pending</span>';
            }
            $('#show_status').html(statusBadge);

            var proofUrl = button.data('payment_proof_url');
            if (proofUrl) {
                $('#show_payment_proof').html('<button type="button" class="btn btn-sm btn-primary edit-payment-btn" data-url="{{ route("transport-orders.payments.show", [$transportOrder, $payment]) }}" data-toggle="modal" data-target="#editPaymentModal">Edit</button>');
            } else {
                $('#show_payment_proof').html('<em>No payment proof uploaded</em>');
            }
        });

        $(document).ready(function() {
            // Handle modal opening and data fetching
            $('#editPaymentModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                var editUrl = button.data('url');
                var updateUrl = editUrl.replace('/edit', '');

                // Set form action
                $('#editPaymentForm').attr('action', updateUrl);

                // Fetch payment data via AJAX
                $.ajax({
                    url: editUrl,
                    method: 'GET',
                    success: function(data) {
                        // Populate basic fields from the button's data attributes first
                        modal.find('#edit_amount').val(button.data('amount'));
                        modal.find('#edit_payment_date').val(button.data('payment_date'));
                        modal.find('#edit_payment_method').val(button.data('payment_method'));
                        modal.find('#edit_receipt_number').val(button.data('receipt_number'));
                        modal.find('#edit_transaction_reference').val(button.data('transaction_reference'));
                        modal.find('#edit_notes').val(button.data('notes'));

                        // Populate fields from AJAX response
                        modal.find('#edit_order_number').val(data.summary.order_number);
                        modal.find('#edit_transporter_name').val(data.summary.transporter_name);
                        modal.find('#edit_amount').attr('max', data.max_amount);
                        modal.find('#edit_max_amount_text').text('Maximum amount: ' + parseFloat(data.max_amount).toFixed(2));

                        // Display payment proof if it exists
                        var proofPreview = modal.find('#edit_payment_proof_preview');
                        proofPreview.empty(); // Clear previous preview
                        if (data.payment.payment_proof_url) {
                            proofPreview.html('<a href="' + data.payment.payment_proof_url + '" target="_blank">View current proof</a>');
                        } else {
                            proofPreview.html('<p>No proof uploaded.</p>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch payment details:', xhr.responseText);
                        alert('Could not load payment details. Please try again.');
                        modal.modal('hide');
                    }
                });
            });

            // Handle form submission via AJAX
            $('#editPaymentForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: 'POST', // Form method spoofing (@method('PUT')) handles the actual method
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editPaymentModal').modal('hide');
                            if (response.message) {
                                const alert = `
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        ${response.message}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `;
                                $('#alertsContainer').html(alert);

                                setTimeout(function() {
                                    $("#alertsContainer .alert").alert('close');
                                }, 5000);
                            }

                            if (response.redirect) {
                                setTimeout(function() {
                                    window.location.href = response.redirect;
                                }, 1000);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Clear previous errors
                        form.find('.is-invalid').removeClass('is-invalid');
                        form.find('.invalid-feedback').remove();

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                var field = form.find('[name="' + key + '"]');
                                field.addClass('is-invalid');
                                field.after('<div class="invalid-feedback">' + value[0] + '</div>');
                            });
                        } else {
                            alert('An error occurred: ' + (xhr.responseJSON.message || 'Please try again.'));
                        }
                    }
                });
            });
        });
    });
</script>
@endpush