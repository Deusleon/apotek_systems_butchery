@extends("layouts.master")

@section('page_css')
    <link rel="stylesheet" href="{{asset('css/datatables.min.css')}}">
@endsection

@section('content-title')
    Vehicle Management
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Vehicles </a></li>
@endsection

@section("content")

@if(Session::has('error'))
        <div class="alert alert-danger alert-top-right mx-auto" style="width: 70%">
        {{ Session::get('error') }}
        </div>
    @endif
    @if(Session::has('alert-success'))
        <div class="alert alert-success alert-top-right mx-auto" style="width: 70%">
        {{ Session::get('alert-success') }}
        </div>
    @endif

    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                        data-toggle="modal" data-target="#createVehicle">
                    Add Vehicle
                </button>

                <div class="table-responsive">
                    <table id="vehicles-table" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Plate No.</th>
                            <th>Transporter</th>
                            <th>Type</th>
                            <th>Capacity</th>
                            <th>Model</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th hidden>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td>{{$vehicle->plate_number}}</td>
                                <td>{{$vehicle->transporter->name ?? 'N/A'}}</td>
                                <td>{{ucfirst($vehicle->vehicle_type)}}</td>
                                <td>{{ number_format($vehicle->capacity, 2) }} (tons)</td>
                                <td>{{$vehicle->make}} {{$vehicle->model}}</td>
                                <td>
                                    @if ($vehicle->status == 'active')
                                        <span class='badge badge-success'>Active</span>
                                    @elseif($vehicle->status == 'maintenance')
                                        <span class='badge badge-warning'>Maintenance</span>
                                    @else
                                        <span class='badge badge-danger'>{{ucfirst($vehicle->status)}}</span>
                                    @endif
                                </td>
                                <td style='white-space: nowrap'>
                                    <button class="btn btn-success btn-sm btn-rounded btn-show"
                                            data-id="{{$vehicle->id}}"
                                            data-plate_number="{{$vehicle->plate_number}}"
                                            data-transporter_id="{{$vehicle->transporter_id}}"
                                            data-transporter_name="{{$vehicle->transporter->name ?? 'N/A'}}"
                                            data-vehicle_type="{{$vehicle->vehicle_type}}"
                                            data-capacity="{{$vehicle->capacity}}"
                                            data-make="{{$vehicle->make}}"
                                            data-model="{{$vehicle->model}}"
                                            data-year="{{$vehicle->year}}"
                                            data-chassis_number="{{$vehicle->chassis_number}}"
                                            data-engine_number="{{$vehicle->engine_number}}"
                                            type="button" data-toggle="modal" data-target="#showVehicle">
                                        Show
                                    </button>
                                    <button class="btn btn-primary btn-sm btn-rounded btn-edit"
                                            data-id="{{ $vehicle->id }}"
                                            data-plate_number="{{ $vehicle->plate_number }}"
                                            data-transporter_id="{{ $vehicle->transporter_id }}"
                                            data-vehicle_type="{{ $vehicle->vehicle_type }}"
                                            data-capacity="{{ $vehicle->capacity }}"
                                            data-make="{{ $vehicle->make }}"
                                            data-model="{{ $vehicle->model }}"
                                            data-year="{{ $vehicle->year }}"
                                            data-color="{{ $vehicle->color }}"
                                            data-chassis_number="{{ $vehicle->chassis_number }}"
                                            data-engine_number="{{ $vehicle->engine_number }}"
                                            data-status="{{ $vehicle->status }}"
                                            data-fitness_expiry="{{ optional($vehicle->fitness_expiry)->format('Y-m-d') }}"
                                            data-insurance_expiry="{{ optional($vehicle->insurance_expiry)->format('Y-m-d') }}"
                                            data-permit_expiry="{{ optional($vehicle->permit_expiry)->format('Y-m-d') }}"
                                            data-notes="{{ $vehicle->notes }}"
                                            data-documents="{{ json_encode($vehicle->documents) }}"
                                            type="button" 
                                            data-toggle="modal" 
                                            data-target="#editVehicle">
                                        Edit
                                    </button>
                                </td>
                                <td hidden>{{ $vehicle->created_at ?? '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('vehicles.create')
    @include('vehicles.show')
    @include('vehicles.edit')

@endsection

@push("page_scripts")
    <script src="{{asset('js/datatables.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#vehicles-table').DataTable({
                order: [[7, 'desc']],
            });

            // Fade out alerts after 3 seconds (3000ms)
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);

            // Handle show button click
            document.querySelectorAll('.btn-show').forEach(button => {
    button.addEventListener('click', function() {
        // Populate modal from data attributes
        document.getElementById('show_plate_number').value = this.getAttribute('data-plate_number');
        document.getElementById('show_transporter_name').value = this.getAttribute('data-transporter_name');
        document.getElementById('show_vehicle_type').value = this.getAttribute('data-vehicle_type');
        document.getElementById('show_capacity').value = this.getAttribute('data-capacity');
        document.getElementById('show_make').value = this.getAttribute('data-make');
        document.getElementById('show_model').value = this.getAttribute('data-model');
        document.getElementById('show_year').value = this.getAttribute('data-year');
        document.getElementById('show_chassis_number').value = this.getAttribute('data-chassis_number');
        document.getElementById('show_engine_number').value = this.getAttribute('data-engine_number');
        // document.getElementById('show_updated_at').value = this.getAttribute('data-updated_at');
        
        // Set status badge - keep this as innerHTML since it's a div with badge
        // const status = this.getAttribute('data-status');
        // let statusBadge = '';
        // if(status == 'active') {
        //     statusBadge = '<span class="badge badge-success">Active</span>';
        // } else if(status == 'maintenance') {
        //     statusBadge = '<span class="badge badge-warning">Maintenance</span>';
        // } else {
        //     statusBadge = '<span class="badge badge-danger">'+status.charAt(0).toUpperCase() + status.slice(1)+'</span>';
        // }
        // document.getElementById('show_status').innerHTML = statusBadge;
        
        // // For textarea
        // document.getElementById('show_notes').value = this.getAttribute('data-notes') || 'N/A';
    });
});

