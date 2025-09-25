@extends('theme.support-admin.layouts.main')
@section('title', 'Cms')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      CMS
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('support-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('support-admin.companies') }}">Cms</a></li>
        <li class="active">View CMS</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        
      </div>
      <div class="row">

        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$cms->title}}</h3>

              <p class="text-muted text-center">Cms Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Title</b> <a class="pull-right">{{$cms->title}}</a>
                </li>
                <li class="list-group-item">
                  <b>Field Status</b> <a class="pull-right">{{$cms->field_status}}</a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection