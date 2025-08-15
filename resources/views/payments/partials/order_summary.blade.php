<input type="hidden" id="order_number_hidden" value="{{ $transportOrder->order_number }}">

<div class="row">
    <div class="col-md-6">
        <h6>Order Details</h6>
        <p><strong>Order Number:</strong> {{ $transportOrder->order_number }}</p>
        <p><strong>Client:</strong> {{ $transportOrder->client->name }}</p>
        <p><strong>Total Amount:</strong> {{ number_format($transportOrder->transport_rate, 2) }}</p>
    </div>
    <div class="col-md-6">
        <h6>Payment Summary</h6>
        <p><strong>Total Paid:</strong> {{ number_format($summary['total_paid'], 2) }}</p>
        <p><strong>Advance Paid:</strong> {{ number_format($summary['advance_paid'], 2) }}</p>
        <p><strong>Balance Paid:</strong> {{ number_format($summary['balance_paid'], 2) }}</p>
        <p><strong>Remaining Balance:</strong> {{ number_format($summary['remaining_balance'], 2) }}</p>
    </div>
</div>