@extends('theme.admin.layouts.main')
@section('title', 'Subscription')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add Subscription
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.subscription') }}">Subscriptions</a></li>
        <li class="active">Add Subscription</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Subscription (Monthly)</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" action="{{ route('admin.subscription.store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="title">Subscription title*</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="form-control" id="title" placeholder="Enter title">
                    @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Type*</label>
                    <select name="type" class="form-control">
                      <option disabled="" selected="">Select type</option>
                      @foreach($companyTypes as $companyType)
                        <option value="{{ $companyType->id }}">{{ $companyType->company_type}}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Currency</label>
                    <select name="currency" class="form-control">
                      <option disabled="" selected="">Select currency</option>
                      @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->currency_code}} ({{ $currency->currency_symbol }})</option>
                      @endforeach
                    </select>
                    
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="monthly_subscription">Monthly Subscription</label>
                    <input type="number" min="0" value="{{ old('monthly_subscription') }}" step="any" name="monthly_subscription" class="form-control" id="monthly_subscription" placeholder="Enter monthly subscription">
                   
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="extra_user_month">Extra User/Month</label>
                    <input type="number" min="0" value="{{ old('extra_user_month') }}" step="any" name="extra_user_month" class="form-control" id="extra_user_month" placeholder="Enter Extra User/Month">
                    
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="free_users">Free Users/Month</label>
                    <input type="number" min="0" value="{{ old('free_users') }}" name="free_users" class="form-control" id="free_users" placeholder="Enter monthly free users">
                   
                  </div>
                </div>
              </div>
              <div class="box-body">
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Status*</label>
                    <select name="status" class="form-control">
                      <option disabled="" selected="">Select status</option>
                      <option value="0">De-active</option>
                      <option value="1">Active</option>
                    </select>
                    @if ($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
            
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection
@section('page-script')
<script>
 //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })
</script>
@stop