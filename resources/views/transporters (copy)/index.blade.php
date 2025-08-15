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
                            <th>Transport Type</th>
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
                                <td>{{ucfirst($transporter->transport_type)}}</td>
                                <td>{{$transporter->number_of_vehicles}}</td>
                                <td>
                                    @if ($transporter->status == 'active')
                                        <span class='badge badge-success'>Active</span>
                                    @else
                                        <span class='badge badge-danger'>Inactive</span>
                                    @endif
                                </td>
                                <td style='white-space: nowrap'>
                                    <a href="#">
                                        <button class="btn btn-success btn-sm btn-rounded"
                                                data-id="{{$transporter->id}}"
                                                type="button" data-toggle="modal" data-target="#showTransporter">
                                            Show
                                        </button>
                                    </a>
                                    <a href="#">
                                        <button class="btn btn-primary btn-sm btn-rounded"
                                                data-id="{{$transporter->id}}"
                                                type="button" data-toggle="modal" data-target="#editTransporter">
                                            Edit
                                        </button>
                                    </a>
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
                order: [[8, 'desc']],
            });

            // Fade out alerts after 3 seconds (3000ms)
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });
    </script>
@endpush