@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
    Roles

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Security / Roles </a></li>
@endsection



@section("content")

    <div class="col-sm-12">

        <div class="card">


            <div class="card-body">
                @if(auth()->user()->checkPermission('View Settings'))
                    <a href="{{route('roles.create')}}">
                        <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm">
                            Add Role
                        </button>
                    </a>
                @endif
                <div class="table-responsive">
                    <table id="fixed-header" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            @if(auth()->user()->checkPermission('View Settings'))
                                <th>Actions</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>{{$role->name}}</td>
                                <td>{{$role->description}}</td>
                                @if(auth()->user()->checkPermission('View Settings'))
                                    <td>
                                        <a href="{{route('roles.edit',$role->id)}}">
                                            <button class="btn btn-primary btn-rounded btn-sm" type="button">Edit
                                            </button>
                                        </a>
                                        <a href="#">
                                            <button class="btn btn-danger btn-rounded btn-sm" data-id="{{$role->id}}"
                                                    data-name="{{$role->name}}" type="button" data-toggle="modal"
                                                    data-target="#deleteModal"> Delete
                                            </button>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

@endsection

@push("page_scripts")
    @include('roles.delete')

    @include('partials.notification')

    <script>

        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var message = "Are you sure you want to delete Role '".concat(button.data('name'), "'?");
            var modal = $(this);
            modal.find('.modal-body #message').text(message);
            modal.find('.modal-body #role_id').val(id)
        })

    </script>

@endpush
