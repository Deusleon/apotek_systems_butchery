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
                                <td>{{$vehicle->capacity}}</td>
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
                                    <a href="{{ route('vehicles.show', $vehicle->id) }}">
                                        <button class="btn btn-success btn-sm btn-rounded">
                                            Show
                                        </button>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle->id) }}">
                                        <button class="btn btn-primary btn-sm btn-rounded">
                                            Edit
                                        </button>
                                    </a>
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
        });
    </script>
@endpush