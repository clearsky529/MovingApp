@extends('theme.company-admin.layouts.main')
@section('title', 'Extend subscription')
@section('page-style')
    <link rel="stylesheet" href="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@stop
@section('content')
{{app()->setLocale(session()->get("locale"))}}
    
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Extend Subscription
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i> @lang('auth.home')</a></li>
                <li class="active">Extend Subscription</li>
            </ol>
        </section>

        <section class="content">
          <div class="row">
            <div class="col-lg-6">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Extend plan</h3>
                </div>
                <form role="form" id="myform" action="{{ route('company-admin.subcription.payment') }}" method="post">
                  {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                          <label>Subscription Plan</label>
                          <select name="subscription" class="form-control">
                            <option selected disabled>Choose subscription plan</option>
                            @foreach($subscriptions as $subscription)
                                <option value="{{ $subscription->id }}" <?php if (old('subscription') == $subscription->id) { echo "selected"; } ?>>{{ $subscription->title }}</option>
                            @endforeach
                          </select>
                          @if ($errors->has('subscription'))
                              <span class="text-danger">{{ $errors->first('subscription') }}</span>
                          @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="add_on_users">Add Users</label>
                            <input type="number" name="add_on_users" value="{{old('add_on_users')}}" class="form-control" id="add_on_user" placeholder="Enter number of users to addon.">
                            @if ($errors->has('add_on_users'))
                                <span class="text-danger">{{ $errors->first('add_on_users') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="box-footer">
                          <button type="submit" class="btn btn-primary">Buy plan</button>
                        </div>
                    </div>
                </form>
              </div>
                
            </div>
          </div>
          
        </section>

        @if(Session::has('flash_message_success'))
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! session('flash_message_success') !!}</strong>
            </div>
        </div>
        @endif
    
@endsection
@section('page-script')
<script src="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
   
</script>
@stop
