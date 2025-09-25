@extends('theme.company-admin.layouts.main')
@section('title', 'User')
@section('page-style')
</style>
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      View Device
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.user') }}">Manage Devices</a></li>
        <li class="active">View Device</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$users->userInfo->username}}</h3>

              <p class="text-muted text-center">Device Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Device Name</b> <a class="pull-right">{{$users->userInfo->username}}</a>
                </li>
                <!-- <li class="list-group-item">
                  <b>Contact No</b> <a class="pull-right">{{$users->phone}}</a>
                </li> -->
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php if($users->userInfo->status == 1){ echo "Approved"; }else{ echo "On hold"; } ?></a>
                </li>
                <li class="list-group-item">
                  <b>Created On</b> <a class="pull-right">{{date('d-m-Y', strtotime($users->created_at))}}</a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection