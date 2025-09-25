@extends('theme.company-admin.layouts.main')
@section('title', 'Agents')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
        <section class="content-header">
            <h1>
            Manage Agents
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Manage Agents</li>
            </ol>
        </section>
        @if(Session::has('flash_message_success'))
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="alert alert-success alert-block" style="text-align: center">
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
                    <a style="float: right; width: 150px;" href="{{ url('/company-admin/agents/create-agents') }}" type="button" class="btn btn-success">Add Agent</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body ">
                    <table id="post-table" class="table-responsive table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kika ID</th>
                                <th>Name</th>
                                <th>City</th> 
                                <th>State/Province</th> 
                                <th>Country</th>                        
                                <th>Type</th>                      
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                          @if(isset($agents))
                              @foreach($agents as $key=>$agent)
                                  <tr>
                                      <td>{{$agent->kika_id ? $agent->kika_id : 'N/A'}} </td>
                                      <td>{{$agent->company_name}} </td>
                                      <td>{{$agent->cityName ? $agent->cityName->name : '-'}}</td>
                                      <td>{{$agent->stateName ? $agent->stateName->name : '-'}}</td>
                                      <td>{{$agent->countryName ? $agent->countryName->name : '-'}}</td>
                                      <td>{{$agent->companyType ? $agent->companyType->company_type : '-'}}</td>
                                      <td class="text-center">
                                      <label class="switch">
                                          <input type="checkbox" data-id="{{ $agent->id }}"  <?php if($agent->status == 1){ echo "checked"; } ?> class="status"  name="status" id="status">
                                          <span class="slider round"></span>
                                      </label>
                                      </td>
                                      <td>
                                          <div class="btn-group" style="display: flex;">
                                            <button type="button" class="btn btn-sm btn-primary">More Actions</button>
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                              <li><a href="{{ url('company-admin/agents/view-agents/'.Crypt::encrypt($agent->id)) }}">View Agent</a></li>
                                              <li><a href="{{ url('/company-admin/agents/edit-agents/'.Crypt::encrypt($agent->id)) }}">Edit Agent</a></li>
                                              <li><a href="{{ url('company-admin/agents/delete-agents/'.Crypt::encrypt($agent->id)) }}">Delete Agent</a></li>
                                            </ul>
                                        </div>
                                      </td>
                                  </tr>
                              @endforeach
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
  $('.select2').select2()
  $('.status').change(function() {
      var status = $(this).prop('checked') == true ? 1 : 0; 
      var agents_id = $(this).data('id'); 

      var url = '{{ url("company-admin/agents/change-status") }}';
      $.ajax({
          type: "POST",
          // dataType: "json",
          url: url,
          data: {'status': status, 'company_id': agents_id,'_token': "{{ csrf_token() }}"},
          success: function(data){
            // setTimeout(function(){
            //     location.reload(1);
            // }, 1000);
          }
      });
  })
})

  $(function () {
    $('#post-table').DataTable({
      
        columnDefs: [
            {  orderable: false, targets: 6 },
            {  orderable: false, targets: 7 },
        ],
        "initComplete": function (settings, json) {  
          $("#post-table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        aaSorting: []
    });
  });

  $('.deleteAgents').on('click',function(){
      var result = confirm("Are you sure, you want to delete record?");
      if (!result) {
          event.preventDefault();
      }
  })

</script>
@stop