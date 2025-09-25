@extends('theme.admin.layouts.main')
@section('title', 'Companies')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />

@stop
@section('content')
         <section class="content-header">
          <h1>
          Companies
            <!-- <small>Control panel</small> -->
          </h1>
          <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ route('admin.companies') }}">Companies</a></li>
            <li class="active">Archive Device Details</li>
          </ol>
        </section>
    
        <section class="content">
            
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
                                <th>Device Name</th>
                                <th>Deleted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($get_delete_data as $key=>$data)
                        <tr>
                            <td>{{$data->username}}</td>
                            <td>{{date('d-m-Y', strtotime($data->deleted_at))}}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>
            </section>
            
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
       
        aaSorting: [],
        aoColumns: [{ width:'15%'},
                    { width:'15%'}
                   ]
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