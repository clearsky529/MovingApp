@extends('theme.support-admin.layouts.main')
@section('title', 'CMS')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
        <section class="content-header">
            <h1>
            Companies
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('support-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Companies</li>
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
                      <a style="float: right; width: 150px;" href="{{ url('/support-admin/cms/create-cms') }}" type="button" class="btn btn-success">Add Device</a>
                    <!-- <h3 class="box-title">Users</h3>   -->
                    <!-- <a href="#" class="btn btn-primary btn-mini pull-right"  title="Add post"><i class="fa fa-plus"></i></a>               -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body ">
                    <table id="post-table" class="table-responsive table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>                      
                                <th>Field Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($list))
                            
                                @foreach($list as $key=>$company)
                                    <tr>
                                        <td>{{$company->title}} </td>
                                        <td>{{$company->status }} </td>
                                        <td>{{$company->field_status}} </td>
                                        <td>
                                            <div class="btn-group more-action-display">
                                            <button type="button" class="btn btn-sm btn-primary">More Actions</button>
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{  url('/support-admin/cms/view-cms/'.Crypt::encrypt($company->id)) }}">View Cms</a></li>
                                                <li><a href="{{  url('/support-admin/cms/edit-cms/'.Crypt::encrypt($company->id)) }}">Edit Cms</a></li>
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
    var company_id = $(this).data('id'); 

    var url = '{{ url("support-admin/companies/change-status") }}';
    $.ajax({
        type: "POST",
        // dataType: "json",
        url: url,
        data: {'status': status, 'company_id': company_id,'_token': "{{ csrf_token() }}"},
        success: function(data){
          // setTimeout(function(){
          //     location.reload(1);
          // }, 1000);
        }
    });
})
})

  $(function () {
    $('#post-table').removeAttr('width').DataTable({
        columnDefs: [
            // {  orderable: false, targets: 8 },
            // {  orderable: false, targets: 7 },
            {  orderable: false, targets: 3 },
        ],
        "initComplete": function (settings, json) {  
          $("#post-table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
        },
        aaSorting: [],
        // aoColumns: [{ width:'15%'},
        //             { width:'15%'},
        //             { width:'15%'},
        //             { width:'10%'},
        //             { width:'15%'},
        //             { width:'5%'},
        //             { width:'5%'},
        //             { width:'5%'}]
    });
  });

  $("#reset").click(function () {
    var start = $('#community').val();
    var mode = $('#mode').val();
    var url = '{{ url("support-admin/companies") }}';
    $.ajax({
        type: 'POST',
        url: '{{ url("support-admin/companies/reset") }}',
        data: {
        'start': start,
        'mode': mode,
        '_token': "{{ csrf_token() }}"
        },
        success: function(data)
        {
        setTimeout(function(){
            // location.reload(1);
            $(location).attr('href', url)
        }, 1000);
        }
    });
    
});

  $(document).ready(function(){

   $('.dynamic').change(function(){
    if($(this).val() != '0')
    {
     var select = $(this).attr("id");
     var value = $(this).val();
     var dependent = $(this).data('dependent');
     var _token = $('input[name="_token"]').val();
     $.ajax({
      url:"{{ route('support-admin.companies.locationFetch') }}",
      method:"POST",
      data:{select:select, value:value, _token:_token, dependent:dependent},
      success:function(result)
      {
       $('#'+dependent).html(result);
      }

     })
    }
   });

   $('#country').change(function(){
    $("#state option").remove();
    $("#city option").remove();
    $("#state").val("0");
    $("#city").val("0");
   });

   $('#state').change(function(){
    $("#city").val("0");
    $("#city option").remove();
   });
   

  });
</script>
@stop