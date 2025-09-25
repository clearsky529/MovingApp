@extends('theme.company-admin.layouts.main')
@section('title', 'Archived Moves')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css" />

@stop
@section('content')
    
<section class="content-header">
  <h1>Archived Moves </h1>
  <ol class="breadcrumb">
    <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
    <li class="active">Archived Moves</li>
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
@if(Session::has('flash_message_error'))
  <div class="col-md-12" style="margin-top: 10px;">
      <div class="alert alert-danger alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('flash_message_success') !!}</strong>
      </div>
  </div>
@endif
<section class="content">
  <div class="row">
    <div class="col-lg-12">
       <div class="box box-primary">
          <div class="box-header with-border">
            <a type="button" style="float: right; width: 150px;" href="{{ route('company-admin.move.create-uplift') }}" class="btn btn-success btn-sm pull-right">Create A Move</a>
          </div>
          
       </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
       <div class="nav-tabs-custom">
          <ul class="nav nav-tabs move_nav_tabs_ul" id="myTab">
             <li class="active">
              <a href="#tab_1" data-toggle="tab"> <b> Uplift </b></a>
             </li>
             <!-- <li>
              <a href="#tab_2" data-toggle="tab"> <b> Transit </b> </a>
             </li> -->
             <li>
              <a href="#tab_3" data-toggle="tab"> <b> Delivery </b></a>
             </li>
             @if($userId->kika_direct == 0)
             <li>
              <a href="#tab_4" data-toggle="tab"> <b> Tranship </b></a>
             </li>
             <li>
              <a href="#tab_5" data-toggle="tab"> <b> Screen </b></a>
             </li>
             @endif
             <li class="pull-right hide-completed">
                <div class="form-group">
                   <label>
                   <input type="checkbox" name="r1" id="toggleComplete" class="minimal v-align-top">
                   Hide Completed
                   </label>
                </div>
             </li>
          </ul>
          <div class="tab-content">
             <div class="tab-pane active" id="tab_1">
                <div class="table-responsive">
                  <table id="uplift-table" class="uplift-table table-responsive table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>Status</th>
                              <th>Date</th>
                              <th>Move Number</th>
                              <th>Customer</th> 
                              <th>Agent</th> 
                              <th>Controlling Agent</th>
                              <th>Volume</th> 
                              <th>Action</th>                        
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($moves as $move)
                          @if($move->uplift)
                            <tr <?php if($move->uplift->status == 2) { ?> data-status="Completed" <?php } ?>>
                              <td>
                                @if($move->uplift->status == 0)
                                <span class="label label-primary">Pending</span>
                                @elseif($move->uplift->status == 1)
                                <span class="label label-warning">In Progress</span>
                                @elseif($move->uplift->status == 2)
                                <span class="label label-success">Complete</span>
                                @endif
                              </td>
                              <td><?php $date = new DateTime($move->uplift->date); echo $date->format('d M Y');?></td>
                              <td>{{ $move->move_number }}</td>
                              <td>{{ $move->contact ? $move->contact->contact_name : '-'}}</td>
                              <td>{{ $move->uplift->origin_agent }}</td>
                              <td>{{ $move->controlling_agent}}</td>
                              <td>{{ $move->uplift->volume }}</td>
                              <td class="text-center">
                                    <a href="#" data-unarchive_move_id="{{ $move->id }}" class="btn btn-primary btn-mini" title="@lang('common.unarchive_move')"><i class="fa fa-undo"></i></a>
                                    <!-- <a href="{{ url('/move/unarchive-move')}}" class="btn btn-primary btn-mini" title="@lang('common.View')"></a> -->
                               </td>
                             </tr>
                          @endif  
                        @endforeach
                      </tbody>
                  </table>
                </div>
             </div>
          <!-- transhit code remove -->
             <div class="tab-pane" id="tab_3">
                <div class="table-responsive">
                  <table id="delivery-table" class="delivery-table table-responsive table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>Status</th>
                              <th>Date</th>
                              <th>Move Number</th>
                              <th>Customer</th> 
                              <th>Agent</th>
                              <th>Controlling Agent</th> 
                              <th>Volume</th> 
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($moves as $move)
                          @if($move->delivery)
                            <tr <?php if($move->delivery->status == 2) { ?> data-status="Completed" <?php } ?>>
                              <td>
                                @if($move->delivery->status == 0)
                                <span class="label label-primary">Pending</span>
                                @elseif($move->delivery->status == 1)
                                <span class="label label-warning">In Progress</span>
                                @elseif($move->delivery->status == 2)
                                <span class="label label-success">Complete</span>
                                @endif
                              </td>
                              <td><?php $date = new DateTime($move->delivery->date); echo $date->format('d M Y');?></td>
                              <td>{{ $move->move_number }}</td>
                              <td>{{ $move->contact ? $move->contact->contact_name : '-'}}</td>
                              <td>{{ $move->delivery->delivery_agent }}</td>
                              <td>{{ $move->controlling_agent }}</td>
                              <td>{{ $move->delivery->volume }}</td>
                             </tr>
                          @endif  
                        @endforeach
                      </tbody>
                  </table>
                </div>
             </div>
             <div class="tab-pane" id="tab_4">
                <div class="table-responsive">
                  <table id="transload-table" class="transload-table table-responsive table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>Status</th>
                              <th>Date</th>
                              <th>Move Number</th>
                              <th>Customer</th> 
                              <th>Controlling Agent</th>
                              <th>Volume</th> 
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($moves as $move)
                          @if($move->transload)
                            <tr <?php if($move->transload->status == 2) { ?> data-status="Completed" <?php } ?>>
                              <td>
                                @if($move->transload->status == 0)
                                <span class="label label-primary">Pending</span>
                                @elseif($move->transload->status == 1)
                                <span class="label label-warning">In Progress</span>
                                @elseif($move->transload->status == 2)
                                <span class="label label-success">Complete</span>
                                @endif
                              </td>
                              <td><?php $date = new DateTime($move->transload->created_at); echo $date->format('d M Y');?></td>
                              <!-- <td>{{ date('d M Y', strtotime($move->transload->created_at)) }}</td> -->
                              <td>{{ $move->move_number }}</td>
                              <td>{{ $move->contact ? $move->contact->contact_name : '-'}}</td>
                              <td>{{ $move->controlling_agent}}</td>
                              <td>{{ $move->transload->volume }}</td>
                             </tr>
                          @endif  
                        @endforeach
                      </tbody>
                  </table>
                </div>
             </div>
             <div class="tab-pane" id="tab_5">
                <div class="table-responsive">
                 <table id="screening-table" class="screening-table table-responsive table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>Status</th>
                              <th>Date</th>
                              <th>Move Number</th>
                              <th>Customer</th> 
                              <th>Controlling Agent</th>
                              <th>Volume</th> 
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($moves as $move)
                          @if($move->screening)
                            <tr <?php if($move->screening->status == 2) { ?> data-status="Completed" <?php } ?>>
                              <td>
                                @if($move->screening->status == 0)
                                <span class="label label-primary">Pending</span>
                                @elseif($move->screening->status == 1)
                                <span class="label label-warning">In Progress</span>
                                @elseif($move->screening->status == 2)
                                <span class="label label-success">Complete</span>
                                @endif
                              </td>
                              <td>{{ date('d M Y', strtotime($move->screening->created_at)) }}</td>
                              <td>{{ $move->move_number }}</td>
                              <td>{{ $move->contact ? $move->contact->contact_name : '-'}}</td>
                              <td>{{ $move->controlling_agent}}</td>
                              <td>{{ $move->screening->volume }}</td>
                             </tr>
                          @endif  
                        @endforeach
                      </tbody>
                  </table>
                </div>
             </div>
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

