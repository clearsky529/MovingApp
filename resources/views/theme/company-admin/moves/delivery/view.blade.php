@extends('theme.company-admin.layouts.main')
@section('title', 'Moves - Delivery')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      View Delivery
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.move','tab_3') }}">Manage Moves</a></li>
        <li class="active">View Delivery</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">

        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{ $delivery_move->move->title }}</h3>

              <p class="text-muted text-center">Move Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Volume</b> <a class="pull-right">{{ $delivery_move->volume }}</a>
                </li>
                <li class="list-group-item">
                  <b>Delivery Address</b> <a class="pull-right">{{$delivery_move->delivery_address}}</a>
                </li>
                <li class="list-group-item">
                  <b>Delivery Agent Kika ID</b> <a class="pull-right">{{ $delivery_move->delivery_agent_kika_id ? $delivery_move->delivery_agent_kika_id : 'N/A' }}</a>
                </li>
                <li class="list-group-item">
                  <b>Delivery Agent</b> <a class="pull-right">{{ $delivery_move->delivery_agent }}</a>
                </li>
                <li class="list-group-item">
                  <b>Delivery Agent Email</b> <a class="pull-right">{{ $delivery_move->delivery_agent_email }}</a>
                </li>
                <li class="list-group-item">
                  <b>Date</b> <a class="pull-right"><?php $date = new DateTime($delivery_move->date); echo $date->format('d M Y');?></a>
                </li>
                <li class="list-group-item">
                  <b>Vehicle Registration</b> <a class="pull-right">{{ $delivery_move->vehicle_registration }}</a>
                </li>
                <li class="list-group-item">
                  <b>Container/Module No.</b> <a class="pull-right">{{ $delivery_move->container_number }}</a>
                </li>
                <li class="list-group-item">
                  <b>Notes</b> <a class="pull-right">{{ $delivery_move->note ? $delivery_move->note : '-' }}</a>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php if($delivery_move->status == 0){ echo "Pending"; }elseif($delivery_move->status == 1){ echo "In-progress"; }else{ echo "On hold"; } ?></a>
                </li>
              </ul>
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection