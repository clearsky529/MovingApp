@extends('theme.company-admin.layouts.main')
@section('title', 'User')
@section('page-style')
</style>
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Deleted Device Details
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.user') }}">Manage Devices</a></li>
        <li class="active">Details of Deleted Devices</li>
      </ol>
    </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-lg-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title" >Deleted Device Details</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  </button>
                </div>
              </div>
              <div class="box-body">
              @foreach($user as $details)
                <div class="company-panel-deleted-user row">
                  <div class="col-lg-12">
                    <div class="col-lg-4">
                      <dl>
                        <dt>Device Name:</dt>
                        <dd>{{$details->username}}</dd>
                      </dl>
                    </div>
                    <div class="col-lg-4">
                      <dl>
                        <dt>Created On :</dt>
                        <dd>{{date('d-m-Y', strtotime($details->created_at))}}</dd>
                      </dl>
                    </div>
                    <div class="col-lg-4">
                      <dl>
                        <dt>Deleted On :</dt>
                        <dd>{{date('d-m-Y', strtotime($details->deleted_at))}}</dd>
                      </dl>
                    
                    </div>
                  </div>
                </div>
              @endforeach
              </div>
            </div>
          </div>
        </div>
      </section>
    <!-- /.content -->

    @endsection