$(document).ready(function(){
  
});

$('input[type="checkbox"]').click(function(){
  if($(this).prop("checked") == true){
    sessionStorage.setItem("is_completed",true);
    console.log(sessionStorage.getItem("is_completed"));
  }
  else if($(this).prop("checked") == false){
    sessionStorage.removeItem("is_completed");
    console.log(sessionStorage.getItem("is_completed"));
  }
});

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
            setTimeout(function(){
                location.reload(1);
            }, 1000);
          }
      });
  })
})

  $(function () {
    var activeTab = '<?php echo $activeTab; ?>';
    $('#myTab a[href="' + activeTab + '"]').tab('show');
    var status = sessionStorage.getItem("is_completed");
    console.log("status", status);
    if(status){
      $('#toggleComplete').prop('checked', true).trigger("change");
    }else{
      $('#toggleComplete').prop('checked', false).trigger("change");
    }
  });

  $('#toggleComplete').change(function() {
    toggleComplete();
  });

  function toggleComplete(){

    if ($.fn.DataTable.isDataTable("#uplift-table")) {
      $('#uplift-table').DataTable().destroy();
      $('#transit-table').DataTable().destroy();
      $('#delivery-table').DataTable().destroy();
      $('#transload-table').DataTable().destroy();
      $('#screening-table').DataTable().destroy();
    }
    
    var table = [];
    table[0] = $('#uplift-table').DataTable({
        columnDefs: [
          {  orderable: false, targets: 7 },
        ],
        aaSorting: []
    });
    table[1] = $('#transit-table').DataTable({
        columnDefs: [
            {  orderable: false, targets: 5 },
        ],
        aaSorting: []
    });
    table[2] = $('#delivery-table').DataTable({
        columnDefs: [
            {  orderable: false, targets: 6 },
        ],
        aaSorting: []
    });
    table[3] = $('#transload-table').DataTable({
        columnDefs: [
            {  orderable: false, targets: 5 },
        ],
        aaSorting: []
    });
    table[4] = $('#screening-table').DataTable({
        columnDefs: [
            {  orderable: false, targets: 5 },
        ],
        aaSorting: []
    });

    if ($("#toggleComplete").is(':checked')) {
      $.each(table, function( index, value ) {
        $.fn.dataTable.ext.search.pop();
        value.draw();

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                return $(value.row(dataIndex).node()).attr('data-status') != "Completed";
            }
          );
        value.draw();
      });
    }else{
      $.each(table, function( index, value ) {
        $.fn.dataTable.ext.search.pop();
        value.draw();
      });
    }
  }

  $(document).on("click", ".change-status", function () {
     var status  = $(this).data('status');
     var move_id = $(this).data('id');
     var type = $(this).data('type');
     $("#status").val( status );
     $(".hidden-move-id").val(move_id);
     $(".move-type").val(type);
  });

  $('.submit-status').on("click",function() {
    var status  = $('#status').val();
    var type    = $('.move-type').val();
    var move_id = $('.hidden-move-id').val();
    $.ajax({
        type: 'POST',
        url: '{{ route("company-admin.move.change-status") }}',
        data: {
            'move_id': move_id,
            'status': status,
            'type': type,
            '_token': "{{ csrf_token() }}"
        },
        success: function(redirect_tab)
        {
          console.log(redirect_tab);
          $("#modal-default").modal("hide");
          window.location.replace("{{ route('company-admin.move') }}/"+redirect_tab)
        }
    });
  });


  $(".delete-inprg-move").click(function () {
      var id = $(this).data('delete_inprg_move_id');
      $('#delete_inprg_move_id').val(id);
      $('#inprg_delete_move').modal('show');
});

