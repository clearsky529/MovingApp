@extends('theme.company-admin.layouts.main')
@section('title', 'Dashboard')
@section('page-style')
@stop
@section('content')
   <section class="content-header">
     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
       <h1 style="margin: 0;">
         Dashboard
         <!-- <small>Control panel</small> -->
       </h1>

       <!-- Email Search Form -->
       <div style="margin-right: 100px;">
         <form method="GET" action="{{ route('company-admin.home') }}" class="form-inline">
           <div class="form-group">
             <label for="search_email" class="sr-only">Search by Email</label>
             <input type="email" class="form-control" id="search_email" name="search_email"
                    placeholder="Enter email to filter data..."
                    value="{{ request('search_email') }}"
                    style="width: 250px; margin-right: 10px;">
             <button type="submit" class="btn btn-primary">
               <i class="fa fa-search"></i> Search
             </button>
             @if(request('search_email'))
               <a href="{{ route('company-admin.home') }}" class="btn btn-default" style="margin-left: 5px;">
                 <i class="fa fa-times"></i> Clear
               </a>
             @endif
           </div>
         </form>
       </div>
     </div>

     <ol class="breadcrumb">
       <!-- <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li> -->
       <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
     </ol>

     @if(request('search_email'))
       <div class="alert alert-info" style="margin-top: 10px;">
         <strong>Searching for:</strong> {{ request('search_email') }}
         @if($searched_user && $searched_company)
           <br><small>Company: {{ $searched_company->name }} | User: {{ $searched_user->name }}</small>
         @elseif($searched_user)
           <br><small>User found but no company association</small>
         @else
           <br><small>No user found with this email</small>
         @endif
       </div>
     @endif
   </section>

   <section class="content">
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
    
    @if($plan_expiry_notification)
      <div class="col-md-12" style="margin-top: 10px;">
        <div class="alert alert-danger alert-block" style="text-align: center">
            <strong>{!! $plan_expiry_notification !!}</strong>
        </div>
      </div>
    @endif

   
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $active_user }}</h3>

          <p>Active Devices</p>
        </div>
        <div class="icon">
          <i class="ion ion-person-stalker"></i>
        </div>
        <a href="{{ route('company-admin.user') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $delete_user }}</h3>

          <p class="long-text">Deleted Devices <br><small>(Current Month Only)</small></p>
        </div>
        <div class="icon">
          <i class="ion ion-person-stalker"></i>
        </div>
        <a href="{{ route('company-admin.user.userDetails') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
   

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3>{{ $jobCount['uplift'] }}</h3>

          <p>Uplift</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('company-admin.move','tab_1') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-green">
        <div class="inner">
          <h3>{{ $jobCount['delivered'] }}</h3>

          <p>Delivery</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('company-admin.move','tab_3') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    @if($companies == "")
    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ $jobCount['transload'] }}</h3>
          <p>Pending Tranship</p>
        </div>
        <div class="icon">
          <i class="ion ion-clock"></i>
        </div>
        <a href="{{ route('company-admin.move','tab_4') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ $jobCount['screen'] }}</h3>
          <p>Pending Screen</p>
        </div>
        <div class="icon">
          <i class="ion ion-clock"></i>
        </div>
        <a href="{{ route('company-admin.move','tab_5') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ $delete_move }}</h3>

          <p>Deleted Moves</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('company-admin.move') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    @endif

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>{{ $user_job }}</h3>

          <p>Total Moves</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('company-admin.move') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    
    @if($companies)

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-aqua">
        <div class="inner">
          <h3>0</h3>

          <p>Inventories Created </p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <div class="small-box bg-red">
        <div class="inner">
          <h3>{{ $delete_move }}</h3>

          <p>Deleted Inventories</p>
        </div>
        <div class="icon">
          <i class="ion ion-clipboard"></i>
        </div>
        <a href="{{ route('company-admin.move') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
      </div>
    </div>
    @endif
  </div>
  </section>
@endsection