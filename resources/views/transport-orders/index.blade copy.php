@extends("layouts.master")

@section('page_css')
@endsection

@section('content-title')
    Orders
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Orders </a></li>
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
                <a href="{{ route('transport-orders.create') }}" style="float: right;margin-bottom: 2%;" class="btn btn-secondary btn-sm">
                    Create Order
                </a>

                <div class="table-responsive">
                    <table id="fixed-header-transport-orders" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Transporter</th>
                            <th>Advance Amount</th>
                            <th>Pickup Location</th>
                            <th>Delivery Location</th>
                            <th>Pickup Date</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Priority</th>
                            <th>Actions</th>
                            <th hidden>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transportOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->transporter->name }}</td>
                                <td>{{ $order->advance_payment }}</td>
                                <td>{{ \App\TransportOrder::pickupLocations()[$order->pickup_location] ?? $order->pickup_location }}</td>
                                <td>{{ \App\TransportOrder::deliveryLocations()[$order->delivery_location] ?? $order->delivery_location }}</td>
                                <td>{{ $order->pickup_date->format('Y-m-d H:i') }}</td>
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
                                <td>
                                    @if($order->payments->sum('amount') >= $order->transport_rate)
                                        <span class="badge badge-success">Completed</span>
                                    @else
                                        <span class="badge badge-warning">Not Completed</span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->priority == 'normal')
                                        <span class="badge badge-primary">Normal</span>
                                    @elseif($order->priority == 'urgent')
                                        <span class="badge badge-warning">Urgent</span>
                                    @elseif($order->priority == 'very_urgent')
                                        <span class="badge badge-danger">Very Urgent</span>
                                    @endif
                                </td>
                                <td style='white-space: nowrap'>
                                    <a href="#">
                                        <button class="btn btn-success btn-sm btn-rounded btn-show"
                                                data-id="{{$order->id}}"
                                                data-order_number="{{$order->order_number}}"
                                                data-transporter_id="{{$order->transporter_id}}"
                                                data-transporter_name="{{$order->transporter->name}}"
                                                data-advance_payment="{{$order->advance_payment}}"
                                                data-transport_rate="{{$order->transport_rate}}"
                                                data-pickup_location="{{$order->pickup_location}}"
                                                data-delivery_location="{{$order->delivery_location}}"
                                                data-pickup_date="{{$order->pickup_date->format('Y-m-d H:i')}}"
                                                data-delivery_date="{{$order->delivery_date ? $order->delivery_date->format('Y-m-d H:i') : ''}}"
                                                data-status="{{$order->status}}"
                                                data-priority="{{$order->priority}}"
                                                data-notes="{{$order->notes}}"
                                                type="button" data-toggle="modal" data-target="#showOrder">Show
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button class="btn btn-primary btn-sm btn-rounded btn-edit"
                                                data-id="{{$order->id}}"
                                                data-order_number="{{$order->order_number}}"
                                                data-transporter_id="{{$order->transporter_id}}"
                                                data-advance_payment="{{$order->advance_payment}}"
                                                data-transport_rate="{{$order->transport_rate}}"
                                                data-pickup_location="{{$order->pickup_location}}"
                                                data-delivery_location="{{$order->delivery_location}}"
                                                data-pickup_date="{{$order->pickup_date->format('Y-m-d H:i')}}"
                                                data-delivery_date="{{$order->delivery_date ? $order->delivery_date->format('Y-m-d H:i') : ''}}"
                                                data-status="{{$order->status}}"
                                                data-priority="{{$order->priority}}"
                                                data-notes="{{$order->notes}}"
                                                type="button" data-toggle="modal" data-target="#editOrder">Edit
                                        </button>
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

    <!-- Show Order Modal -->
  <!-- Show Order Modal -->
<div class="modal fade" id="showOrder" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Transport Order Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Order Number:</label>
                            <input type="text" class="form-control" id="show_order_number" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Transporter:</label>
                            <input type="text" class="form-control" id="show_transporter_name" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Advance Payment:</label>
                            <input type="text" class="form-control" id="show_advance_payment" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Transport Rate:</label>
                            <input type="text" class="form-control" id="show_transport_rate" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pickup Location:</label>
                            <input type="text" class="form-control" id="show_pickup_location" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Delivery Location:</label>
                            <input type="text" class="form-control" id="show_delivery_location" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Pickup Date:</label>
                            <input type="text" class="form-control" id="show_pickup_date" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Delivery Date:</label>
                            <input type="text" class="form-control" id="show_delivery_date" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status:</label>
                            <div id="show_status" class="pt-2"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Priority:</label>
                            <div id="show_priority" class="pt-2"></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Notes:</label>
                            <textarea class="form-control" id="show_notes" rows="3" disabled></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

   <!-- Edit Order Modal -->
