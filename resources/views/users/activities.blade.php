@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
    Activities

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Activities / Logs </a></li>
@endsection



@section("content")

    <div class="col-sm-12">

        <div class="card">

            <div class="card-body">
                <div class="table-responsive">
                    <table id="activities" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Log Type</th>
                            <th>Log Date</th>
                            <th>Data</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($activities as $user)
                            <tr>
                                <td>{{$user->name ?? ''}} </td>
                                <td>{{$user->log_type ?? ''}} </td>
                                <td>{{$user->log_date ?? ''}}</td>
                                <td>{{$user->data ?? ''}}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

@endsection

@push('page_scripts')

    <script>
        $(document).ready(function () {

            $('#activities').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });

        });
    </script>


@endpush
