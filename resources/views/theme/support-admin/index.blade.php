@extends('theme.support-admin.layouts.main')
@section('title', 'Dashboard')
@section('page-style')
@stop
@section('content')
  <section class="content-header">
    <h1>
      Dashboard
      <!-- <small>Control panel</small> -->
    </h1>
    <ol class="breadcrumb">
      <!-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li> -->
      <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
    </ol>
  </section>

   <section class="content">

    
    
      <!-- Small boxes (Stat box) -->
      <div class="row">
        @if(Session::has('flash_message_success'))
          <div class="col-md-12" style="margin-top: 10px;">
              <div class="alert alert-success alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{!! session('flash_message_success') !!}</strong>
              </div>
          </div>
        @endif

        @if(Session::has('flash_message_failure'))
          <div class="col-md-12" style="margin-top: 10px;">
              <div class="alert alert-danger alert-block">
                  <button type="button" class="close" data-dismiss="alert">×</button>
                  <strong>{!! session('flash_message_failure') !!}</strong>
              </div>
          </div>
        @endif
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{ $company['all'] }}</h3>

              <p>Active Companies</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('support-admin.companies') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{ $company['mobility'] }}</h3>

              <p>Active Mobility Companies</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('support-admin.companies') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow active_move_companies">
            <div class="inner">
              <h3>{{ $company['moving'] }}</h3>

              <p>Active Moving Companies</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('support-admin.companies') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-blue">
            <div class="inner">
              <h3>{{ $company['contractor'] }}</h3>

              <p>Active Contractors</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('support-admin.companies') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{ $move_count['uplift'] }}</h3>

              <p>Uplift</p>
            </div>
            <div class="icon">
              <i class="ion ion-clipboard"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_1') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

        <!-- <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-blue">
            <div class="inner">
              <h3>{{ $move_count['transit'] }}</h3>

              <p>In Transit</p>
            </div>
            <div class="icon">
              <i class="ion ion-clipboard"></i>
            </div>
            <a href="{{ route('admin.move','tab_2') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div> -->

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{ $move_count['transload'] }}</h3>

              <p>Tranship</p>
            </div>
            <div class="icon">
              <i class="ion ion-clipboard"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_4') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3>{{ $move_count['delivered'] }}</h3>

              <p>Delivery</p>
            </div>
            <div class="icon">
              <i class="ion ion-clipboard"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_3') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{ $move_count['inprogress_uplift'] }}</h3>

              <p>Uplift - In Progress</p>
            </div>
            <div class="icon">
              <i class="ion ion-clock"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_1') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{ $move_count['inprogress_delivery'] }}</h3>

              <p>Delivery - In Progess</p>
            </div>
            <div class="icon">
              <i class="ion ion-clock"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_3') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>{{ $move_count['inprogress_transload'] }}</h3>

              <p>Tranship - In Progress</p>
            </div>
            <div class="icon">
              <i class="ion ion-clock"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            <!-- <a href="{{ route('support-admin.move','tab_4') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a> -->
          </div>
        </div>

      </div>
      
    </section>
@endsection