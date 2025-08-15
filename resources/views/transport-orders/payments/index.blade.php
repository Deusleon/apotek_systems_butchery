@extends("layouts.master")

@section('content-title')
    Payments for Order #{{ $transportOrder->order_number }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
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
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Payment History</h5>
                        <a href="{{ route('transport-orders.payments.create', $transportOrder) }}" 
                           class="btn btn-primary btn-sm">
                            <i class="feather icon-plus"></i> Add Payment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('payments._table', ['payments' => $payments])
                </div>
            </div>
        </div>
    </div>
@endsection