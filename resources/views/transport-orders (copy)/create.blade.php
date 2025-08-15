@extends("layouts.master")

@section('content-title')
    Create Transport Order
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}"> Transport Orders </a></li>
    <li class="breadcrumb-item"><a href="#"> Create </a></li>
@endsection

@section("content")
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>New Transport Order</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('transport-orders.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-section">
                                <h5 class="section-title"><i class="feather icon-truck"></i> Order Details</h5>
                                
                                <div class="form-group">
                                    <label for="transporter_id">Transporter <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="transporter_id" required>
                                        <option value="">Select Transporter</option>
                                        @foreach($transporters as $transporter)
                                            <option value="{{ $transporter->id }}" {{ old('transporter_id') == $transporter->id ? 'selected' : '' }}>
                                                {{ $transporter->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pickup_location">Pickup Location <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="pickup_location" 
                                                   value="{{ old('pickup_location') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="delivery_location">Delivery Location <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="delivery_location" 
                                                   value="{{ old('delivery_location') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="pickup_date">Pickup Date & Time <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" name="pickup_date" 
                                                   value="{{ old('pickup_date') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="delivery_date">Delivery Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="delivery_date" 
                                                   value="{{ old('delivery_date') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="product">Product <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="product" 
                                           value="{{ old('product') }}" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" name="quantity" 
                                                       value="{{ old('quantity') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="unit-display">kg</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="unit">Unit <span class="text-danger">*</span></label>
                                            <select class="form-control" name="unit" id="unit-selector" required>
                                                @foreach(['tons' => 'Tons', 'bags' => 'Bags', 'kg' => 'Kilograms', 'units' => 'Units'] as $value => $label)
                                                    <option value="{{ $value }}" {{ old('unit') == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control" name="priority" required>
                                        @foreach(['normal' => 'Normal', 'urgent' => 'Urgent', 'very_urgent' => 'Very Urgent'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-section">
                                <h5 class="section-title"><i class="feather icon-dollar-sign"></i> Transport Details</h5>
                                
                                <div class="form-group">
                                    <label for="assigned_vehicle_id">Assigned Vehicle</label>
                                    <select class="form-control select2" name="assigned_vehicle_id">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('assigned_vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->registration_number }} ({{ $vehicle->vehicle_type }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="transport_rate">Transport Rate (Total Charges) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" name="transport_rate" 
                                               value="{{ old('transport_rate') }}" required>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="advance_payment">Advance Payment</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" name="advance_payment" 
                                               value="{{ old('advance_payment', '0.00') }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select class="form-control" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money', 'cheque' => 'Cheque'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Order Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        @foreach(['draft' => 'Draft', 'confirmed' => 'Confirmed', 'dispatched' => 'Dispatched', 'in_transit' => 'In Transit', 'delivered' => 'Delivered', 'closed' => 'Closed'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('status', 'draft') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-save"></i> Create Transport Order
                            </button>
                            <a href="{{ route('transport-orders.index') }}" class="btn btn-outline-secondary">
                                <i class="feather icon-x"></i> Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .section-title {
            color: #5a5a5a;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                width: '100%'
            });
            
            // Update unit display when unit selector changes
            $('#unit-selector').change(function() {
                $('#unit-display').text($(this).find('option:selected').text().toLowerCase());
            }).trigger('change');
        });
    </script>
@endpush