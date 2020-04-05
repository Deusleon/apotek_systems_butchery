@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
    Users

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Users / Users </a></li>
@endsection



@section("content")

    <div class="col-sm-12">

        <div class="card">


            <div class="card-body">
                @if(auth()->user()->checkPermission('Manage Users'))
                    <button style="float: right;margin-bottom: 2%;" type="button" class="btn btn-secondary btn-sm"
                            data-toggle="modal" data-target="#register">
                        Add User
                    </button>
                @endif
                <div class="table-responsive">
                    <table id="fixed-header" class="display table nowrap table-striped table-hover" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-mail</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Position</th>
                            <th>Status</th>
                            @if(auth()->user()->checkPermission('Manage Users'))
                                <th>Actions</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{$user->name}} </td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->mobile}}</td>
                                <td>{{ implode(", ", $user->getRoleNames()->toArray()) }}</td>
                                <td>{{$user->position}}</td>
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

                                @if(auth()->user()->checkPermission('Manage Users'))
                                    <td style='white-space: nowrap'>
                                        <a href="#">
                                            <button class="btn btn-primary btn-sm btn-rounded"
                                                    data-name="{{$user->name}}"
                                                    data-email="{{$user->email}}" data-id="{{$user->id}}"
                                                    data-job="{{$user->position}}" data-mobile="{{$user->mobile}}"
                                                    data-role="{{ implode(", ", $user->getRoleNames()->toArray()) }}"
                                                    type="button" data-toggle="modal" data-target="#editUser">Edit
                                            </button>
                                        </a>
                                        @if ($user->status == 1)
                                            <a href="#">
                                                <button class="btn btn-danger btn-sm btn-rounded" type="button"
                                                        data-toggle="modal"
                                                        data-target="#disableUser" data-id="{{$user->id}}"
                                                        data-status="{{$user->status}}" data-name="{{$user->name}}">De
                                                    Activate
                                                </button>
                                            </a>
                                        @endif
                                        @if ($user->status == 0 || $user->status == -1)
                                            <a href="#">
                                                <button class="btn btn-success btn-sm btn-rounded" type="button"
                                                        data-toggle="modal"
                                                        data-target="#disableUser" data-id="{{$user->id}}"
                                                        data-status="{{$user->status}}" data-name="{{$user->name}}">
                                                    Activate
                                                </button>
                                            </a>
                                        @endif

                                        {{--                                        <div class="btn-group">--}}
                                        {{--                                            <button type="button" class="btn" data-toggle="dropdown"--}}
                                        {{--                                                    aria-haspopup="true" aria-expanded="false">--}}
                                        {{--                                                <i class="feather icon-more-horizontal"></i>--}}
                                        {{--                                            </button>--}}
                                        {{--                                            <div class="dropdown-menu">--}}
                                        {{--                                                <a id="reset_btn" href="{{ route('password.reset.admin') }}">--}}
                                        {{--                                                    <button class="dropdown-item "><span--}}
                                        {{--                                                            class="feather feather icon-unlock"></span> Reset Password--}}
                                        {{--                                                    </button>--}}
                                        {{--                                                </a>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}

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

    @include('users.create')
    @include('users.edit')
    @include('users.de_activate')



@endsection

@push("page_scripts")
    @include('partials.notification')

    <script>
        $(document).ready(function () {

            $('#editUser').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var name = button.data('name');
                var email = button.data('email');
                var mobile = button.data('mobile');
                var job = button.data('job');
                var role = button.data('role');
                var id = button.data('id');
                var modal = $(this);


                modal.find('.modal-body #name1').val(name);
                modal.find('.modal-body #email1').val(email);
                modal.find('.modal-body #position1').val(job);
                modal.find('.modal-body #mobile1').val(mobile).change();
                modal.find('.modal-body #UserID').val(id);

                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url: "{{route('getRoleID')}}",
                    method: "POST",
                    data: {role: role, _token: _token},
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


        });

    </script>

@endpush