$(document).ready(function() {
    // Handle edit button click
    $(document).on('click', '.btn-edit', function() {
        const vehicleId = $(this).data('id');
        
        // Set the form action URL
        $('#form_vehicle_edit').attr('action', `/vehicles/${vehicleId}`);
        
        // Populate all form fields from data attributes
        $('#vehicle_id').val(vehicleId);
        $('#plate_number_edit').val($(this).data('plate_number'));
        $('#transporter_id_edit').val($(this).data('transporter_id'));
        $('#vehicle_type_edit').val($(this).data('vehicle_type'));
        $('#capacity_edit').val($(this).data('capacity'));
        $('#make_edit').val($(this).data('make'));
        $('#model_edit').val($(this).data('model'));
        $('#year_edit').val($(this).data('year'));
        $('#color_edit').val($(this).data('color'));
        $('#chassis_number_edit').val($(this).data('chassis_number'));
        $('#engine_number_edit').val($(this).data('engine_number'));
        $('#status_edit').val($(this).data('status'));
        $('#notes_edit').val($(this).data('notes'));
        
        // Date fields
        $('#fitness_expiry_edit').val($(this).data('fitness_expiry'));
        $('#insurance_expiry_edit').val($(this).data('insurance_expiry'));
        $('#permit_expiry_edit').val($(this).data('permit_expiry'));
        
        // Handle documents
        const documents = $(this).data('documents');
        const documentsContainer = $('#existing-documents');
        documentsContainer.empty();
        
        if (documents && documents.length > 0) {
            $('#existing-documents-container').show();
            documents.forEach(doc => {
                documentsContainer.append(`
                    <div class="document-thumbnail mr-2 mb-2">
                        <a href="/storage/${doc.path}" target="_blank">
                            ${doc.type.startsWith('image') ? 
                                `<img src="/storage/${doc.path}" width="100">` : 
                                `<div class="document-icon"><i class="far fa-file-alt fa-3x"></i></div>`}
                            <small class="d-block">${doc.name}</small>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger delete-document" data-id="${doc.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `);
            });
        } else {
            $('#existing-documents-container').hide();
        }
    });

    // Handle document deletion
    $(document).on('click', '.delete-document', function() {
        const documentId = $(this).data('id');
        if (confirm('Are you sure you want to delete this document?')) {
            $.ajax({
                url: `/documents/${documentId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $(this).closest('.document-thumbnail').remove();
                    }
                }.bind(this)
            });
        }
    });
});
            // // Handle edit button click
            // document.querySelectorAll('.btn-edit').forEach(button => {
            //     button.addEventListener('click', function() {
            //         const vehicleId = this.getAttribute('data-id');
                    
            //         // Set the form action URL
            //         const form = document.getElementById('form_vehicle_edit');
            //         form.action = `/vehicles/${vehicleId}`;
                    
            //         // Populate form fields from data attributes
            //         document.getElementById('vehicle_id').value = vehicleId;
            //         document.getElementById('plate_number_edit').value = this.getAttribute('data-plate_number');
            //         document.getElementById('transporter_id_edit').value = this.getAttribute('data-transporter_id');
            //         document.getElementById('vehicle_type_edit').value = this.getAttribute('data-vehicle_type');
            //         document.getElementById('capacity_edit').value = this.getAttribute('data-capacity');
            //         document.getElementById('make_edit').value = this.getAttribute('data-make');
            //         document.getElementById('model_edit').value = this.getAttribute('data-model');
            //         document.getElementById('year_edit').value = this.getAttribute('data-year');
            //         document.getElementById('color_edit').value = this.getAttribute('data-color');
            //         document.getElementById('status_edit').value = this.getAttribute('data-status');
            //         document.getElementById('color_edit').value = this.getAttribute('data-color');
            //         document.getElementById('chassis_number').value = this.getAttribute('data-chassis_number');
            //         document.getElementById('show_engine_number').value = this.getAttribute('data-engine_number');
            //         document.getElementById('status_edit').value = this.getAttribute('data-status');
            //         document.getElementById('notes_edit').value = this.getAttribute('data-notes') || '';
            //     });
            // });
        });
    </script>
@endpush