@extends('theme.admin.layouts.main')
@section('title', 'General Settings')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
<section class="content-header">
    <h1>
    General Settings
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
        <li class="active">General Settings</li>
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
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Dashboard Data Duration Period</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>Current Setting : <span class="badge bg-green" id="appendDuration">{{ ucfirst(str_replace("_"," ",$dashboardCount->duration)) }}</span> </th>
                                <th style="width: 40px"><button type="button" class="btn btn-primary admin_setting_button" data-toggle="modal" data-target="#modal-default">Change</button></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Free Trial Period</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>Current Free Trial Days : <span class="badge bg-green" id="appendFreePeriod">{{ $free_trial_period }}</span> </th>
                                <th style="width: 40px"><button type="button" class="btn btn-primary admin_setting_button" data-toggle="modal" data-target="#trial-days">Change</button></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Manage Device Price</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>Device Price : <span class="badge bg-green" id="appendDevicePrice">{{$device_price->currency->currency_symbol}} {{ $device_price->value }}</span> </th>
                                <th style="width: 40px"><button type="button" class="btn btn-primary admin_setting_button" data-toggle="modal" data-target="#device-price">Change</button></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-xs-6">
            <div class="box">
                <div class="box-header">
                  <h3 class="box-title">Manage ICR Price setting</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th>ICR Price : <span class="badge bg-green" id="appendIcrPrice">{{$icr_price->currency->currency_symbol}} {{ $icr_price->value }}</span> </th>
                                <th style="width: 40px"><button type="button" class="btn btn-primary admin_setting_button" data-toggle="modal" data-target="#icr-price">Change</button></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
        </div>
    </div>


</section>


<div class="modal fade" id="modal-default">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Time Period</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <select name="duration" id="duration" class="form-control">
                        <option <?php if($dashboardCount->duration == 'all'){ echo "selected"; } ?> value="all">All</option>
                        <option <?php if($dashboardCount->duration == 'this_week'){ echo "selected"; } ?> value="this_week">This week</option>
                        <option <?php if($dashboardCount->duration == 'next_week'){ echo "selected"; } ?> value="next_week">Next week</option>
                        <option <?php if($dashboardCount->duration == 'this_month'){ echo "selected"; } ?> value="this_month">This month</option>
                        <option <?php if($dashboardCount->duration == 'next_month'){ echo "selected"; } ?> value="next_month">Next month</option>
                        <option <?php if($dashboardCount->duration == 'last_week'){ echo "selected"; } ?> value="last_week">Last week</option>
                        <option <?php if($dashboardCount->duration == 'last_month'){ echo "selected"; } ?> value="last_month">Last month</option>
                        <option <?php if($dashboardCount->duration == 'last_2month'){ echo "selected"; } ?> value="last_2_month">Last 2 months</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-setting">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="trial-days">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Free Trial Period</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="number" class="form-control" name="days" id="days" value="{{ $free_trial_period }}" placeholder="Enter Free Trial days">
                    <span class="error-refer" style="color:red; display:none;"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" id="change-duration" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="device-price">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change Device Price</h4>
            </div>
            <div class="modal-body" style="padding: 0px 15px;">
                <div class="form-group">
                    <label>Device Price</label>
                    <input type="number" class="form-control" name="deviceprice" id="deviceprice" value="{{ $device_price->value }}" placeholder="Enter Device Price">
                    <span class="error-refer" style="color:red; display:none;"></span>
                </div>
            </div>
            <div class="modal-body" style="padding: 0px 15px;">
                <div class="form-group">
                    <label>Currency</label>
                    <select name="device_currency" class="form-control" id="device_currency">
                            <option disabled="" selected="">Select currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}"
                                    <?php 
                                      if($currency->id == $device_currency)
                                      { 
                                        echo "selected"; 
                                      }
                                      else{
                                        echo "";
                                      }
                                    ?>>
                                {{ $currency->currency_code}} ({{ $currency->currency_symbol }})</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" id="change-device_price" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="icr-price">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Change ICR Price</h4>
            </div>
            <div class="modal-body" style="padding: 0px 15px;">
                <div class="form-group">
                    <label>ICR Price</label>
                    <input type="number" class="form-control" name="icrprice" id="icrprice" value="{{ $icr_price->value }}" placeholder="Enter Icr Price">
                    <span class="error-refer" style="color:red; display:none;"></span>
                </div>
            </div>
            <div class="modal-body" style="padding: 0px 15px;">
                <div class="form-group">
                    <label>Currency</label>
                    <select name="currency" class="form-control" id="currency">
                            <option disabled="" selected="">Select currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}"
                                    <?php 
                                      if($currency->id == $icr_currency_symbol)
                                      { 
                                        echo "selected"; 
                                      }
                                      else{
                                        echo "";
                                      }
                                    ?>>
                                {{ $currency->currency_code}} ({{ $currency->currency_symbol }})</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <button type="button" id="change-icr_price" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-script')
