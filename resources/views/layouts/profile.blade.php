@extends("layouts.master")


@section('page_css')
@endsection

@section('content-title')
    Profile

@endsection

@section('content-sub-title')
    <li class="breadcrumb-item"><a href="{{route('home')}}"><i class="feather icon-home"></i></a></li>
    <li class="breadcrumb-item"><a href="#"> Profile </a></li>
@endsection



@section("content")

    <div class="col-sm-12">

        <div class="card">


            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger mx-auto" style="width: 70%">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success mx-auto" style="width: 70%">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="container">
                    <div class="row my-2">
                        <div class="col-lg-8 order-lg-2">
                            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Change Password</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Full Name</h6>
                                            <p>
                                                {{Auth::user()->name}}
                                            </p>
                                            <h6>Mobile No</h6>
                                            <p>
                                                @if (Auth::user()->mobile != null)
                                                    {{Auth::user()->mobile}}
                                                @else
                                                    <i>--N/l--</i>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Email</h6>
                                            <p>
                                                {{Auth::user()->email}}
                                            </p>
                                            <h6>Position</h6>
                                            <p>
                                                {{ Auth::user()->roles->pluck('name')[0] }}
                                            </p>
                                        </div>                
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                    <form method="POST" action="{{ route('changePassword') }}">
                                        @csrf
                                        {{-- <h3 class="mb-4">Change Password</h3> --}}
                                        <div class="input-group mb-3{{ $errors->has('current-password') ? ' has-error' : '' }} mx-auto" style="width: 70%">
                                            <input id="current-password" type="password" class="form-control"
                                                name="current-password" placeholder="Current Password" required>
                            
                                            @if ($errors->has('current-password'))
                                                <span class="help-block">
                                                            <strong>{{ $errors->first('current-password') }}</strong>
                                                        </span>
                                            @endif
                                        </div>
                            
                                        <div class="input-group mb-3{{ $errors->has('new-password') ? ' has-error' : '' }} mx-auto" style="width: 70%">
                                            <input id="new-password" type="password" class="form-control" name="new-password"
                                                placeholder="New Password"
                                                required>
                            
                                            @if ($errors->has('new-password'))
                                                <span class="help-block">
                                                            <strong>{{ $errors->first('new-password') }}</strong>
                                                        </span>
                                            @endif
                                        </div>
                            
                                        <div class="input-group mb-3 mx-auto" style="width: 70%">
                                            <input id="new-password-confirm" type="password" class="form-control"
                                                name="new-password_confirmation" placeholder="Confirm New Password" required>
                                        </div>
                            
                                        <div class="form-group mx-auto">
                                            <center>
                                                <div class="col-md-12 col-md-offset-4">
                                                    <button type="submit" class="btn btn-primary">
                                                        Change
                                                    </button>
                                                </div>
                                            </center>
                                        </div>
                            
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 order-lg-1 text-center"><br>
                            {{-- <h5>User Image</h5> --}}
                            @if (Auth::user()->profile_image != null)
                                <img src="/fileStore/logo/{{Auth::user()->profile_image}}" class="mx-auto img-fluid img-circle d-block" alt="avatar" style="height: 200px">
                            @else
                            <img src="/male_avatar.png" class="mx-auto img-thumbnail img-fluid img-circle d-block" alt="avatar" style="height: 200px">
                            @endif
                            
                            {{-- <h6 class="mt-2">Upload a different photo</h6> --}}
                            {{-- <label class="custom-file m-1">
                                <form action="{{route('updateProfileImage')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" id="profile_image" name="profile_image" class="form-control w-60 mx-auto" required>
                                    <button class="btn btn-sm btn-primary m-1" type="submit" >Update</button>
                                </form>
                            </label> --}}
                        </div>
                    </div>
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
