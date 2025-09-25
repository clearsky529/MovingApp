@extends('theme.admin.layouts.main')
@section('title', 'Companies')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Companies
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.companies') }}">Companies</a></li>
        <li class="active">View Company</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{ $all_companies }}</h3>

              <ul>
              <li><span>100</span>Approve</li>
              <li><span>50</span>Unapprove</li>
              </ul>
            </div>
            <a href="{{ route('admin.companies') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div> -->
      <form id="jobForm" action="{{ url('admin/move')}}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="company_id" value="{{ $company->id }}">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>{{ $move_count }}</h3>

              <p>Total Moves</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <!-- onclick="document.getElementById('jobForm').submit();" -->
              <div href="#" class="small-box-footer"><br><!-- More info <i class="fa fa-arrow-circle-right"></i> --></div>
          </div>
        </div>
      </form>
      <!-- <form id="userForm" action="{{ url('admin/company-user')}}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="company_id" value="{{ $company->id }}">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>{{ $user_count }}</h3>

              <p>Active User</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="#" onclick="document.getElementById('userForm').submit();" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </form> -->
        <!-- <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div> -->
      </div>
      <div class="row">

        <div class="col-lg-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <h3 class="profile-username text-center">{{$company->name}}</h3>

              <p class="text-muted text-center">Company Details</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Company</b> <a class="pull-right">{{$company->name}}</a>
                </li>
                <li class="list-group-item">
                  <b>Contact name</b> <a class="pull-right">{{$company->contact_name}}</a>
                </li>
                <!-- <li class="list-group-item">
                  <b>Contact number</b> <a class="pull-right">{{$company->contact_number}}</a>
                </li> -->
                <li class="list-group-item">
                  <b>Website</b> <a class="pull-right">{{$company->website}}</a>
                </li>
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right">{{$company->email}}</a>
                </li>
                <li class="list-group-item">
                  <b>City</b> <a class="pull-right">{{$company->cityName ? $company->cityName->name : '-'}}</a>
                </li>
                <li class="list-group-item">
                  <b>State/Province</b> <a class="pull-right">{{$company->stateName->name}}</a>
                </li>
                <li class="list-group-item">
                  <b>Country</b> <a class="pull-right">{{$company->countryName->name}}</a>
                </li>
                <li class="list-group-item">
                  <b>Type</b> <a class="pull-right">{{$company->companyType->company_type}}</a>
                </li>
                <li class="list-group-item">
                  <b>Registered date</b> <a class="pull-right">{{ date('d M Y', strtotime($company->created_at)) }}</a>
                </li>
                <li class="list-group-item">
                  <b>Plan name</b> <a class="pull-right">{{$company->subscription ? $company->subscription->title : 'N/A'}}</a>
                </li>
                @if($company->referred_by)
                <li class="list-group-item">
                  <b>Referred By</b> <a class="pull-right">{{$company->referred_by ? $get_referby->name : 'N/A'}}</a>
                </li>
                @endif
                <li class="list-group-item list-item-part">
                    <b class="reftrls">Referred Company</b>
                    <ul class="ul-parts">
                    @forelse($company->getRefferdCompany as $key => $data)
                    <li><a href="{{url('/admin/companies/view-company/'.Crypt::encrypt($data->id))}}">{{$data->name}}</a></li>
                    @empty
                    <a class="pull-right">{{'N/A'}}</a>
                    @endforelse
                    </ul>
                </li>
                <li class="list-group-item">
                  <b>Status</b> <a class="pull-right"><?php if($company->user['status'] == 1){ echo "Approved"; }else{ echo "On hold"; } ?></a>
                </li>
                 <li class="list-group-item">
                  <b>Total Created Users</b> <a class="pull-right" id="active"><span><label class="label bg-green badge-sm-cst">{{$active_user}}</label></a>

                </li>
                <li class="list-group-item">
                  <b>Total Deleted Users</b> <a class="pull-right" id="deleteid"><span><label class="label bg-green badge-sm-cst">{{$delete_user}}</label></a>
                </li>
              </ul>
              <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->

            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="box box-primary" id="create_user">
              <div class="box-header with-border">
                <h3 class="box-title" >Created User Details </h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="box-body">
                @foreach($active_user_details as $details)
                <div class="row">
                  <div class="col-lg-6">
                    <dl class="custom-listing">
                      <dt>Username :</dt>
                      <dd>{{$details->userInfo->username}}</dd>
                    </dl>
                  </div>
                  <div class="col-lg-6">
                    <dl class="custom-listing">
                      <dt>Created On :</dt>
                      <dd>{{date('d-m-Y', strtotime($details->created_at))}}</dd>
                    </dl>
                  </div>
                </div>
                @endforeach
              </div>
          </div>
          <div class="box box-primary delete_user">
            <div class="box-header with-border">
              <h3 class="box-title" >Deleted User Details </h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="box-body">
              @foreach($delete_user_details as $details)
              <div class="delete_user_main">
                <div class="delete_user_list">
                  <dl class="custom-listing">
                    <dt>Username :</dt>
                    <dd>{{$details->username}}</dd>
                  </dl>
                </div>
                <div class="delete_user_list">
                  <dl class="custom-listing">
                    <dt>Created On :</dt>
                    <dd>{{date('d-m-Y', strtotime($details->created_at))}}</dd>
                  </dl>
                </div>
                <div class="delete_user_list">
                  <dl class="custom-listing">
                    <dt>Deleted On :</dt>
                    <dd>{{date('d-m-Y', strtotime($details->deleted_at))}}</dd>
                  </dl>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
    </section>
    <!-- /.content -->
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script type="text/javascript">

    $(document).ready(function(){
         $("#create_user").hide();
         $(".delete_user").hide();
    });
    $("#active").click(function(){
         $("#create_user").show();
         $(".delete_user").hide();
    });
    $("#deleteid").click(function(){
      $(".delete_user").show();
      $("#create_user").hide();
        
    });

      //  $("#active").on("click", function(){
      //    $("#create_user").show();
      //        $("#delete_user").hide();
      // });

           
    </script>
  @endsection