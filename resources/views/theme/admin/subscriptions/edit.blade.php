@extends('theme.admin.layouts.main')
@section('title', 'Subscription')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Subscription
        <!-- <small>Control panel</small> -->
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.subscription') }}">Subscriptions</a></li>
        <li class="active">Edit Subscription</li>
      </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-lg-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Subscription (Monthly)</h3>
          </div>
            <!-- /.box-header -->
            <!-- form start -->
          <form role="form" action="{{ route('admin.subscription.update',$subscription->id) }}" method="post">
              {{ csrf_field() }}
            <div class="box-body">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="title">Subscription title*</label>
                  <input type="text" value="{{ old('title') ? old('title') : $subscription->title }}" name="title" class="form-control" id="title" placeholder="Enter title">
                  @if ($errors->has('title'))
                      <span class="text-danger">{{ $errors->first('title') }}</span>
                  @endif
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Type*</label>
                  <select name="company_type" class="form-control">
                    <option disabled="" selected="">Select type</option>
                    @foreach($companyTypes as $companyType)
                      <option <?php if($subscription->companyType->id == $companyType->id){ echo "selected"; } ?> value="{{ $companyType->id }}">{{ $companyType->company_type}}</option>
                    @endforeach
                  </select>
                  @if ($errors->has('company_type'))
                      <span class="text-danger">{{ $errors->first('company_type') }}</span>
                  @endif
                </div>
              </div>
            </div>
            <div class="box-body">
              <div class="col-lg-6">
                  <div class="form-group">
                    <label>Currency*</label>
                    <select name="currency" class="form-control">
                      <option disabled="" selected="">Select currency</option>
                      @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                        <?php 
                        if($subscription->currency){
                          if($subscription->currency->id == $currency->id)
                          { 
                            echo "selected"; 
                          }
                        }else{
                          echo "";
                        } 
                        ?>> 
                        {{ $currency->currency_code}} ({{ $currency->currency_symbol }})
                        </option>
                      @endforeach
                    </select>
                    @if ($errors->has('currency'))
                        <span class="text-danger">{{ $errors->first('currency') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="monthly_subscription">Monthly Subscription*</label>
                    <input type="number" min="0" value="{{ old('monthly_subscription') ? old('monthly_subscription') : $subscription->monthly_price }}" step="any" name="monthly_subscription" class="form-control" id="monthly_subscription" placeholder="Enter monthly subscription">
                    @if ($errors->has('monthly_subscription'))
                        <span class="text-danger">{{ $errors->first('monthly_subscription') }}</span>
                    @endif
                  </div>
                </div>
            </div>
              
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="extra_user_month">Extra User/Month*</label>
                    <input type="number" min="0" value="{{ old('extra_user_month') ? old('extra_user_month') : $subscription->addon_price }}" step="any" name="extra_user_month" class="form-control" id="extra_user_month" placeholder="Enter add-on price">
                    @if ($errors->has('extra_user_month'))
                        <span class="text-danger">{{ $errors->first('extra_user_month') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="free_users">Free Users/Month*</label>
                    <input type="number" min="0" value="{{ old('free_users') ? old('free_users') : $subscription->free_users }}" name="free_users" class="form-control" id="free_users" placeholder="Enter monthly free users">
                    @if ($errors->has('free_users'))
                        <span class="text-danger">{{ $errors->first('free_users') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Status*</label>
                    <select name="status" class="form-control">
                        <option disabled="" selected="">Select status</option>
                        <option <?php if($subscription->status == 0){ echo "selected"; } ?> value="0">de-active</option>
                        <option <?php if($subscription->status == 1){ echo "selected"; } ?> value="1">active</option>
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