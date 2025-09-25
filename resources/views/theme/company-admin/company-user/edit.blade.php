@extends('theme.company-admin.layouts.main')
@section('title', 'User')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Edit Device
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.user') }}">Manage Devices</a></li>
        <li class="active">Edit Device</li>
      </ol>
    </section>

    <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Device</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" action="{{ route('company-admin.user.update',$user->id) }}" method="post">
              {{ csrf_field() }}
              <input type = "hidden" name="company_id" value="{{$user->id}}">
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="devicename">Device Name*</label>
                    <input type="text" name="devicename" value="{{ $user->userInfo->username }}" class="form-control" id="devicename" placeholder="Enter Device name">
                    @if ($errors->has('devicename'))
                        <span class="text-danger">{{ $errors->first('devicename') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" name="confirm_password"  class="form-control" id="confirm-password" placeholder="Confirm Password">
                    @if ($errors->has('confirm-password'))
                        <span class="text-danger">{{ $errors->first('confirm-password') }}</span>
                    @endif
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="confirm-email">Device Email</label>
                    <input type="text" name="device_email"  class="form-control" id="device-email" placeholder="Input Email" value={{ $user->userInfo->email?$user->userInfo->email:$user->company->email }}>
                    @if ($errors->has('device-email'))
                        <span class="text-danger">{{$errors->first('device-email')}}</span>
                    @endif
                  </div>
                </div>
               
              </div>
              <!-- <div class="box-body">
                

              </div> -->
              <div class="box-body">
                <div class="box-footer">
                  <!-- <input type="hidden" name="id" value = "{{$user->id}}"> -->
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
<script>
 //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })
</script>


@stop