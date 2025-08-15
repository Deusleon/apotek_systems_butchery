@extends("layouts.master")

@section('page_css')
@endsection

@section('content-title')
    Order Payments
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Payments</a></li>
@endsection

@section("content")
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
            <div class="card-body">
                <div class="table-responsive">
                    <table id="payments-table" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Transporter</th>
                            <th>Total Rate</th>
                            <th>Advance Payment</th>
                            <th>Balance</th>
                            <th>Payment Method</th>
                            <th>Pickup Location</th>
                            <th>Delivery Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th hidden>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transportOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->transporter->name }}</td>
                                <td><strong>{{ number_format($order->transport_rate) }}</strong></td>
                                <td><span class="text-success">{{ number_format($order->advance_payment ?? 0) }}</span></td>
                                <td>
                                    <span class="text-danger">
                                        {{ number_format(($order->transport_rate ?? 0) - ($order->advance_payment ?? 0)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ \App\TransportOrder::paymentMethods()[$order->payment_method] ?? $order->payment_method }}
                                </td>
                                <td>{{ \App\TransportOrder::pickupLocations()[$order->pickup_location] ?? $order->pickup_location }}</td>
                                <td>{{ \App\TransportOrder::deliveryLocations()[$order->delivery_location] ?? $order->delivery_location }}</td>
                                <td>
                                    @if($order->status == 'draft')
                                        <span class="badge badge-secondary">Draft</span>
                                    @elseif($order->status == 'confirmed')
                                        <span class="badge badge-primary">Confirmed</span>
                                    @elseif($order->status == 'dispatched')
                                        <span class="badge badge-info">Dispatched</span>
                                    @elseif($order->status == 'in_transit')
                                        <span class="badge badge-warning">In Transit</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge badge-success">Delivered</span>
                                    @elseif($order->status == 'closed')
                                        <span class="badge badge-dark">Closed</span>
                                    @endif
                                </td>
                                <td style='white-space: nowrap'>
                                    <a href="{{ route('transport-orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                        View Order
                                    </a>
                                </td>
                                <td hidden>{{ $order->created_at ?? '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('payments.edit')
    @include('payments.partials.show-modal')
@endsection

@push("page_scripts")
    @include('partials.notification')

    <script>
        $(document).ready(function () {
            $('#payments-table').DataTable({
                order: [[10, 'desc']],
            });

            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush
