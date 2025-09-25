@extends('theme.admin.layouts.main')
@section('title', 'Subscription')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
        <section class="content-header sub-div">
            <h1 class="title-s">
            Subscriptions
            </h1>
            
            <ol class="breadcrumb">
                <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Subscriptions</li>
            </ol>
        </section>
        @if(Session::has('flash_message_success'))
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{!! session('flash_message_success') !!}</strong>
                </div>
            </div>
        @endif
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                <div class="box box-primary">
                    <!-- .box-header -->
                    <div class="box-header">
                      <a style="float: right; width: 150px;" href="{{ url('/admin/subscription/create-subscription') }}" type="button" class="btn btn-success">Add Subscription</a>
                    <!-- <h3 class="box-title">Users</h3>   -->
                    <!-- <a href="#" class="btn btn-primary btn-mini pull-right"  title="Add post"><i class="fa fa-plus"></i></a>               -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body ">
                    <table id="post-table" class="table-responsive table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-left" style="width: 50px;">ID</th>
                                <th>Name</th>
                                <th>Type</th> 
                                <th>Monthly Subscription</th>
                                <th>Extra User/Month</th>
                                <th>Free Users/Month</th>
                                <th>Status</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($subscriptions))
                                @forelse($subscriptions as $key=>$subscription)
                                    <tr>
                                        <th>{{$key + 1}}</th>
                                        <td>{{$subscription->title}} </td>
                                        <td>{{ucfirst($subscription->companyType->company_type)}} </td>
                                        <td>{{ $subscription->currency ? $subscription->currency->currency_symbol : "N/A" }}
                                        {{$subscription->monthly_price}} </td>
                                        @if($subscription->title == "Kika Direct")
                                            <td>{{ "N/A"}} </td>
                                        @else
                                            <td>{{ $subscription->currency? $subscription->currency->currency_symbol : "N/A" }} {{$subscription->addon_price}} </td>
                                        @endif
                                       <!--  <td>{{ $subscription->currency? $subscription->currency->currency_symbol : "N/A" }} {{$subscription->addon_price}} </td> -->
                                        <td>{{ $subscription->free_users ? $subscription->free_users : "N/A"}}</td>
                                        <td>
                                            @if($subscription->status == 0)
                                                De-active
                                            @else
                                                Active
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('/admin/subscription/view-subscription/'.Crypt::encrypt($subscription->id)) }}" class="btn btn-primary btn-mini" title="@lang('common.View')"><i class="fa fa-eye"></i></a>
                                            <a href="{{ url('/admin/subscription/edit-subscription/'.Crypt::encrypt($subscription->id)) }}" class="btn btn-info btn-mini" title="@lang('common.Edit')"><i class="fa fa-edit"></i></a>
                                            <a href="{{ url('/admin/subscription/delete-subscription/'.Crypt::encrypt($subscription->id)) }}" class="btn btn-danger btn-mini deleteSubs" title="@lang('common.Delete')"><i class="fa fa-trash" data-method="DELETE" data-confirm="Are you sure?"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" align="center">No record found</td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
                </div>
            </div>
            </section>
   
@endsection
@section('page-script')
<script src="{{asset('backend/assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('backend/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script>

    $(function() {
        $('#post-table').DataTable({
          
            columnDefs: [
                {  orderable: false, targets: 7 },
            ],
            "order": [[0, 'asc']],
            "initComplete": function (settings, json) {  
                $("#post-table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
        });
    })

    $('.deleteSubs').on('click',function(){
        var result = confirm("Are you sure, you want to delete record?");
        if (!result) {
            event.preventDefault();
        }
    })

</script>
@stop