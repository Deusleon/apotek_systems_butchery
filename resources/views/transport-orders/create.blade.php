@extends("layouts.master")

@section('content-title')
Create Order
@endsection

@section('content-sub-title')
<li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
<li class="breadcrumb-item"><a href="{{ route('transport-orders.index') }}">Orders </a></li>
<li class="breadcrumb-item"><a href="#"> Create </a></li>
@endsection

@section("content")
<div class="col-sm-12">
    <div class="card">
        <div class="card-header">
            <h5>New Order</h5>
        </div>
        <div class="card-body">

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <form method="POST" action="{{ route('transport-orders.store') }}" enctype="multipart/form-data">
                @csrf

                <!-- Order Items -->
                <div id="order-items">
                    <div class="order-item card mb-3">
                        <div class="card-body">
                            <!-- First Row (4 fields) -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label>Order Type <span class="text-danger">*</span></label>
                                    <select class="form-control item-order-type" name="order_type" required>
                                        <option value="">Select Order Type</option>
                                        <option value="factory" {{ old('order_type', 'factory') == 'factory' ? 'selected' : '' }}>Factory</option>
                                        <option value="inter_branch" {{ old('order_type') == 'inter_branch' ? 'selected' : '' }}>Inter-Branch</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label>Transporter <span class="text-danger">*</span></label>
                                    <select name="transporters[]" class="form-control" required>
                                        <option value="">Select Transporter</option>
                                        @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}">{{ $transporter->name }}|{{ $transporter->agreed_routes }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Vehicle</label>
                                    <select name="vehicles[]" class="form-control">
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_type }}|pt.No {{ $vehicle->plate_number }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Product <span class="text-danger">*</span></label>
                                    <select name="products[]" class="form-control" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Second Row (4 fields) -->
                            <div class="row">
                                <div class="col-md-3 pickup-location-group">
                                    <label>Pickup Location <span class="text-danger">*</span></label>
                                    <!-- Factory (Suppliers) -->
                                    <select name="pickup_suppliers[]" class="form-control pickup-supplier">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <!-- Inter-Branch (Stores) -->
                                    <select name="pickup_stores[]" class="form-control pickup-store d-none" disabled>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->name }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Delivery Location<span class="text-danger">*</span></label>
                                    <select name="deliveries[]" class="form-control" required>
                                        <option value="">Select Store</option>
                                        @foreach($stores as $store)
                                        <option value="{{ $store->name }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label>Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantities[]" class="form-control" min="1" required>
                                </div>

                                <div class="col-md-3">
                                    <label>Unit <span class="text-danger">*</span></label>
                                    <select name="units[]" class="form-control" required>
                                        @foreach(['tons' => 'Tons', 'bags' => 'Bags', 'kg' => 'Kilograms', 'units' => 'Units'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" id="add-item">Add Another Item</button>

                <hr>

                <!-- Order Details -->
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="pickup_date">Pickup Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" name="pickup_date" value="{{ old('pickup_date') }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="delivery_date">Delivery Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="delivery_date" value="{{ old('delivery_date') }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="priority">Priority <span class="text-danger">*</span></label>
                        <select class="form-control" name="priority" required>
                            @foreach(['normal' => 'Normal', 'urgent' => 'Urgent', 'very_urgent' => 'Very Urgent'] as $value => $label)
                            <option value="{{ $value }}" {{ old('priority') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="status">Order Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            @foreach(['draft' => 'Loading', 'confirmed' => 'In Transit', 'dispatched' => 'Offloading','delivered' => 'Completed', 'closed' => 'Closed'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', 'draft') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="transport_rate">Transport Cost <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="transport_rate" value="{{ old('transport_rate') }}" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="advance_payment">Advance Payment</label>
                        <input type="number" step="0.01" class="form-control" name="advance_payment" value="{{ old('advance_payment', '0.00') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" name="payment_method">
                            <option value="">Select Payment Method</option>
                            @foreach(['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'mobile_money' => 'Mobile Money', 'cheque' => 'Cheque'] as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="attachments">Attachments (Optional)</label>
                        <input type="file" class="form-control" name="attachments[]" multiple>
                        <small class="form-text text-muted">You can select multiple files.</small>
                    </div>
                </div>

            

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Enter any additional information...">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>

            <!-- JavaScript -->
            <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to toggle pickup fields based on order type
        function togglePickupFields(item) {
            const orderTypeSelect = item.querySelector('.item-order-type');
            if (!orderTypeSelect) return;
            
            const orderType = orderTypeSelect.value;
            const supplier = item.querySelector('.pickup-supplier');
            const store = item.querySelector('.pickup-store');

            if (orderType === 'factory') {
                supplier.classList.remove('d-none');
                supplier.disabled = false;
                store.classList.add('d-none');
                store.disabled = true;
                store.value = '';
            } else if (orderType === 'inter_branch') {
                store.classList.remove('d-none');
                store.disabled = false;
                supplier.classList.add('d-none');
                supplier.disabled = true;
                supplier.value = '';
            } else {
                supplier.classList.add('d-none');
                store.classList.add('d-none');
                supplier.disabled = true;
                store.disabled = true;
            }
        }

        // Add new item
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('order-items');
            const firstItem = container.querySelector('.order-item');
            const clone = firstItem.cloneNode(true);
            
            // Clear all values in the cloned item
            clone.querySelectorAll('input').forEach(el => el.value = '');
            clone.querySelectorAll('select').forEach(el => {
                el.selectedIndex = 0;
                // Remove any error classes that might have been copied
                el.classList.remove('is-invalid');
            });
            
            // Reset the pickup location fields
            const orderTypeSelect = clone.querySelector('.item-order-type');
            const supplier = clone.querySelector('.pickup-supplier');
            const store = clone.querySelector('.pickup-store');
            
            // Set default to factory and initialize fields
            orderTypeSelect.value = 'factory';
            supplier.classList.remove('d-none');
            supplier.disabled = false;
            store.classList.add('d-none');
            store.disabled = true;
            store.value = '';
            
            container.appendChild(clone);
            
            // Add event listener for the new order type select
            orderTypeSelect.addEventListener('change', function() {
                togglePickupFields(this.closest('.order-item'));
            });
        });

        // Remove item
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const items = document.querySelectorAll('.order-item');
                if (items.length > 1) {
                    e.target.closest('.order-item').remove();
                } else {
                    alert('At least one order item is required.');
                }
            }
        });

        // Initialize all existing items
        document.querySelectorAll('.order-item').forEach(item => {
            togglePickupFields(item);
            
            // Add event listeners for order type changes
            const orderTypeSelect = item.querySelector('.item-order-type');
            if (orderTypeSelect) {
                orderTypeSelect.addEventListener('change', function() {
                    togglePickupFields(item);
                });
            }
        });
    });
</script>
            <!-- <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Function to toggle pickup fields based on order type
                    function togglePickupFields(item) {
                        const orderTypeSelect = item.querySelector('.item-order-type');
                        const supplier = item.querySelector('.pickup-supplier');
                        const store = item.querySelector('.pickup-store');
                        
                        if (!orderTypeSelect) return;
                        
                        const orderType = orderTypeSelect.value;

                        if (orderType === 'factory') {
                            supplier.classList.remove('d-none');
                            supplier.disabled = false;
                            store.classList.add('d-none');
                            store.disabled = true;
                            store.value = '';
                        } else if (orderType === 'inter_branch') {
                            store.classList.remove('d-none');
                            store.disabled = false;
                            supplier.classList.add('d-none');
                            supplier.disabled = true;
                            supplier.value = '';
                        } else {
                            supplier.classList.add('d-none');
                            store.classList.add('d-none');
                            supplier.disabled = true;
                            store.disabled = true;
                        }
                    }

                    // Add event listener for order type change on all items
                    function setupOrderTypeListeners() {
                        document.querySelectorAll('.item-order-type').forEach(select => {
                            select.addEventListener('change', function() {
                                togglePickupFields(this.closest('.order-item'));
                            });
                        });
                    }

                    // Add new item
                    document.getElementById('add-item').addEventListener('click', function() {
                        const container = document.getElementById('order-items');
                        const firstItem = container.querySelector('.order-item');
                        const clone = firstItem.cloneNode(true);
                        
                        // Clear all values in the cloned item
                        clone.querySelectorAll('input, select').forEach(el => {
                            el.value = '';
                            if (el.tagName === 'SELECT') el.selectedIndex = 0;
                        });
                        
                        container.appendChild(clone);
                        
                        // Setup event listeners for the new item
                        setupOrderTypeListeners();
                        
                        // Initialize the pickup fields for the new item
                        togglePickupFields(clone);
                    });

                    // Remove item
                    document.addEventListener('click', function(e) {
                        if (e.target.classList.contains('remove-item')) {
                            const items = document.querySelectorAll('.order-item');
                            if (items.length > 1) {
                                e.target.closest('.order-item').remove();
                            } else {
                                alert('At least one order item is required.');
                            }
                        }
                    });

                    // Initialize all existing items
                    document.querySelectorAll('.order-item').forEach(item => {
                        togglePickupFields(item);
                    });
                    
                    // Setup event listeners for initial items
                    setupOrderTypeListeners();
                });
            </script> -->

        </div>
    </div>
</div>
@endsection