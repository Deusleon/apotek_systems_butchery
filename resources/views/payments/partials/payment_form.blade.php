<form id="paymentForm" action="{{ isset($payment) ? route('transport-orders.payments.update', [$transportOrder, $payment]) : route('transport-orders.payments.store', $transportOrder) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($payment))
        @method('PUT')
    @endif
    
    @if(isset($transportOrder))
        <input type="hidden" name="transport_order_id" value="{{ $transportOrder->id }}">
    @endif

    <!-- Rest of your form fields remain the same -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-group">
                <label>Order Number</label>
                <input type="text" class="form-control" value="{{ $transportOrder->order_number }}" readonly>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Transporter</label>
                <input type="text" class="form-control" value="{{ $transportOrder->transporter->name }}" readonly>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6>Transport Rate</h6>
                    <h4>{{ number_format($summary['transport_rate'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6>Amount Paid</h6>
                    <h4>{{ number_format($summary['amount_paid'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6>Balance Due</h6>
                    <h4 class="balance-due">{{ number_format($summary['balance_due'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="amount">Amount *</label>
                <input type="number" step="0.01" class="form-control" name="amount" id="amount"
                       value="{{ old('amount', $payment->amount ?? '') }}"
                       max="{{ isset($payment) ? $summary['balance_due'] + $payment->amount : $summary['balance_due'] }}" required>
                <small class="text-muted">Maximum amount: {{ number_format(isset($payment) ? $summary['balance_due'] + $payment->amount : $summary['balance_due'], 2) }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_date">Payment Date *</label>
                <input type="date" class="form-control" name="payment_date" id="payment_date"
                       value="{{ old('payment_date', isset($payment) ? optional($payment->payment_date)->format('Y-m-d') : date('Y-m-d')) }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_method">Payment Method *</label>
                <select class="form-control" name="payment_method" id="payment_method" required>
                    <option value="cash" {{ old('payment_method', $payment->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ old('payment_method', $payment->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="mobile_money" {{ old('payment_method', $payment->payment_method ?? '') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    <option value="cheque" {{ old('payment_method', $payment->payment_method ?? '') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="receipt_number">Receipt Number *</label>
                <input type="text" class="form-control" name="receipt_number" id="receipt_number" value="{{ old('receipt_number', $payment->receipt_number ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="transaction_reference">Transaction Reference</label>
                <input type="text" class="form-control" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference', $payment->transaction_reference ?? '') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="payment_proof">Payment Proof (Optional)</label>
                <input type="file" class="form-control-file" name="payment_proof" id="payment_proof">
                <small class="text-muted">Accepted formats: JPG, PNG, PDF (Max 2MB)</small>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea class="form-control" name="notes" id="notes" rows="2">{{ old('notes', $payment->notes ?? '') }}</textarea>
    </div>
</form>

<script>
document.getElementById('amount').addEventListener('input', function() {
    const initialBalanceDue = parseFloat({{ isset($payment) ? $summary['balance_due'] + $payment->amount : $summary['balance_due'] }});
    const amountEntered = parseFloat(this.value) || 0;
    const balanceDueElement = document.querySelector('.balance-due');
    
    const newBalanceDue = initialBalanceDue - amountEntered;
    
    balanceDueElement.textContent = newBalanceDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
});
</script>