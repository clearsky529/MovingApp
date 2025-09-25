@extends('theme.support-admin.layouts.main')
@section('title', 'Moves')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      View Uplift
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('support-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('support-admin.move') }}">Moves</a></li>
        <li class="active">View Uplift</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-6">
          <div class="box box-primary">
             <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{ $uplift_move->contact->contact_name }}</h3>

              <p class="text-muted text-center">Move Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Volume</b> <a class="pull-right">{{ $uplift_move->volume }}</a>
                </li>
                <li class="list-group-item">
                  <b>Uplift Address</b> <a class="pull-right">{{$uplift_move->uplift_address}}</a>
                </li>
                <li class="list-group-item">
                  <b>Origin Agent Kika ID</b> <a class="pull-right">{{ $uplift_move->origin_agent_kika_id ? $uplift_move->origin_agent_kika_id : 'N/A' }}</a>
                </li>
                <li class="list-group-item">
                  <b>Origin Agent</b> <a class="pull-right">{{ $uplift_move->origin_agent }}</a>
                </li>
                <li class="list-group-item">
                  <b>Origin Agent Email</b> <a class="pull-right">{{ $uplift_move->origin_agent_email }}</a>
                </li>
                <li class="list-group-item">
                  <b>Date</b> <a class="pull-right"><?php $date = new DateTime($uplift_move->date); echo $date->format('d M Y');?></a>
                </li>
                <li class="list-group-item">
                  <b>Vehicle Registration</b> <a class="pull-right">{{ $uplift_move->vehicle_registration }}</a>
                </li>
                <li class="list-group-item">
                  <b>Container/Module No.</b> <a class="pull-right">{{ $uplift_move->container_number }}</a>
                </li>
                <li class="list-group-item">
                  <b>Notes</b> <a class="pull-right">{{ $uplift_move->note ? $uplift_move->note : '-' }}</a>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php if($uplift_move->status == 0){ echo "Pending"; }elseif($uplift_move->status == 1){ echo "In-progress"; }else{ echo "Complete"; } ?></a>
                </li>
              </ul>
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection