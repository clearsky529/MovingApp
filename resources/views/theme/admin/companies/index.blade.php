@extends('theme.admin.layouts.main')
@section('title', 'Companies')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />
<style>
    table .dropdown-menu {

    }
</style>
@stop
@section('content')

        <section class="content-header">
            <h1>
            Companies
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
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
                    <div class="box-header">
                    </div>
                    <form action="{{ url('admin/companies')}}" method="post">{{ csrf_field() }}
                        <div class="box-body">
                            <div class="col-md-8">
                                <div class="form-group col-md-4">
                                    <label for="name" class="control-label">Country</label>
                                    <select name="country" id="country" class="form-control select2 dynamic" data-dependent="state">
                            <?php
                                if(isset($_POST['country'])){
                                    $country_name = $_POST['country'];
                                }else{
                                    $country_name = '0';
                                    }
                            ?>
                                        <option value="0" <?php if($country_name == '0'){ echo "selected"; } ?>>All Country</option>
                                        @if(isset($countries))
                                            @foreach($countries as $key => $country)
                                                <option value="{{ $country->id }}" <?php if($country->id == $country_name){ echo "selected"; } ?>>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('country')
                                        <span class="error">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="name" class="control-label">State/Province</label>
                                    <select name="state" id="state" class="form-control select2 dynamic" data-dependent="city">
                                        <?php
                                            if(isset($_POST['state'])){
                                                $state_name = $_POST['state'];
                                            }else{
                                                $state_name = '0';
                                            }
                                        ?>
                                        <option value="0">All state/province</option>
                                        @if($states)
                                            @foreach($states as $key => $state)
                                                <option value="{{ $state->id }}" <?php if($state->id == $state_name){ echo "selected"; } ?>>{{ $state->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('state')
                                        <span class="error">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    {{ csrf_field() }}
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="name" class="control-label">City</label>
                                    <select name="city" id="city" class="form-control select2">
                                        <?php
                                            if(isset($_POST['city'])){
                                                $city_name = $_POST['city'];
                                            }else{
                                                $city_name = '0';
                                            }
                                        ?>
                                        <option value="0">All City</option>
                                        @if($cities)
                                            @foreach($cities as $key => $city)
                                                <option value="{{ $city->id }}" <?php if($city->id == $city_name){ echo "selected"; } ?>>{{ $city->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('city')
                                        <span class="error">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4" style="margin-top: 23px;">
                            <button type="submit" class="btn btn-primary" style="margin-right:5px;">Search</button>
                            <a href="javascript:void(0);"  id="reset" class="btn btn-info">Reset</a>
                            </div>
                        </div>
                    </form>
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
                                <th>Kika ID</th>
                                <th>Company</th>
                                <th>City</th>
                                <th>State/Province</th>
                                <th>Country</th>
                                <th>Subscription Plan</th>
                                <!-- <th>Total Refferd</th>                       -->
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($companies))
                                @foreach($companies as $key=>$company)
                                    <tr>
                                        <td>{{$company->kika_id}}@if($company->referred_by)
                                            <span><label class="label bg-green badge-sm-cst" title="@lang($company->name)">R</label></span>@endif </td>
                                        <td>{{$company->name}} </td>
                                        <td>{{$company->cityName ? $company->cityName->name : '-'}}</td>
                                        <td>{{$company->stateName->name}}</td>
                                        <td>{{$company->countryName->name}}</td>
                                        <!-- <td style="text-align: center;"> <span><label class="label bg-green badge-sm-cst">{{$company->getRefferdCompany->count()}}</label></td> -->
                                        <td>{{$company->subscription ? $company->subscription->title : 'Free Trial'}}</td>
                                        <td>{{$company->companyType ? $company->companyType->company_type : '-'}}</td>
                                        <td class="text-center">
                                        <label class="switch">
                                            <input type="checkbox" data-id="{{ $company->id }}"  <?php if($company->user['status'] == 1){ echo "checked"; } ?> class="status"  name="status" id="status">
                                            <span class="slider round"></span>
                                        </label>
                                        </td>
                                        <td>
                                            <div class="btn-group more-action-display">
                                            <button type="button" class="btn btn-sm btn-primary">More Actions</button>
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                            <!-- <a href="{{ url('/admin/companies/view-company/'.Crypt::encrypt($company->id)) }}" class="btn btn-primary btn-mini" title="@lang('common.View')"><i class="fa fa-eye"></i></a> -->
                                                <li><a href="{{ url('/admin/companies/view-company/'.Crypt::encrypt($company->id)) }}">View Company</a></li>
                                                <li><a target="_blank" rel="noopener noreferrer" href="{{ route('secret-login',$company->id).'?isFromAdmin=true' }}">Login to Company</a></li>
                                                <li><a href="#" data-delete_account_id="{{ $company->id }}" class="btn delete-account" style="text-align: left !important;">Delete Company</a></li>
                                                <li><a rel="noopener noreferrer"
                                                    href="{{ url('/admin/companies/archive-company/'.Crypt::encrypt($company->id)) }}">Archive Device</a></li>
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


            {{-- model delete-account --}}
            <div class="modal fade delete-modal" id="delete_account">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <input type="hidden" id="delete_account_id" name="delete_account_id" class="hidden-account-id">
                            <form>
                                <div class="form-group">
                                    <label for="password" class="col-form-label">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Your Password">
                                    <span class="error-refer" id="msg" style="color:red; display:none;">Password is required</span>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary chk-password">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- close model --}}

            {{-- model in_prg_move --}}
            <div class="modal fade delete-modal" id="inprg_delete_account">
                <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                    <input type="hidden" id="delete_inprg_account_id" name="delete_inprg_account_id" class="hidden-account-id">
                    <div class="form-group">
                        <label for="name" class="control-label delete-title">To continue with the deletion of this account the status of all sections need to be set at Completed</label>
                    </div>
                    <div class="delete-btns">
                        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
                    </div>
                    </div>

                </div>
                </div>
            </div>
            {{-- close model --}}

@endsection
@section('page-script')
<script src="{{asset('backend/assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('backend/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
<script src="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js"></script>

<script>
$('.table-responsive').on('show.bs.dropdown', function () {
     $('.table-responsive').css( "overflow", "inherit" );
});
$(function() {
$('.select2').select2()
$('.status').change(function() {
    var status = $(this).prop('checked') == true ? 1 : 0;
    var company_id = $(this).data('id');

    var url = '{{ url("admin/companies/change-status") }}';
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
            {  orderable: false, targets: 8 },
            {  orderable: false, targets: 7 },
        ],
        aaSorting: [],
        scrollX : true,
        aoColumns: [{ width:'15%'},
                    { width:'15%'},
                    { width:'15%'},
                    { width:'10%'},
                    { width:'15%'},
                    { width:'5%'},
                    { width:'5%'},
                    { width:'5%'}]
    });
  });

  $("#reset").click(function () {
    var start = $('#community').val();
    var mode = $('#mode').val();
    var url = '{{ url("admin/companies") }}';
    $.ajax({
        type: 'POST',
        url: '{{ url("admin/companies/reset") }}',
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
      url:"{{ route('admin.companies.locationFetch') }}",
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

  $(".delete-account").click(function () {
    var id = $(this).data('delete_account_id');
    $('#delete_account_id').val(id);

    $.ajax({
      url:"{{ route('admin.companies.delete') }}",
      dataType: "json",
      method:"get",
      data:{id:id},
      success:function(data)
      {
            // console.log(data)
            if(data == 1){
                $('#delete_account').modal('show');
            }
            else
            {
                $('#inprg_delete_account').modal('show');
            }
      }

     })
});

$(".chk-password").on("click",function() {
    var id = $('#delete_account_id').val();
    var password = $("#password").val();
    if(password == ''){
        $('#msg').show();
        return false;
    }
    $.ajax({
      url:"{{ route('admin.companies.deleteAccount') }}",
      dataType: "json",
      method:"get",
      data:{password:password, id:id},
      success:function(data)
      {
        if(data == 1){
            $('#delete_account').modal('hide');
            swal("Account deleted successfully!");
            window.setTimeout(function(){location.reload()},1000);
        }else{
            $('#delete_account').modal('hide');
            swal('Opps! Password is wrong.');
            window.setTimeout(function(){location.reload()},1000);
        }
      }

     })

});

$(function() {
  changeAnchor();
});

function changeAnchor() {
  $("a[name$='aWebsiteUrl']").each(function() { // you can write your selector here
    $(this).css("background", "none");
    $(this).css("font-weight", "normal");

    var url = $(this).attr('href').trim();
    if (url == " " || url == "") { //disable empty link
      $(this).attr("class", "disabled");
      $(this).attr("href", "javascript:void(0)");
    } else {
      $(this).attr("target", "_blank");// HERE set the non-empty links, open in new window
    }
  });
}

// $(".inprg_delete-account").click(function () {
//     //   var id = $(this).data('delete_inprg_move_id');
//     //   $('#delete_inprg_move_id').val(id);
//       $('#inprg_delete_account').modal('show');
// });
</script>
@stop
