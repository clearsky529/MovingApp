@extends('theme.company-admin.layouts.main')
@section('title', 'Device')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add Device
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.user') }}">Manage Devices</a></li>
        <li class="active">Add Device</li>
      </ol>
    </section>

    <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Device</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" action="{{ route('company-admin.user.store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="devicename">Device Name*</label>
                    <input type="text" name="devicename" value="{{ old('devicename') }}" class="form-control" id="devicename" placeholder="Enter Device Name">
                    @if ($errors->has('devicename'))
                        <span class="text-danger">{{ $errors->first('devicename') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="password">Password*</label>
                    <input type="password" name="password" value="{{ old('password') }}" class="form-control" id="password" placeholder="Enter Password">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="confirm-password">Confirm Password*</label>
                    <input type="password" name="confirm-password"  class="form-control" id="confirm-password" placeholder="Enter Confirm Password">
                    @if ($errors->has('confirm-password'))
                        <span class="text-danger">{{ $errors->first('confirm-password') }}</span>
                    @endif
                  </div>
                </div>
              </div>
<!--               <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="email">Contact No*</label>
                    <input type="text" step="any" name="phone" value="{{ old('phone') }}" class="form-control" id="phone" placeholder="Enter Contact No">
                    @if ($errors->has('phone'))
                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                    @endif
                  </div>
                </div>
              </div> -->
              <div class="box-body">
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
            
        </div>
      </div>
      
    </section>

    <!-- /.content -->

    @endsection
@section('page-script')
<!-- select2 -->
<script>
  $('.select2').select2()

 //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })

  $(document).ready(function(){
      $('.dynamic').change(function(){
        if($(this).val() != '0')
        {
          var select = $(this).attr("id");
          var value = $(this).val();
          var dependent = $(this).data('dependent');
          var _token = $('input[name="_token"]').val();
          $.ajax({
            url:"{{ route('company-admin.user.locationFetch') }}",
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
  
@stop