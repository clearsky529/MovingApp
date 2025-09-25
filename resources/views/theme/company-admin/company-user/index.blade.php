@extends('theme.company-admin.layouts.main')
@section('title', 'Device')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />

@stop
@section('content')
    
        <section class="content-header sub-div">
            <h1 class="title-s">
            Manage Devices
            </h1>
            
            <ol class="breadcrumb">
                <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Manage Devices</li>
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
        @if(Session::has('flash_message_error'))
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="alert alert-danger alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{!! session('flash_message_error') !!}</strong>
                </div>
            </div>
        @endif
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                <div class="box box-primary">
                    <!-- .box-header -->
                    <div class="box-header">
                      <a style="float: right; width: 150px;" href="{{ url('/company-admin/device/create-device') }}" type="button" class="btn btn-success">Add Device</a>
                    <!-- <h3 class="box-title">Users</h3>   -->
                    <!-- <a href="#" class="btn btn-primary btn-mini pull-right"  title="Add post"><i class="fa fa-plus"></i></a>               -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body ">
                    <table id="post-table" class="table-responsive table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">ID</th>
                                <th>Device Name</th>
                                <!-- <th>Contact No</th> -->
                                <th>Status</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($company_user))
                                @foreach($company_user as $key=>$user)
                                    <tr>
                                        <th>{{$key + 1}}</th>
                                        <td>{{$user->userInfo->username}}</td>
                                        <!-- <td>{{$user->phone}}</td> -->
                                        <td class="">
                                        <label class="switch">
                                            <input type="checkbox" data-id="{{ $user->id }}"  <?php if($user->userInfo->status == 1){ echo "checked"; } ?> class="status"  name="status" id="status">
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
                                              <li><a href="{{ url('/company-admin/device/view-device/'.Crypt::encrypt($user->id)) }}">View Device</a></li>
                                              <li><a href="{{ url('/company-admin/device/edit-device/'.Crypt::encrypt($user->id)) }}">Edit Device</a></li>
                                              <li><a href="#" data-delete_user_id="{{ $user->id }}" class="btn delete-user" style="text-align: left !important;">Delete Device</a></li>
                                              @if($user->is_login == 1)
                                              <li><a href="#" data-logout_user_id="{{ $user->id }}" class="btn logout-user" style="text-align: left !important;">Logout Device</a></li>
                                              @endif
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
<script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>

<script>
    $(function() {
        $('#post-table').DataTable({
          
            columnDefs: [
                {  orderable: false, targets: 2 },
                {  orderable: false, targets: 3 },
            ],
            "order": [[0, 'asc']],
            "initComplete": function (settings, json) {  
                $("#post-table").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
            },
            // aoColumns: [{ width:'1%'},
            //         { width:'1%'},
            //         { width:'1%'},
            //         { width:'1%'}]
        });
    })

    $('.deleteSubs').on('click',function(){
        var result = confirm("Are you sure, you want to delete record?");
        if (!result) {
            event.preventDefault();
        }
    })

    $('.status').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0; 
    var user_id = $(this).data('id'); 

    var url = '{{ url("company-admin/device/change-status") }}';
    $.ajax({
        type: "POST",
        // dataType: "json",
        url: url,
        data: {'status': status, 'user_id': user_id,'_token': "{{ csrf_token() }}"},
        success: function(data){
          
        }
    });
})

$(".delete-user").click(function () {
      var id = $(this).data('delete_user_id');
      swal({
            title: "Do you want to delete this device?",
            // text: "You want to logout this User!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Delete Device",
            cancelButtonClass: "btn btn-sm btn-danger",
            confirmButtonClass: "btn btn-sm btn-success",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
         },
         function (isConfirm) {
            if (isConfirm) {
                  var url = "{{ url('/company-admin/device/delete-device') }}" + '/' + id;
                  $.ajax({
                     url: url,
                     type: 'GET',
                     error: function () {
                        alert('Something is wrong');
                     },
                     success: function (data) {
                        swal("Delete Devices!", "Your Device has been deleted successfully.",
                              "success");
                        setTimeout(function () {
                              window.location.reload(1);
                        }, 1000);
                     }
                  });
            } else {
                window.location.reload();

            }
         });
});

$(".logout-user").click(function () {
      var id = $(this).data('logout_user_id');
      swal({
            title: "Do you want to logout this device ?",
            // text: "You want to logout this User!",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Logout Device",
            cancelButtonClass: "btn btn-sm btn-danger",
            confirmButtonClass: "btn btn-sm btn-success",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
         },
         function (isConfirm) {
            if (isConfirm) {
                  var url = "{{ url('/company-admin/device/logout-device') }}" + '/' + id;
                  $.ajax({
                     url: url,
                     type: 'GET',
                     error: function () {
                        alert('Something is wrong');
                     },
                     success: function (data) {
                        swal("Logout User!", "Your Device has been logout successfully.",
                              "success");
                        setTimeout(function () {
                              window.location.reload(1);
                        }, 1000);
                     }
                  });
            } else {
                window.location.reload();

            }
         });
});


</script>
@stop