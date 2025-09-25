<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
  <title>Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/Ionicons/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('backend/assets/dist/css/AdminLTE.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('backend/assets/plugins/iCheck/square/blue.css')}}">
    <link rel="stylesheet" href="{{asset('css/backend.css')}}">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>kika </b>login
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
      @if (Session::has('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close error" data-dismiss="alert">×</button> 
              <strong>{!! session('error') !!}</strong>
        </div>
      @endif
       @if (Session::has('flash_message_success'))
        <div class="alert alert-success alert-block" align="center">
            <button type="button" class="close error" data-dismiss="alert">×</button> 
              <strong>{!! session('flash_message_success') !!}</strong>
        </div>
      @endif
    <p class="login-box-msg"></p>

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group has-feedback">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

      </div>
      <div class="row">
        <div class="col-xs-8">
          <!-- <div class="checkbox icheck">
            <label>
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}
            </label>
          </div> -->
        </div>
        <!-- /.col -->
        <div class="col-xs-12" style="margin-bottom: 5px;">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Login') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    @if (Route::has('password.request'))
        <a class="" href="{{ route('password.request') }}">
            <center>{{ __('Forgot Your Password ?') }}</center>
        </a>
    @endif  
    <a class="" href="{{ route('company-admin.register') }}">
        <center>Register Your Company.</center>
    </a>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="{{asset('backend/assets/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{asset('backend/assets/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- iCheck -->
<script src="{{asset('backend/assets/plugins/iCheck/icheck.min.js')}}"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
