<!DOCTYPE html>
<html lang="en">

<head>
    <title>APOTEk System</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="author" content="CodedThemes"/>

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset("APOTEk2.ico")}}">

    <!-- fontawesome icon -->
    <link rel="stylesheet" href="{{asset("assets/fonts/fontawesome/css/fontawesome-all.min.css")}}">

    <!-- animation css -->
    <link rel="stylesheet" href="{{asset("assets/plugins/animation/css/animate.min.css")}}">
    <!-- vendor css -->
    <link rel="stylesheet" href="{{asset("assets/css/style.css")}}">


</head>

<body>
<div class="auth-wrapper">
    <div class="auth-content">

        <div class="card">
            <div class="card-body text-center">

                <form method="POST" action="{{ route('password.admin.update') }}">
                    @csrf
                    <div class="mb-4">
                        <i class="feather icon-unlock auth-icon"></i>
                    </div>

                    <h3 class="mb-4">Reset Password</h3>
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control"
                               name="email" value="{{$email}}" required autocomplete="email"
                               autofocus placeholder="Email" readonly>
                    </div>

                    <div class="input-group mb-3{{ $errors->has('new-password') ? ' has-error' : '' }}">
                        <input id="new-password" type="password" class="form-control" name="password"
                               placeholder="New Password"
                               required>

                        @if ($errors->has('new-password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('new-password') }}</strong>
                                    </span>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <input id="new-password-confirm" type="password" class="form-control"
                               name="new-password_confirmation" placeholder="Confirm New Password" required>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 col-md-offset-4">
                            <button id="save_btn" type="submit" class="btn btn-primary">
                                Change
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

</body>

<!-- Required Js -->
<script src="{{asset("assets/js/vendor-all.min.js")}}"></script>
<script src="{{asset("assets/plugins/bootstrap/js/bootstrap.min.js")}}"></script>
<script src="{{asset("assets/js/pcoded.min.js")}}"></script>
<!-- notification Js -->
<script src="{{asset("assets/plugins/notification/js/bootstrap-growl.min.js")}}"></script>

<script>
    $('#new-password-confirm').on('change', function () {
        let new_pwd = document.getElementById('new-password').value;
        let confirm_pwd = document.getElementById('new-password-confirm').value;

        if (String(new_pwd) !== String(confirm_pwd)) {
            $('#save_btn').prop('disabled', true);
        } else {
            $('#save_btn').prop('disabled', false);
        }

    });
</script>

</html>
