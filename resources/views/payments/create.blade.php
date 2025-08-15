@extends("layouts.master")

@section('content-title')
    Record Payment for Order #{{ $transportOrder->order_number }}
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}">Transport Orders</a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.show', $transportOrder) }}">Order #{{ $transportOrder->order_number }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.payments.index', $transportOrder) }}">Payments</a></li>
    <li class="breadcrumb-item active">Record Payment</li>
@endsection

@section("content")
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Information</h5>
                    <div class="card-header-right">
                        <div class="btn-group card-option">
                            <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="feather icon-more-vertical"></i>
                            </button>
                            <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Transport Rate:</label>
                                <input type="text" class="form-control" value="{{ number_format($transportOrder->transport_rate, 2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amount Paid:</label>
                                <input type="text" class="form-control" value="{{ number_format($summary['total_paid'], 2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Balance Due:</label>
                                <input type="text" class="form-control" value="{{ number_format($summary['remaining_balance'], 2) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('transport-orders.payments.store', $transportOrder) }}" method="POST" enctype="multipart/form-data">
    @csrf
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="amount">Amount *</label>
                                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" 
                                           value="{{ old('amount') }}" required 
                                           max="{{ $summary['remaining_balance'] }}">
                                    <small class="form-text text-muted">
                                        Maximum allowed amount: {{ number_format($summary['remaining_balance'], 2) }}
                                    </small>
                                    @error('amount')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_type">Payment Type *</label>
                                    <select class="form-control" id="payment_type" name="payment_type" required>
                                        <option value="advance" {{ old('payment_type') == 'advance' ? 'selected' : '' }}>Advance Payment</option>
                                        <option value="balance" {{ old('payment_type') == 'balance' ? 'selected' : '' }}>Balance Payment</option>
                                    </select>
                                    @error('payment_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method *</label>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        @foreach(\App\Payment::paymentMethods() as $key => $method)
                                            <option value="{{ $key }}" {{ old('payment_method') == $key ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="payment_date">Payment Date *</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="{{ old('payment_date', date('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                                    @error('payment_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="receipt_number">Receipt Number *</label>
                                    <input type="text" class="form-control" id="receipt_number" name="receipt_number" 
                                           value="{{ old('receipt_number') }}" required>
                                    @error('receipt_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_reference">Transaction Reference</label>
                                    <input type="text" class="form-control" id="transaction_reference" 
                                           name="transaction_reference" value="{{ old('transaction_reference') }}">
                                    @error('transaction_reference')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_proof">Payment Proof (Receipt/Screenshot)</label>
                            <input type="file" class="form-control-file" id="payment_proof" name="payment_proof">
                            <small class="form-text text-muted">Accepted formats: JPG, PNG, PDF (Max: 2MB)</small>
                            @error('payment_proof')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                        
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-save"></i> Record Payment
                            </button>
                            <a href="{{ route('transport-orders.payments.index', $transportOrder) }}" class="btn btn-secondary">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentType = document.getElementById('payment_type');
    const amountInput = document.getElementById('amount');
    const remainingBalance = {{ $summary['remaining_balance'] }};
    const advanceBalance = {{ $summary['advance_balance'] }};
    
    function updateAmountLimit() {
        const selectedType = paymentType.value;
        let maxAmount = remainingBalance;
        
        if (selectedType === 'advance') {
            maxAmount = Math.min(advanceBalance, remainingBalance);
        }
        
        amountInput.max = maxAmount;
        amountInput.nextElementSibling.textContent = 
            `Maximum allowed amount: ${maxAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        if (parseFloat(amountInput.value) > maxAmount) {
            amountInput.value = maxAmount.toFixed(2);
        }
    }
    
    // Initialize on page load
    updateAmountLimit();
    
    // Update when payment type changes
    paymentType.addEventListener('change', updateAmountLimit);
    
    // Validate amount on input
    amountInput.addEventListener('input', function() {
        const maxAmount = parseFloat(this.max);
        const enteredAmount = parseFloat(this.value) || 0;
        
        if (enteredAmount > maxAmount) {
            this.value = maxAmount.toFixed(2);
        }
    });
});
</script>
@endpush