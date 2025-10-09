@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
    Users

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#">Settings / Security / Users </a></li>
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
                @if(auth()->user()->checkPermission('Add Users'))
                    <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-primary btn-sm"
                        data-toggle="modal" data-target="#add-permission" hidden>
                        Add User Permission
                    </button>
                    <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                        data-toggle="modal" data-target="#register">
                        Add User
                    </button>

                @endif
                <div class="table-responsive">
                    <table id="fixed-header-users" class="display table nowrap table-striped table-hover"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>E-mail</th>
                                <th hidden>Mobile</th>
                                <th>Role</th>
                                <th hidden>Position</th>
                                <th>Branch</th>
                                <th>Status</th>
                                @if(auth()->user()->checkPermission('Edit Users') || auth()->user()->checkPermission('Delete Users'))
                                    <th>Actions</th>
                                @endif
                                <th hidden>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->name}} </td>
                                    <td>{{$user->email}}</td>
                                    <td hidden>{{$user->mobile}}</td>
                                    <td>{{ implode(", ", $user->getRoleNames()->toArray()) }}</td>
                                    <td hidden>{{$user->position}}</td>
                                    <td>{{$user->store->name ?? '' }}</td>
                                    <td>
                                        @if ($user->status == 1)
                                            <span class='badge badge-success'>Active</span>
                                        @endif
                                        @if ($user->status == -1)
                                            <span class='badge badge-info'>In-active</span>
                                        @endif
                                        @if ($user->status == 0)
                                            <span class='badge badge-danger'>De-activated</span>
                                        @endif
                                    </td>

                                    @if(auth()->user()->checkPermission('Edit Users') || auth()->user()->checkPermission('Delete Users'))
                                        <td style='white-space: nowrap'>
                                            <a href="#">
                                                <button class="btn btn-success btn-sm btn-rounded" data-name="{{$user->name}}"
                                                    data-email="{{$user->email}}" data-id="{{$user->id}}"
                                                    data-job="{{$user->position}}" data-mobile="{{$user->mobile}}"
                                                    data-role="{{ implode(", ", $user->getRoleNames()->toArray()) }}"
                                                    data-store="{{$user->store->name ?? ""}}" type="button" data-toggle="modal"
                                                    data-target="#showUser">Show
                                                </button>
                                            </a>
                                            @if(auth()->user()->checkPermission('Edit Users'))
                                                <a href="#">
                                                    <button class="btn btn-primary btn-sm btn-rounded" data-name="{{$user->name}}"
                                                        data-email="{{$user->email}}" data-id="{{$user->id}}"
                                                        data-job="{{$user->position}}" data-mobile="{{$user->mobile}}"
                                                        data-role="{{ implode(", ", $user->getRoleNames()->toArray()) }}"
                                                        data-store="{{$user->store->id ?? 0}}" type="button" data-toggle="modal"
                                                        data-target="#editUser">Edit
                                                    </button>
                                                </a>
                                            @endif

                                            @if(auth()->user()->isAdmin('admin'))
                                                <div class="btn-group">
                                                    <button type="button" class="btn" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="feather icon-more-horizontal"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a id="reset_btn" href="{{ route('password.reset.admin', $user->email) }}">
                                                            <button class="dropdown-item "><span class="feather feather icon-unlock"
                                                                    hidden></span> Reset
                                                                Password
                                                            </button>
                                                        </a>
                                                        {{-- <a href="#">
                                                            <button class="dropdown-item " data-toggle="modal"
                                                                data-target="#add-permission">
                                                                User Permissions
                                                            </button>
                                                        </a> --}}
                                                        @if(auth()->user()->checkPermission('Permit Users'))
                                                            @if ($user->status == 1)
                                                                <a href="#">
                                                                    <button class="dropdown-item " type="button" data-toggle="modal"
                                                                        data-target="#disableUser" data-id="{{$user->id}}"
                                                                        data-status="{{$user->status}}" data-name="{{$user->name}}">Deactivate
                                                                    </button>
                                                                </a>
                                                            @endif
                                                            @if ($user->status == 0 || $user->status == -1)
                                                                <a href="#">
                                                                    <button class="dropdown-item " type="button" data-toggle="modal"
                                                                        data-target="#disableUser" data-id="{{$user->id}}"
                                                                        data-status="{{$user->status}}" data-name="{{$user->name}}">
                                                                        Activate
                                                                    </button>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td hidden>{{ $user->created_at ?? '' }}</td>
                                    @endif
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    @include('users.create')
    @include('users.permission')
    @include('users.edit')
    @include('users.show')
    @include('users.de_activate')



@endsection

@push("page_scripts")
    @include('partials.notification')



    <script>


        $(document).ready(function () {



            $('#fixed-header-users').DataTable({
                order: [[8, 'desc']],
            });

            $('#editUser').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var name = button.data('name');
                var email = button.data('email');
                var store = button.data('store')
                var mobile = button.data('mobile');
                var job = button.data('job');
                var role = button.data('role');
                var id = button.data('id');
                var modal = $(this);


                modal.find('.modal-body #name1').val(name);
                modal.find('.modal-body #store').val(store);
                modal.find('.modal-body #email1').val(email);
                modal.find('.modal-body #position1').val(job);
                modal.find('.modal-body #mobile1').val(mobile).change();
                modal.find('.modal-body #UserID').val(id);

                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{route('getRoleID')}}",
                    method: "POST",
                    data: { role: role, _token: _token },
                    success: function (result) {
                        $('#role1').val(result).change();
                    }
                })

            });//end edit

            $('#showUser').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var name = button.data('name');
                var email = button.data('email');
                var store = button.data('store')
                var mobile = button.data('mobile');
                var job = button.data('job');
                var role = button.data('role');
                var id = button.data('id');
                var modal = $(this);


                modal.find('.modal-body #name1').html(name);
                modal.find('.modal-body #store').html(store);
                modal.find('.modal-body #email1').html(email);
                modal.find('.modal-body #position1').html(job);
                modal.find('.modal-body #mobile1').html(mobile).change();
                modal.find('.modal-body #UserID').val(id);
                modal.find('.modal-body #role1').html(role);

                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{route('getRoleID')}}",
                    method: "POST",
                    data: { role: role, _token: _token },
                    success: function (result) {
                        $('#role1').val(result).change();
                    }
                })

            });//end edit

            //de activate and activate user
            $('#disableUser').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var status = button.data('status');
                var user = button.data('name');
                var modal = $(this);

                if (status == 1) {
                    var message = "Are you sure you want to de-activate - ".concat(user)
                }
                if (status == 0 || status == -1) {
                    var message = "Are you sure you want to activate - ".concat(user)
                }
                modal.find('.modal-body #userid').val(id);
                modal.find('.modal-body #status').val(status);
                modal.find('.modal-body #prompt_message').text(message);

            });//end

            //delete user
            $('#deleteUser').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var user = button.data('name');
                var modal = $(this);
                var message = "Are you sure you want to delete '".concat(user);

                modal.find('.modal-body #user').val(id);
                modal.find('.modal-body #message_del').text(message)

            })//end

            // Fade out alerts after 3 seconds (3000ms)
            setTimeout(function () {
                $('.alert').fadeOut('slow');
            }, 3000); // Adjust the time (3000 ms) as needed

        });

    </script>

@endpush