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
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('backend/assets/plugins/select2/select2.min.css')}}">
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
    <p class="login-box-msg">Company Details 2/2</p>

    <form name="step2" role="form" action="{{ route('company-admin.submit-step-2') }}" method="POST">
      {{ csrf_field() }}
      
      <div class="form-group has-feedback">
        <input id="contact_name" type="text" class="form-control" name="contact_name" value="{{ old('contact_name') }}" placeholder="Contact name">
        <span class="glyphicon glyphicon-bookmark form-control-feedback"></span>
        @error('contact_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <input id="contact_number" type="text" class="form-control" name="contact_number" value="{{ old('contact_number') }}" placeholder="Contact number">
        <span class="glyphicon glyphicon-bookmark form-control-feedback"></span>
        @error('contact_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <select name="country" id="country" class="form-control select2 dynamic" data-dependent="state">
          <option selected disabled>Select country</option>
          @foreach($countries as $country)
          <option value="{{ $country->id }}"> {{ $country->name }} </option>
          @endforeach
        </select>
        @error('country')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <select name="state" id="state" class="form-control select2 dynamic" data-dependent="city">
          <option selected disabled>Select State/Province</option>
        </select>
        @error('state')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback">
        <select name="city" id="city" class="form-control select2">
          <option selected disabled>Select city</option>
        </select>
        @error('city')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="row">
        
        <!-- /.col -->
        <div class="col-xs-12" style="margin-bottom: 5px;">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Submit') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    <!-- @if (Route::has('password.request'))
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
<!-- select2 -->
<script src="{{asset('backend/assets/plugins/select2/select2.full.min.js')}}"></script>
<script>
  $(function () {
    $('.select2').select2()
  });

  $(document).ready(function(){

   $('.dynamic').change(function(){
    if($(this).val() != '0')
    {
     var select = $(this).attr("id");
     var value = $(this).val();
     var dependent = $(this).data('dependent');
     var _token = $('input[name="_token"]').val();
     $.ajax({
      url:"{{ route('company-admin.locationFetch') }}",
      method:"POST",
      data:{select:select, value:value, _token:_token, dependent:dependent},
      success:function(result)
      {
       $('#'+dependent).html(result);
      }

     })
    }
   });

   $('#country').change(function(){
    $("#state option").remove();
    $("#city option").remove();
    $("#state").val("0");
    $("#city").val("0");
   });

   $('#state').change(function(){
    $("#city").val("0");
    $("#city option").remove();
   });
   

  });
</script>
</body>
</html>