<script src="{{asset('backend/assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('backend/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>
  $('.select2').select2()

    $('.datepicker').datepicker({
        autoclose: true
    })

    $('.save-setting').on("click",function() {
        var duration = $('#duration').val();
        $.ajax({
            type: 'POST',
            url: '{{ route("admin.setting") }}',
            data: {
                'duration': duration,
                '_token': "{{ csrf_token() }}"
            },
            success: function(data)
            {
                $("#modal-default").modal("hide");
                $('#appendDuration').html(data);
            }
        });
    });

    $('#trial-days').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
        $('.error-refer').hide();
    });

    $('#change-duration').click(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $.ajax({
            url: "{{ route('admin.setting.change-free-trial') }}",
            method: 'post',
            data: {
                days: $('input[name="days"]').val(),
            },
            success: function(result){
                if(result.errors)
                {
                    $.each(result.errors, function(key, value){
                        $('.error-refer').show();
                        $('.error-refer').html(value);
                    });
                }
                else
                {
                    $('.error-refer').hide();
                    $('#trial-days').modal('hide');
                    $('#appendFreePeriod').html($('input[name="days"]').val());
                }
            }
        });
    });

    $('#device-price').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
        $('.error-refer').hide();
    });

    $('#change-device_price').click(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        var device_currency = $('#device_currency').val();
        $.ajax({
            url: "{{ route('admin.setting.change-device-price') }}",
            method: 'post',
            data: {
                deviceprice: $('input[name="deviceprice"]').val(),
                device_currency : device_currency,
            },
            success: function(result){
                if(result.errors)
                {
                    $.each(result.errors, function(key, value){
                        $('.error-refer').show();
                        $('.error-refer').html(value);
                    });
                }
                else
                {
                    $('.error-refer').hide();
                    $('#device-price').modal('hide');
                     
                    var device_curr = $('input[name="deviceprice"]').val();
                    var device_split = device_curr.split('.');
                    var devicefloatValue = device_split[1] > 0 ? device_split[1] : '00';
                    var device_symbol = result.currency_symbol+' '+Math.floor(device_curr)+'.'+devicefloatValue ;
                     $('#appendDevicePrice').html(device_symbol);
                    // $('#appendDevicePrice').html($('input[name="deviceprice"]').val());
                }
            }
        });
    });

    $('#icr-price').on('hidden.bs.modal', function(){
        // $(this).find('form')[0].reset();
        $('.error-refer').hide();
    });

    $('#change-icr_price').click(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        var currency = $('#currency').val();
        $.ajax({
            url: "{{ route('admin.setting.change-icr-price') }}",
            method: 'post',
            data: {
                icrprice: $('input[name="icrprice"]').val(),
                currency: currency,

            },
            success: function(result){
                if(result.errors)
                {
                    $.each(result.errors, function(key, value){
                        $('.error-refer').show();
                        $('.error-refer').html(value);
                    });
                }
                else
                {
                    $('.error-refer').hide();
                    $('#icr-price').modal('hide');
                    var price = $('input[name="icrprice"]').val();
                    var price_split = price.split('.');
                    var floatValue = price_split[1] > 0 ? price_split[1] : '00';
                    var price_lab = result.currency_symbol+' '+Math.floor(price)+'.'+floatValue ;
                    $('#appendIcrPrice').html(price_lab);
                    //$('#appendIcrPrice').html($('input[name="icrprice"]').val());
                }
            }
        });
    });

 
</script>
@stop