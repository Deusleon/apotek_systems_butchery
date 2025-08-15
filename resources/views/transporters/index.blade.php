@extends("layouts.master")

@section('page_css')
@endsection

@section('content-title')
    Transporters
@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Transporters </a></li>
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
                <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                        data-toggle="modal" data-target="#register">
                    Add Transporter
                </button>

                <div class="table-responsive">
                    <table id="fixed-header-transporters" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact Person</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Vehicles</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th hidden>Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($transporters as $transporter)
                            <tr>
                                <td>{{$transporter->name}}</td>
                                <td>{{$transporter->contact_person}}</td>
                                <td>{{$transporter->phone}}</td>
                                <td>{{$transporter->email}}</td>
                                <td>{{$transporter->number_of_vehicles}}</td>
                                <td>
                                    @if ($transporter->status == 'active')
                                        <span class='badge badge-success'>Active</span>
                                    @else
                                        <span class='badge badge-danger'>Inactive</span>
                                    @endif
                                </td>
                                <td style='white-space: nowrap'>
                                    <button class="btn btn-success btn-sm btn-rounded btn-show"
                                            data-id="{{$transporter->id}}"
                                            data-name="{{$transporter->name}}"
                                            data-contact_person="{{$transporter->contact_person}}"
                                            data-phone="{{$transporter->phone}}"
                                            data-email="{{$transporter->email}}"
                                            data-number_of_vehicles="{{$transporter->number_of_vehicles}}"
                                            data-status="{{$transporter->status}}"
                                            data-notes="{{$transporter->notes}}"
                                            type="button" data-toggle="modal" data-target="#showTransporter">
                                        Show
                                    </button>
                                    <button class="btn btn-primary btn-sm btn-rounded btn-edit"
                                            data-id="{{$transporter->id}}"
                                            data-name="{{$transporter->name}}"
                                            data-contact_person="{{$transporter->contact_person}}"
                                            data-phone="{{$transporter->phone}}"
                                            data-email="{{$transporter->email}}"
                                            data-transport_type="{{$transporter->transport_type}}"
                                            data-number_of_vehicles="{{$transporter->number_of_vehicles}}"
                                            data-status="{{$transporter->status}}"
                                            data-notes="{{$transporter->notes}}"
                                            type="button" data-toggle="modal" data-target="#editTransporter">
                                        Edit
                                    </button>
                                </td>
                                <td hidden>{{ $transporter->created_at ?? '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('transporters.create')
    @include('transporters.edit')
    @include('transporters.show')

@endsection

@push("page_scripts")
    @include('partials.notification')

    <script>
    $(document).ready(function () {
        $('#fixed-header-transporters').DataTable({
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
                document.getElementById('show_name').value = this.getAttribute('data-name');
                document.getElementById('show_contact_person').value = this.getAttribute('data-contact_person');
                document.getElementById('show_phone').value = this.getAttribute('data-phone');
                document.getElementById('show_email').value = this.getAttribute('data-email');
                document.getElementById('show_number_of_vehicles').value = this.getAttribute('data-number_of_vehicles');
                
                // Set status badge
                const status = this.getAttribute('data-status');
                let statusBadge = '';
                if(status == 'active') {
                    statusBadge = '<span class="badge badge-success">Active</span>';
                } else {
                    statusBadge = '<span class="badge badge-danger">Inactive</span>';
                }
                document.getElementById('show_status').innerHTML = statusBadge;
                
                document.getElementById('show_notes').value = this.getAttribute('data-notes') || 'N/A';
                
                $('#showTransporter').modal('show');
            });
        });

        // Handle edit button click
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const transporterId = this.getAttribute('data-id');
                
                // Set the form action URL
                const form = document.getElementById('form_transporter_edit');
                form.action = `/transport-logistics/transporters/${transporterId}`;
                
                // Populate form fields from data attributes
                document.getElementById('transporter_id').value = transporterId;
                document.getElementById('name_edit').value = this.getAttribute('data-name');
                document.getElementById('contact_person_edit').value = this.getAttribute('data-contact_person');
                document.getElementById('phone_edit').value = this.getAttribute('data-phone');
                document.getElementById('email_edit').value = this.getAttribute('data-email');
                document.getElementById('transport_type_edit').value = this.getAttribute('data-transport_type');
                document.getElementById('number_of_vehicles_edit').value = this.getAttribute('data-number_of_vehicles');
                document.getElementById('status_edit').value = this.getAttribute('data-status');
                document.getElementById('notes_edit').value = this.getAttribute('data-notes') || '';
                
                // Show the modal
                $('#editTransporter').modal('show');
            });
        });
    });
</script>
@endpush