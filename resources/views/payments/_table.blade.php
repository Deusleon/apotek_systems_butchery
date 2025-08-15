<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="thead-light">
            <tr>
                <th>Date</th>
                <th>Receipt #</th>
                <th>Order #</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                <td>{{ $payment->receipt_number }}</td>
                <td>
                    @isset($payment->transportOrder)
                        <a href="{{ route('transport-orders.show', $payment->transportOrder) }}">
                            #{{ $payment->transportOrder->order_number }}
                        </a>
                    @else
                        N/A
                    @endisset
                </td>
                <td>{{ number_format($payment->amount, 2) }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                <td>{{ ucfirst($payment->payment_type) }}</td>
                <td>
                    <span class="badge badge-{{ $payment->status_badge_class }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </td>
                <td>
                    @isset($payment->transportOrder)
                        <a href="{{ route('transport-orders.payments.edit', [$payment->transportOrder, $payment]) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="feather icon-edit"></i>
                        </a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endisset
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if(method_exists($payments, 'links'))
        <div class="d-flex justify-content-center mt-3">
            {{ $payments->links() }}
        </div>
    @endif
</div>