@extends('theme.company-admin.layouts.main')
@section('title', 'Moves - Uplift')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Uplift ICR
    <!-- <small>Control panel</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li><a href="{{ route('company-admin.move') }}">Manage Moves</a></li>
    <li class="active">Uplift ICR</li>
  </ol>
</section>

<section class="content">
  <div class="row">
    <div class="col-lg-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Inventory & Condition Report</h3>
        </div>
        <div class="box-body">
          <div class="col-lg-12">
            <iframe src="{{ route('company-admin.moves.uplift-icr-pdf',Crypt::encrypt($id)) }}" width="100%" height="700px"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection