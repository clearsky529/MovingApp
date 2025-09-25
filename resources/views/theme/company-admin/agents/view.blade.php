@extends('theme.company-admin.layouts.main')
@section('title', 'Agents')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Manage Agents
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.agents') }}">Manage Agents</a></li>
        <li class="active">View Agent</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">

        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$agent->first_name.' '.$agent->last_name}}</h3>

              <p class="text-muted text-center">Agent Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Company name</b> <a class="pull-right">{{$agent->company_name}}</a>
                </li>
                <li class="list-group-item">
                  <b>kika ID</b> <a class="pull-right">{{$agent->kika_id ? $agent->kika_id : 'N/A'}}</a>
                </li>
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right">{{$agent->email}}</a>
                </li>
                <li class="list-group-item">
                  <b>Company type</b> <a class="pull-right">{{$agent->companyType->company_type}}</a>
                </li>
                <li class="list-group-item">
                  <b>Contact number</b> <a class="pull-right">{{$agent->phone}}</a>
                </li>
                <li class="list-group-item">
                  <b>website</b> <a class="pull-right">{{$agent->website ? $agent->website : '-'}}</a>
                </li>
                <li class="list-group-item">
                  <b>City</b> <a class="pull-right">{{$agent->cityName ? $agent->cityName->name : '-'}}</a>
                </li>
                <li class="list-group-item">
                  <b>State/Province</b> <a class="pull-right">{{$agent->stateName ? $agent->stateName->name : '-'}}</a>
                </li>
                <li class="list-group-item">
                  <b>Country</b> <a class="pull-right">{{$agent->countryName ? $agent->countryName->name : '-'}}</a>
                </li>
                <li class="list-group-item">
                  <b>Registered date</b> <a class="pull-right">{{ date('d M Y', strtotime($agent->created_at)) }}</a>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php if($agent->status == 1){ echo "Approved"; }else{ echo "On Hold"; } ?></a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection