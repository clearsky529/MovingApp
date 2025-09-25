@extends('theme.company-admin.layouts.main')
@section('title', 'Extend Subscription')
@section('page-style')
<link rel="stylesheet" href="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@stop
@section('content')
{{app()->setLocale(session()->get("locale"))}}
<?php 
	$userId = '';
    if(Session::get('company-admin')){
        $userId = Session::get('company-admin');
    }
    elseif(Auth::user() != null){
        $userId = Auth::user()->id;
    }
    $company_type = App\Companies::where('tbl_users_id',$userId)->value('type'); 
    // dd($company_type);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Extend Subscription
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i> @lang('auth.home')</a></li>
    <li class="active">Extend Subscription</li>
  </ol>
</section>

@if(Session::has('flash_message_success'))
<div class="col-md-12" style="margin-top: 10px;">
  <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{!! session('flash_message_success') !!}</strong>
  </div>
</div>
@endif

<section class="content">
  <div class="row">
    <div class="col-lg-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Extend Plan</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" id="myform" action="{{ route('company-admin.subcription.payment') }}" method="post">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="form-group">
              <label>Subscription Plan</label>
              <select name="subscription" id="subscription" class="form-control @error('website') is-invalid @enderror subscription">
                <option selected disabled>Select A Plan</option>
                @foreach($subscriptions as $subscription)
                <option value="{{ $subscription->id }}" <?php if (old('subscription') == $subscription->id) echo "selected"; ?> data-nm="{{ $subscription->title }}" data-month-price="{{ $subscription->currency ? $subscription->currency->currency_symbol : '-1'}} {{$subscription->monthly_price ? $subscription->monthly_price : '' }}" data-addon-price="{{ $subscription->currency ? $subscription->currency->currency_symbol : ''}} {{$subscription->addon_price ? $subscription->addon_price : '' }}" data-free-users="{{ $subscription->free_users }}"> {{ $subscription->title }} </option>
                @endforeach
              </select>
              @if ($errors->has('subscription'))
              <span class="text-danger">{{ $errors->first('subscription') }}</span>
              @endif
            </div>
          </div>
          <div class="box-body">
            <div class="form-group">
              <label for="add_on_users">Additional Devices</label>
              <input type="number" name="add_on_users" value="{{old('add_on_users')}}" class="form-control" id="add_on_user" placeholder="Enter number of extra devices">
              @if ($errors->has('add_on_users'))
              <span class="text-danger">{{ $errors->first('add_on_users') }}</span>
              @endif
            </div>
          </div>
          <div class="box-body">
            <div class="box-footer">
              <button type="submit" class="btn btn-primary">Buy Plan</button>
            </div>
          </div>
        </form>
      </div>

    </div>
    <div class="col-lg-6 has-feedback addon-toggle">
      <div id="subsData">

      </div>
    </div>

    <div class="col-lg-6 has-feedback enterprise">
      <div class="box box-primary">
        <div class="box-body text-center">
          <p>The Enterprise Plan allows you to add as many devices to your account as necessary for an agreed price between your company and Kika.</p>
          <p>Enterprise Plan fees will be invoiced to your company monthly.</p>
        </div>
      </div>
    </div>

    <div class="col-lg-6 has-feedback kikaDirect">
      <div class="box box-primary">
        <div class="box-body">
          <p>Kika Direct is for companies that do not need to Screen, Tranship or use Agents for their jobs so these features are NOT available on this plan.</p>
          <p>This plan is charged per uplift inventory created and allows you to register as many devices as you like at no extra cost.</p>
          <p class="text-primary" style="font-size: 20;font-weight: bold;">Important : When selecting this plan all your devices will be deleted and will need to be added again. This is done to ensure correct billing.</p>
          <hr />
          <div class="" style="justify-content: space-between;display: flex; font-weight: bold;font-size: 20;">
            <p>Monthly Access Fee</p>
            <p><span id="add_here"></span></p>
          </div>
          <div class="" style="justify-content: space-between;display: flex; font-weight: bold;font-size: 20;">
            <p>Cost Per Inventory</p>
            <p><span id="add_here1"></span></p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6 has-feedback withoutKikaDirect">
        <div class="box box-primary">
          <div class="box-body">
            <p class="text-primary" style="font-size: 20;font-weight: bold;">Important : If you are on the Kika Direct Plan and change to this plan all your devices will be deleted and need to be added again. This is done to ensure correct billing. Switching from other plans will not affect your devices.</p>
          </div>
        </div>
      
    </div>
  </div>
</section>

@endsection
@section('page-script')
<script src="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
  $()
  $(document).ready(function() {
    $('.enterprise').hide();
    $('.kikaDirect').hide();
    $('.withoutKikaDirect').hide();
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

  var subscriptionName = '';
  $('.subscription').change(function() {
    var month_price = $(this).find(':selected').attr('data-month-price');
    var addon_price = $(this).find(':selected').attr('data-addon-price');
    var free_users = $(this).find(':selected').attr('data-free-users');
    var input_value = $(this).val();
    //start code by ss_23_aug
    var freenm = $(this).find(':selected').attr('data-nm');
    var company_type = '<?php echo $company_type; ?>';


    if (freenm == 'Kika Direct') {
      $('.enterprise').hide();
      $('.addon-toggle').hide();
      $('.withoutKikaDirect').hide();
      $('.kikaDirect').show();
    } else {
    	if(company_type == 1)
    	{
    		$('.kikaDirect').hide();
      		$('.withoutKikaDirect').hide();
    	}else{
	      $('.kikaDirect').hide();
	      $('.withoutKikaDirect').show();
	     }
    }
    //end code by ss_23_aug
    // alert(input_value);

    var url = '{{ url("company-admin/profile/subscription/selectedPlan") }}';
    $.ajax({
      type: "POST",
      // dataType: "json",
      url: url,
      data: {
        'plan': input_value,
        '_token': "{{ csrf_token() }}"
      },
      success: function(data) {
        if (freenm == 'Kika Direct') {
          console.log(data);
           var price = data[1]['currency']['currency_code']+' '+data[1]['currency']['currency_symbol']+data[1]['monthly_price'];
           var icrPrice = data[0]['currency']['currency_code']+' '+data[0]['currency']['currency_symbol']+data[0]['value'];
          // console.log(price)

          }else{
            var price = '';
            var icrPrice = '';
          }
        appendSubsInfo(month_price, addon_price, free_users, input_value, data, freenm, price, icrPrice);
      }
    });
  });

  function appendSubsInfo(month_price, addon_price, free_users, input_value, subscriptionName, freenm, price, icrPrice) {
    if (input_value != "0" && month_price != -1 && freenm != "Kika Direct") {
      $('.addon-toggle').show();
      $('.enterprise').hide();
      $("#subsData").html('<div class="box box-primary"><div class="box-header with-border"><h3 class="box-title">' + subscriptionName + ' Plan Details</h3></div><div class="box-footer no-padding" style=""><ul class="nav nav-pills nav-stacked"><li><a href="#">Monthly Fee<span class="pull-right text-green">' + month_price + '</span></a></li><li><a href="#">Additional Device/Month<span class="pull-right text-green">' + addon_price + '</span></a></li><li><a href="#">Free Devices/Month <span class="pull-right text-green">' + free_users + '</span></a></li></ul></div></div>');
      if(company_type == 1){
      	$('.withoutKikaDirect').hide();
      }
      else{
      	$('.withoutKikaDirect').show();
      }
    }
    //start code by ss_23_aug
    else if (freenm == 'Kika Direct') {
      $('.enterprise').hide();
      $('.addon-toggle').hide();
      $('.kikaDirect').show();
      $('#add_here').text(price);
      $('#add_here1').text(icrPrice);
      $('.withoutKikaDirect').hide();
    }
    //start code by ss_23_aug
    else if (input_value > 0 && month_price == -1) {
      $('.addon-toggle').hide();
      $('.enterprise').show();
      $('.withoutKikaDirect').show();
      //  $("#subsData").html('<div class="box box-info"><div class="box-header with-border"><h3 class="box-title">'+subscriptionName+' Plan Details</h3></div><div class="box-footer no-padding" style=""><ul class="nav nav-pills nav-stacked"><li><a href="#">Monthly Fee<span class="pull-right text-green">'+ month_price +'</span></a></li><li><a href="#">Additional User/Month<span class="pull-right text-green">'+ addon_price +'</span></a></li><li><a href="#">Free Users/Month <span class="pull-right text-green">'+ free_users +'</span></a></li></ul></div></div>');
    }
  }
</script>
@stop