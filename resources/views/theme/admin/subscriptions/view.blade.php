@extends('theme.admin.layouts.main')
@section('title', 'Subscription')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Subscription
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.subscription') }}">Subscription</a></li>
        <li class="active">View Subscription</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$subscription->title}}</h3>

              <p class="text-muted text-center">Subscription Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Title</b> <a class="pull-right">{{$subscription->title}}</a>
                </li>
                <li class="list-group-item">
                  <b>Type</b> <a class="pull-right">{{$subscription->companyType->company_type}}</a>
                </li>
                <li class="list-group-item">
                  <b>Currency</b> <a class="pull-right">{{$subscription->currency? $subscription->currency->currency_name : null}} ({{$subscription->currency ? $subscription->currency->currency_code : null}})</a>
                </li>
                <li class="list-group-item">
                  <b>Monthly Subscription</b> <a class="pull-right">{{$subscription->currency?$subscription->currency->currency_symbol:null}} {{$subscription->monthly_price}}</a>
                </li>
                <li class="list-group-item">
                  <b>Extra User/Month</b> 
                  @if($subscription->title == "Kika Direct")
                    {{""}}
                  @else
                    <a class="pull-right">{{$subscription->currency?$subscription->currency->currency_symbol:null}} {{$subscription->addon_price}}</a>
                  @endif
                  
                </li>
                <li class="list-group-item">
                  <b>Free Users/Month</b> <a class="pull-right">{{$subscription->free_users}}</a>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right">@if($subscription->status == 0)
                                                De-active
                                            @else
                                                Active
                                            @endif</a>
                </li>
                <li class="list-group-item">
                  <b>Registered date</b> <a class="pull-right">{{ date('d M Y', strtotime($subscription->created_at)) }}</a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
            </div>
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection