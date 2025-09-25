@extends('theme.admin.layouts.main')
{{app()->setLocale(session()->get("locale"))}}
<?php $heading_title = trans('auth.user profile'); ?>
@section('title', $heading_title)
@section('page-style')
    <link rel="stylesheet" href="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@stop
@section('content')
{{app()->setLocale(session()->get("locale"))}}
    
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @lang('auth.user profile')
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i> @lang('auth.home')</a></li>
                <li class="active">@lang('auth.edit profile')</li>
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
        <!-- Main content -->
        <section class="content">

            <div class="row">
                <div class="col-md-3">
                    <div class="box box-primary">
                        <div class="box-body box-profile">
                            <?php 
                                $s3_base_url = config('filesystems.disks.s3.url');
                                $s3_image_path = $s3_base_url.'userprofile/';
                                  if(isset($user->profile_pic) && !empty($user->profile_pic))
                                  {
                                //   $path = public_path('user_image/').$user->profile_pic;

                                //   if(file_exists($path))
                                //   {
                                       $profile_img_path = $s3_image_path.$user->profile_pic;
                                //   }
                                //   else
                                //   {
                                //       $profile_img_path = asset('backend/assets/dist/img/avatar.png');
                                //   }
                                  }
                                  else
                                  {
                                      $profile_img_path = $s3_image_path.'avatar.png';
                                  }
                                // $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/'.$user->profile_pic;
                              ?>
                            <img src="{{$profile_img_path}}" id="output" class="profile-user-img img-responsive img-circle" alt="User Image">
                            <!-- <h3 class="profile-username text-center"></h3> -->
                        <p class="text-muted text-center">{{ $user->username }}</p>
                        <ul class="list-group list-group-unbordered">
                        </ul>
                        <!--  <button type="submit" class="btn btn-primary btn-block"><b>Change Profile</b></button> -->
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@lang('auth.edit profile')</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="" method="post" enctype="multipart/form-data" action="{{ url('/admin/edit-user-profile') }}">{{ csrf_field() }}
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">@lang('auth.username')</label>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="@lang('user.Enter username')" value="{{ $user->username }}">
                                            @error('username')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">@lang('auth.email')</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="@lang('user.Enter email')" value="{{ $user->email }}" readonly>
                                            @error('email')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="file_path" class=" control-label">@lang('auth.image')<label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" readonly="readonly" id="file_path" class="form-control" placeholder="{{ trans('common.Browse...')}}">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="file_browser">
                                                    <i class="fa fa-search"></i>@lang('auth.browse')</button>
                                                </span>
                                            </div>
                                        <input accept="image/*" onchange="loadFile(event)" type="file" class="hidden" id="file" name="image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <!-- <button type="submit" class="btn btn-default">Cancel</button> -->
                                <button type="submit" class="btn btn-primary pull-right">@lang('auth.save')</button>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                    </div>
                </div>
                <!--/.col (right) -->
            </div>
            <!-- /.row -->

        </section>
        <!-- /.content -->
    
@endsection
@section('page-script')
<script src="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
   $(document).ready(function(){  
    var now = new Date();
    var years_ago = new Date(now.getFullYear()-18,now.getMonth(),now.getDate());
    // alert(now.getMonth());

    startDate = new Date('1960-01-01'),
    // alert(startDate);
    endDate = new Date('2001-01-01');
        $('#dob').datepicker({
            autoclose: true,
            orientation: "bottom",
            format: 'yyyy-mm-dd',
            // startDate: startDate, //set start date
            endDate: now,
        })

      $('#file_browser').click(function(e){
          e.preventDefault();
          $('#file').click();
      });

      $('#file').change(function(){
          $('#file_path').val($(this).val());
      });

      $('#file_path').click(function(){
          $('#file_browser').click();
      });  
  });

  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
  };
</script>
@stop