$(".delete-move").click(function () {
  var id = $(this).data('delete_move_id');
        // alert(id);
        $('#delete_move_id').val(id);
      $('#delete_move').modal('show');

      // alert('complete');
});

$('#delete').on("click",function (){
  $('#delete_move').modal('hide');
  $('#delete_moveId').modal('show');
});

$('#delete_id').on("click",function (){

  var id =$('#delete_move_id').val();
     
        // $('#delete_moveId').modal('show');
        $.ajax({
        type: 'get',
        url: "{{ url('company-admin/move/delete/') }}" + '/' + id,
        
        success: function()
        {
          $('#delete_moveId').modal('hide');
          swal("Your move deleted successfully!");
          window.location.reload();
        }
    });
});

$(".btn-mini").click(function () {
  var move_id = $(this).data('unarchive_move_id');
  // alert(id);
  $.ajax({
        type: 'post',
        dataType: "json",
        url: "{{ url('company-admin/move/unarchive-move/') }}",
        data:{
          'move_id': move_id,
          '_token': "{{ csrf_token() }}"
        },
        success: function(data)
        {
           if(data == 1){
              swal("Move Unarchive Sucessfully!");
            //   window.location.reload();
              setInterval(function() {window.location.reload();
                }, 2500);
           }else{
            swal('Opps! Something went wrong.');
           }
         
        }
    });
      
});

</script>
@stop