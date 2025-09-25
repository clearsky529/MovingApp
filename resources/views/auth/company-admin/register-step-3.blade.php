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
    <p class="login-box-msg">Subscription Plan</p>

    <form name="step3" method="POST" action="{{ route('company-admin.store') }}">
      @csrf
      <div class="form-group has-feedback">
        <select name="subscription" id="subscription" class="form-control @error('website') is-invalid @enderror subscription">
          <option selected disabled>Select A Plan</option>
          <option value="0">Free Trial</option>
          @foreach($subscriptions as $subscription)
          <option value="{{ $subscription->id }}" <?php if(old('subscription') == $subscription->id) echo "selected";?> data-month-price="{{ $subscription->currency ? $subscription->currency->currency_symbol : '-1'}} {{$subscription->monthly_price ? $subscription->monthly_price : '' }}" data-addon-price="{{ $subscription->currency ? $subscription->currency->currency_symbol : ''}} {{$subscription->addon_price ? $subscription->addon_price : '' }}" data-free-users="{{ $subscription->free_users }}" > {{ $subscription->title }} </option>
          @endforeach
        </select>
        @error('subscription')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="form-group has-feedback addon-toggle">
        <input id="addon" type="number" class="form-control" name="addon" value="{{ old('addon') }}" placeholder="Add-on users">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
        @error('addon')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div id="subsData">
        
      </div>

      <!--  <div id="enterprise">
        
      </div> -->
      
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

  $( document ).ready(function() {
    var check_addon = "<?php echo old('addon'); ?>";
    var subscription = $('#subscription').val();
    var month_price = $('#subscription').find(':selected').attr('data-month-price');
    var addon_price = $('#subscription').find(':selected').attr('data-addon-price');
    var free_users = $('#subscription').find(':selected').attr('data-free-users');
    if (!check_addon) {
      $('.addon-toggle').hide();
    }
    if (subscription && subscription != "0") {
      appendSubsInfo(month_price, addon_price, free_users, subscription);
    }
  });

  $('.subscription').change(function(){
    var month_price = $(this).find(':selected').attr('data-month-price');
    var addon_price = $(this).find(':selected').attr('data-addon-price');
    var free_users = $(this).find(':selected').attr('data-free-users');
    var input_value = $(this).val();
    appendSubsInfo(month_price, addon_price, free_users, input_value);
  });

  function appendSubsInfo(month_price, addon_price, free_users, input_value){
    <?php $free_days = App\Constant::first(); ?>
    var days = <?php echo $free_days->value; ?>;
    // alert(month_price);
    if(input_value != 0 && month_price != -1){
      $('.addon-toggle').show();
      $("#subsData").html('<div class="box box-info"><div class="box-header with-border"><h3 class="box-title">Subscription plan information</h3></div><div class="box-footer no-padding" style=""><ul class="nav nav-pills nav-stacked"><li><a href="#">Monthly price<span class="pull-right text-green">'+ month_price +'</span></a></li><li><a href="#">Add-on price<span class="pull-right text-green">'+ addon_price +'</span></a></li><li><a href="#">Monthly free users <span class="pull-right text-green">'+ free_users +'</span></a></li></ul></div></div>');
    }

    else if (input_value > 0 && month_price == -1) {
      $('.addon-toggle').hide();
        // $('#enterprise').html('<div class="box box-info"><div class="box-header with-border"><h3 class="box-title"><center>Your free trial is valid for '+ days +' days</center></h3></div><div class="box-footer no-padding" style=""><ul class="nav nav-pills nav-stacked"><li><a href="#"><center>Your free trial is valid for '+ 'vgdfvgdfg' +' days</center></a></li></ul></div></div>');
    }
    else{
      $('#addon').val('');
      $('.addon-toggle').hide();
      $('#subsData').html('<div class="box box-info"><div class="box-header with-border"><h3 class="box-title"><center>Your free trial is valid for '+ days +' days</center></h3></div><div class="box-footer no-padding" style=""><ul class="nav nav-pills nav-stacked"><li><a href="#"><center>Your free trial is valid for '+ days +' days</center></a></li></ul></div></div>');
    }
  }

</script>
</body>
</html>
