<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
  <title>Register</title>
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
    <b>kika </b>register
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Company Details 1/2</p>

    @if (Session::has('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close error" data-dismiss="alert">×</button> 
              <strong>{!! session('error') !!}</strong>
        </div>
      @endif

    @if (Session::has('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close error" data-dismiss="alert">×</button> 
              <strong>{!! session('success') !!}</strong>
        </div>
      @endif

    <form name="step1" method="POST" action="{{ route('company-admin.submit-step-1') }}">
      @csrf
      <!-- <div class="form-group has-feedback">
        <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="First name">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
        @error('first_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Last name">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
        @error('last_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div> -->
      <div class="form-group has-feedback">
        <input id="company_name" type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name" value="{{ old('company_name') }}" placeholder="Company name">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
        @error('company_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <select name="type" class="form-control">
          <option selected disabled>Select type</option>
          @foreach($companyTypes as $companyType)
          <option value="{{ $companyType->id }}"> {{ $companyType->company_type }} </option>
          @endforeach
        </select>
        @error('type')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="website" type="text" class="form-control @error('website') is-invalid @enderror" name="website" value="{{ old('website') }}" placeholder="Website url">
        <span class="glyphicon glyphicon-globe form-control-feedback"></span>
        @error('website')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="referral_code" type="text" class="form-control @error('referral_code') is-invalid @enderror" name="referral_code" value="{{ old('referral_code') ? old('referral_code') : ($referral_code ?  $referral_code : '')}}" placeholder="Referral Code">
        <span class="glyphicon glyphicon-font form-control-feedback"></span>
        @error('referral_code')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="checkbox" style="margin-left: 20px;">
        <label style="padding: 0;">
          <input type="checkbox" id="chkbox" style="width: 20px;height: 20px;"><span style="margin-left: 5px;"> I agree to the terms of service and <a href="{{route('terms-condition')}}" target="_blank">privacy policy</a></span>
        </label>
      </div>
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-12" style="margin-bottom: 5px;">
          <button type="submit" id="btnsubmit" class="btn btn-primary btn-block btn-flat">{{ __('Submit') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
   <!--  @if (Route::has('password.request'))
        <a class="" href="{{ url('admin/login') }}">
            Login?
        </a>
    @endif -->
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
  // $(function () {
  //   $('input').iCheck({
  //     checkboxClass: 'icheckbox_square-blue',
  //     radioClass: 'iradio_square-blue',
  //     increaseArea: '20%' /* optional */,
  //   });
  // });
  $(document).ready(function(){
    $('#btnsubmit').attr('disabled','disabled');
        $('#chkbox').click(function(){
            if($(this).prop("checked") == true){
              $('#btnsubmit').attr('disabled',false);
            }
            else if($(this).prop("checked") == false){
              $('#btnsubmit').attr('disabled','disabled');
            }
        });
    });

  
</script>
</body>
</html>
