@extends('theme.company-admin.layouts.main')
@section('title', 'Moves - Transload')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Tranship Activity
    <!-- <small>Control panel</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{ route('company-admin.move','tab_4') }}">Manage Moves</a></li>
    <li class="active">Tranship Activity</li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-lg-12">
       <div class="box box-primary">
          <div class="box-header with-border">
             <h3 class="box-title">Tranship Activity</h3>
             <!-- <div class="btn-group pull-right">
                <button type="button" class="btn btn-xs btn-primary">More Actions</button>
                <button type="button" class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li><a href="#">Separated link</a></li>
                </ul>
             </div> -->
          </div>
          <form>
             <div class="box-body">
                <div class="row">
                   <div class="col-md-12">
                     <dl class="d-inline-block w-100">
                       <div class="col-md-6 col-sm-6 col-xs-6">
                          <dt>Customer</dt>
                          <dd>{{ $transload->contact->contact_name }}</dd>
                       </div>
                       <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                          <dt>Volume</dt>
                          <dd>{{ $transload->volume }}</dd>
                       </div>
                     </dl>
                   </div>
                </div>
                <div class="row">
                   <div class="col-md-12 custom-main">
                    @foreach($transload->activity as $activity)
                      <div class="custom-view">
                         <div class="col-md-6">
                            <p><b>Transloaded on</b> {{ date('d M Y H:i a', strtotime($activity->transload_date)) }}</p>
                            <p><b>Completed on</b> {{ date('d M Y H:i a', strtotime($activity->complete_date)) }}</p>
                         </div>
                         <div class="col-md-6">
                            <p><b>To {{ $activity->to }}</b></p>
                         </div>
                      </div>
                    @endforeach
                   </div>
                </div>
             </div>
          </form>
       </div>
    </div>
  </div>
</section>

@endsection