<div class="modal fade" id="editOrder" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header bg-white text-white">
                <h5 class="modal-title">Update Transport Order</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="form_order_edit" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="order_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Order Number</label>
                                <input type="text" class="form-control" name="order_number" id="order_number_edit" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Transporter</label>
                                <select class="form-control" name="transporter_id" id="transporter_id_edit" required>
                                    @foreach($transporters as $transporter)
                                        <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Advance Payment</label>
                                <input type="number" class="form-control" name="advance_payment" id="advance_payment_edit" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Transport Rate</label>
                                <input type="number" class="form-control" name="transport_rate" id="transport_rate_edit" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pickup Location</label>
                                <select class="form-control" name="pickup_location" id="pickup_location_edit" required>
                                    @foreach(\App\TransportOrder::pickupLocations() as $key => $location)
                                        <option value="{{ $key }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Delivery Location</label>
                                <select class="form-control" name="delivery_location" id="delivery_location_edit" required>
                                    @foreach(\App\TransportOrder::deliveryLocations() as $key => $location)
                                        <option value="{{ $key }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pickup Date</label>
                                <input type="datetime-local" class="form-control" name="pickup_date" id="pickup_date_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Delivery Date</label>
                                <input type="datetime-local" class="form-control" name="delivery_date" id="delivery_date_edit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status" id="status_edit" required>
                                    <option value="draft">Draft</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="dispatched">Dispatched</option>
                                    <option value="in_transit">In Transit</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority</label>
                                <select class="form-control" name="priority" id="priority_edit" required>
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="very_urgent">Very Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" id="notes_edit" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push("page_scripts")
    @include('partials.notification')

    <script src="{{asset('js/datatables.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#fixed-header-transport-orders').DataTable({
                order: [[10, 'desc']], // Sorts by the hidden created_at column
            });

            // Fade out alerts after 3 seconds (3000ms)
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });

        // Handle show button click
        document.querySelectorAll('.btn-show').forEach(button => {
            button.addEventListener('click', function() {
                // Populate modal from data attributes
                document.getElementById('show_order_number').value = this.getAttribute('data-order_number');
                document.getElementById('show_transporter_name').value = this.getAttribute('data-transporter_name');
                document.getElementById('show_advance_payment').value = this.getAttribute('data-advance_payment');
                document.getElementById('show_transport_rate').value = this.getAttribute('data-transport_rate');
                
                // Set locations using the predefined locations array
                const pickupLocations = @json(\App\TransportOrder::pickupLocations());
                const deliveryLocations = @json(\App\TransportOrder::deliveryLocations());
                
                const pickupLocationKey = this.getAttribute('data-pickup_location');
                const deliveryLocationKey = this.getAttribute('data-delivery_location');
                
                document.getElementById('show_pickup_location').value = pickupLocations[pickupLocationKey] || pickupLocationKey;
                document.getElementById('show_delivery_location').value = deliveryLocations[deliveryLocationKey] || deliveryLocationKey;
                
                document.getElementById('show_pickup_date').value = this.getAttribute('data-pickup_date');
                document.getElementById('show_delivery_date').value = this.getAttribute('data-delivery_date') || 'Not set';
                
                // Set status badge
                const status = this.getAttribute('data-status');
                let statusBadge = '';
                if(status == 'draft') {
                    statusBadge = '<span class="badge badge-secondary">Draft</span>';
                } else if(status == 'confirmed') {
                    statusBadge = '<span class="badge badge-primary">Confirmed</span>';
                } else if(status == 'dispatched') {
                    statusBadge = '<span class="badge badge-info">Dispatched</span>';
                } else if(status == 'in_transit') {
                    statusBadge = '<span class="badge badge-warning">In Transit</span>';
                } else if(status == 'delivered') {
                    statusBadge = '<span class="badge badge-success">Delivered</span>';
                } else if(status == 'closed') {
                    statusBadge = '<span class="badge badge-dark">Closed</span>';
                }
                document.getElementById('show_status').innerHTML = statusBadge;
                
                // Set priority badge
                const priority = this.getAttribute('data-priority');
                let priorityBadge = '';
                if(priority == 'normal') {
                    priorityBadge = '<span class="badge badge-primary">Normal</span>';
                } else if(priority == 'urgent') {
                    priorityBadge = '<span class="badge badge-warning">Urgent</span>';
                } else if(priority == 'very_urgent') {
                    priorityBadge = '<span class="badge badge-danger">Very Urgent</span>';
                }
                document.getElementById('show_priority').innerHTML = priorityBadge;
                
                document.getElementById('show_notes').value = this.getAttribute('data-notes') || 'N/A';
                
                $('#showOrder').modal('show');
            });
        });

        // Handle edit button click
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                
                // Set the form action URL
                const form = document.getElementById('form_order_edit');
                form.action = `/transport-orders/${orderId}`;
                
                // Populate form fields from data attributes
                document.getElementById('order_id').value = orderId;
                document.getElementById('order_number_edit').value = this.getAttribute('data-order_number');
                document.getElementById('transporter_id_edit').value = this.getAttribute('data-transporter_id');
                document.getElementById('advance_payment_edit').value = this.getAttribute('data-advance_payment');
                document.getElementById('transport_rate_edit').value = this.getAttribute('data-transport_rate');
                document.getElementById('pickup_location_edit').value = this.getAttribute('data-pickup_location');
                document.getElementById('delivery_location_edit').value = this.getAttribute('data-delivery_location');
                
                // Format dates for datetime-local input
                const pickupDate = this.getAttribute('data-pickup_date');
                document.getElementById('pickup_date_edit').value = pickupDate.replace(' ', 'T');
                
                const deliveryDate = this.getAttribute('data-delivery_date');
                if(deliveryDate) {
                    document.getElementById('delivery_date_edit').value = deliveryDate.replace(' ', 'T');
                }
                
                document.getElementById('status_edit').value = this.getAttribute('data-status');
                document.getElementById('priority_edit').value = this.getAttribute('data-priority');
                document.getElementById('notes_edit').value = this.getAttribute('data-notes') || '';
                
                $('#editOrder').modal('show');
            });
        });
    </script>
@endpush