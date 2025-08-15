@extends("layouts.master")

@section('content-title')
    Edit Transport Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}"> Transport Orders </a></li>
    <li class="breadcrumb-item"><a href="#"> Edit </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Transport Order #{{ $transportOrder->order_number }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('transport-orders.update', $transportOrder->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Order Details</h5>
                            <hr>
                            
                            <div class="form-group">
                                <label for="transporter_id">Transporter</label><font color="red">*</font>
                                <select class="form-control" name="transporter_id" required>
                                    <option value="">Select Transporter</option>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}" {{ $transportOrder->transporter_id == $transporter->id ? 'selected' : '' }}>{{ $transporter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="pickup_location_id">Pickup Location</label><font color="red">*</font>
                                <select class="form-control" name="pickup_location_id" required>
                                    <option value="">Select Pickup Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ $transportOrder->pickup_location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="delivery_location_id">Delivery Location</label><font color="red">*</font>
                                <select class="form-control" name="delivery_location_id" required>
                                    <option value="">Select Delivery Location</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ $transportOrder->delivery_location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="pickup_date">Pickup Date & Time</label><font color="red">*</font>
                                        <input type="datetime-local" class="form-control" name="pickup_date" value="{{ $transportOrder->pickup_date->format('Y-m-d\TH:i') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="delivery_date">Delivery Date</label><font color="red">*</font>
                                        <input type="date" class="form-control" name="delivery_date" value="{{ $transportOrder->delivery_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_id">Product</label><font color="red">*</font>
                                <select class="form-control" name="product_id" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $transportOrder->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->pack_size }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label><font color="red">*</font>
                                        <input type="number" step="0.01" class="form-control" name="quantity" value="{{ $transportOrder->quantity }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit">Unit</label><font color="red">*</font>
                                        <select class="form-control" name="unit" required>
                                            @foreach(TransportOrder::unitOptions() as $value => $label)
                                                <option value="{{ $value }}" {{ $transportOrder->unit == $value ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="priority">Priority</label><font color="red">*</font>
                                <select class="form-control" name="priority" required>
                                    @foreach(TransportOrder::priorityOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ $transportOrder->priority == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Transport Details</h5>
                            <hr>
                            
                            <div class="form-group">
                                <label for="assigned_vehicle_id">Assigned Vehicle</label>
                                <select class="form-control" name="assigned_vehicle_id">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ $transportOrder->assigned_vehicle_id == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->registration_number }} ({{ $vehicle->vehicle_type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="transport_rate">Transport Rate (Total Charges)</label><font color="red">*</font>
                                <input type="number" step="0.01" class="form-control" name="transport_rate" value="{{ $transportOrder->transport_rate }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="advance_payment">Advance Payment</label>
                                <input type="number" step="0.01" class="form-control" name="advance_payment" value="{{ $transportOrder->advance_payment }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select class="form-control" name="payment_method">
                                    <option value="">Select Payment Method</option>
                                    @foreach(TransportOrder::paymentMethods() as $value => $label)
                                        <option value="{{ $value }}" {{ $transportOrder->payment_method == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Order Status</label><font color="red">*</font>
                                <select class="form-control" name="status" required>
                                    @foreach(TransportOrder::statusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ $transportOrder->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" name="notes" rows="3">{{ $transportOrder->notes }}</textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="pod_document">Proof of Delivery Document</label>
                                <input type="file" class="form-control" name="pod_document">
                                @if($transportOrder->documents->count() > 0)
                                    <small class="text-muted">Current documents: {{ $transportOrder->documents->count() }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">Update Transport Order</button>
                            <a href="{{ route('transport-orders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection