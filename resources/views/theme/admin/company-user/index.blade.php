@extends('theme.admin.layouts.main')
@section('title', 'Users')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
        <section class="content-header">
            <h1>
            Users
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Users</li>
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
                    <div class="box-header">
                        <h3>Filter by company</h3>
                    </div>
                    <?php 
                        if(isset($_POST['company_id'])){
                            $companyId = $_POST['company_id'];
                        }else{
                            $companyId = '0';
                            }
                    ?>
                        <div class="box-body">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <form action="{{ url('admin/company-user')}}" method="post">
                                        {{ csrf_field() }}
                                        <label for="name" class="control-label">Company</label>
                                        <select name="company_id" id="company_id" onchange="this.form.submit()" class="form-control select2">
                                            <option value="all">All company</option>
                                            @if(isset($companies))
                                                @foreach($companies as $key => $company)
                                                    <option value="{{ $company->id }}" <?php if($company->id == $companyId) { echo "selected"; } ?> >{{ $company->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                <div class="box box-primary">
                    <!-- .box-header -->
                    <div class="box-header">
                    <!-- <h3 class="box-title">Users</h3>   -->
                    <!-- <a href="#" class="btn btn-primary btn-mini pull-right"  title="Add post"><i class="fa fa-plus"></i></a>               -->
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body ">
                    <table id="post-table" class="table-responsive table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">ID</th>
                                <th>Username</th>
                                <th>Company</th>
                                <th>Status</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($users))
                                @foreach($users as $key=>$user)
                                    <tr>
                                        <th>{{$key + 1}}</th>
                                        <td>{{$user->userInfo->username}} </td>
                                        <td>{{$user->company->name}} </td>
                                        <td>
                                            @if($user->userInfo->status == 1)
                                                Active
                                            @else
                                                On Hold
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('/admin/company-user/view-user/'.Crypt::encrypt(
$user->id)) }}" class="btn btn-primary btn-mini" title="@lang('common.View')"><i class="fa fa-eye"></i></a>
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
        $('#post-table').DataTable({
          
            columnDefs: [
                {  orderable: false, targets: 4 },
            ],
            "order": [[0, 'asc']],
            scrollX : true,
            aoColumns: [{ width:'1%'},
                    { width:'3%'},
                    { width:'3%'},
                    { width:'1%'},
                    { width:'1%'}]
        });
    })

    // $('#job').on('change', function (e) {
    //     var company_id = $(this).val();
    //     var url = '{{ url("admin/job") }}';

    //     // $('#example').DataTable( {
    //     //     "processing": true,
    //     //     "serverSide": true,
    //     //     "ajax": {
    //     //         "url": url,
    //     //         "type": "POST",
    //     //         "data": {'company_id': company_id,'_token': "{{ csrf_token() }}"},
    //     //     },
    //     //     "columns": [
    //     //         { "data": "id" },
    //     //         { "data": "last_name" },
    //     //         { "data": "position" },
    //     //         { "data": "office" },
    //     //         { "data": "start_date" },
    //     //         { "data": "salary" }
    //     //     ]
    //     // });

    //     $.ajax({
    //         type: "POST",
    //         // dataType: "json",
    //         url: url,
    //         data: {'company_id': company_id,'_token': "{{ csrf_token() }}"},
    //         success: function(data){
    //           setTimeout(function(){
    //               location.reload(1);
    //           }, 1000);
    //         }
    //     });
    // });

</script>
@stop