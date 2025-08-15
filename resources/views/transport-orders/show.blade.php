@extends("layouts.master")

@section('content-title')
    Transport Order Details
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}"> Transport Orders </a></li>
    <li class="breadcrumb-item"><a href="#"> Details </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Transport Order #{{ $transportOrder->order_number }}</h5>
                <div class="float-right">
                    <a href="{{ route('transport-orders.edit', $transportOrder->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    <a href="{{ route('transport-orders.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <hr>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Order Number</th>
                                <td>{{ $transportOrder->order_number }}</td>
                            </tr>
                            <tr>
                                <th>Transporter</th>
                                <td>{{ $transportOrder->transporter->name }}</td>
                            </tr>
                            <tr>
                                <th>Pickup Location</th>
                                <td>{{ $transportOrder->pickupLocation->name }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Location</th>
                                <td>{{ $transportOrder->deliveryLocation->name }}</td>
                            </tr>
                            <tr>
                                <th>Pickup Date & Time</th>
                                <td>{{ $transportOrder->pickup_date->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Delivery Date</th>
                                <td>{{ $transportOrder->delivery_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <td>{{ $transportOrder->product->name }} ({{ $transportOrder->product->pack_size }})</td>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <td>{{ $transportOrder->quantity }} {{ $transportOrder->unit }}</td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td>
                                    @if($transportOrder->priority == 'normal')
                                        <span class="badge badge-primary">Normal</span>
                                    @elseif($transportOrder->priority == 'urgent')
                                        <span class="badge badge-warning">Urgent</span>
                                    @elseif($transportOrder->priority == 'very_urgent')
                                        <span class="badge badge-danger">Very Urgent</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Transport Details</h5>
                        <hr>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Assigned Vehicle</th>
                                <td>
                                    @if($transportOrder->assignedVehicle)
                                        {{ $transportOrder->assignedVehicle->registration_number }} ({{ $transportOrder->assignedVehicle->vehicle_type }})
                                    @else
                                        Not assigned
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Transport Rate</th>
                                <td>{{ number_format($transportOrder->transport_rate, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Advance Payment</th>
                                <td>{{ number_format($transportOrder->advance_payment, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Payment Method</th>
                                <td>
                                    @if($transportOrder->payment_method)
                                        {{ TransportOrder::paymentMethods()[$transportOrder->payment_method] }}
                                    @else
                                        Not specified
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Order Status</th>
                                <td>
                                    @if($transportOrder->status == 'draft')
                                        <span class="badge badge-secondary">Draft</span>
                                    @elseif($transportOrder->status == 'confirmed')
                                        <span class="badge badge-primary">Confirmed</span>
                                    @elseif($transportOrder->status == 'dispatched')
                                        <span class="badge badge-info">Dispatched</span>
                                    @elseif($transportOrder->status == 'in_transit')
                                        <span class="badge badge-warning">In Transit</span>
                                    @elseif($transportOrder->status == 'delivered')
                                        <span class="badge badge-success">Delivered</span>
                                    @elseif($transportOrder->status == 'closed')
                                        <span class="badge badge-dark">Closed</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $transportOrder->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td>{{ $transportOrder->updated_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        </table>
                        
                        <h5 class="mt-4">Notes</h5>
                        <hr>
                        <div class="border p-3">
                            {{ $transportOrder->notes ?? 'No notes available' }}
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h5>Proof of Delivery Documents</h5>
                        <hr>
                        @if($transportOrder->documents->count() > 0)
                            <div class="row">
                                @foreach($transportOrder->documents as $document)
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <div class="card-bod