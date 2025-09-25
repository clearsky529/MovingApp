@extends('theme.admin.layouts.main')
@section('title', 'Users')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Users
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.company-user') }}">Users</a></li>
        <li class="active">View User</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$user->name}}</h3>

              <p class="text-muted text-center">User Details</p>

              <ul class="list-group list-group-unbordered">
               <!--  <li class="list-group-item">
                  <b>Full name</b> <a class="pull-right">{{$user->name}}</a>
                </li> -->
                <li class="list-group-item">
                  <b>Username</b> <a class="pull-right">{{$user->userInfo->username}}</a>
                </li>
                <li class="list-group-item">
                  <b>Company</b> <a class="pull-right">{{$user->company->name}}</a>
                </li>
               <!--  <li class="list-group-item">
                  <b>Contact number</b> <a class="pull-right">{{$user->userInfo->phone}}</a>
                </li> -->
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right">@if($user->userInfo->status == 1) Active @else On Hold @endif</a>
                </li>
                <li class="list-group-item">
                  <b>Registered date</b> <a class="pull-right">{{ date('d M Y', strtotime($user->created_at)) }}</a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection