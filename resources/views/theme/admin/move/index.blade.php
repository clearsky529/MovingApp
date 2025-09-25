@extends('theme.admin.layouts.main')
@section('title', 'Moves')
@section('page-style')
<link rel="stylesheet" href="{{asset('backend/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@stop
@section('content')
    
        <section class="content-header">
            <h1>
            Moves
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">Moves</li>
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
                    <?php 
                        if(isset($_POST['company_id'])){
                            $companyId = $_POST['company_id'];
                        }else{
                            $companyId = '0';
                        }

                        if(isset($_POST['duration'])){
                            $duration = $_POST['duration'];
                        }else{
                            $duration = '0';
                        }

                        if(isset($_POST['from_date'])){
                            $from_date = $_POST['from_date'];
                        }else{
                            $from_date = null;
                        }

                        if(isset($_POST['to_date'])){
                            $to_date = $_POST['to_date'];
                        }else{
                            $to_date = null;
                        }
                    ?>
                        <div class="box-body">
                            <form action="{{ url('admin/move')}}" method="post">
                                {{ csrf_field() }}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Company</label>
                                        <select name="company_id" id="company_id" class="form-control select2">
                                            <option value="all">All</option>
                                            @if(isset($companies))
                                                @foreach($companies as $key => $company)
                                                    <option value="{{ $company->id }}" <?php if($company->id == $companyId) { echo "selected"; } ?> >{{ $company->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From:</label>
                                        <div class="input-group date">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" name="from_date" class="form-control pull-right datepicker" id="datepicker_from" autocomplete="off" readonly <?php if($from_date) { ?> value="{{ $from_date }}" <?php } ?>>
                                        </div>
                                   </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To:</label>
                                        <div class="input-group date">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" name="to_date" class="form-control pull-right datepicker" id="datepicker_to" autocomplete="off" readonly <?php if($to_date) { ?> value="{{ $to_date }}" <?php } ?>>
                                        </div>
                                   </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="control-label">Time Period</label>
                                        <select name="duration" id="duration" class="form-control select2">
                                            <option <?php if($duration == "all") { echo "selected"; } ?> value="all">All</option>
                                            <option <?php if($duration == "last_week") { echo "selected"; } ?> value="last_week">Last week</option>
                                            <option <?php if($duration == "last_month") { echo "selected"; } ?> value="last_month">last month</option>
                                            <option <?php if($duration == "last_2month") { echo "selected"; } ?> value="last_2month">last 2 months</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4" style="margin-top: 23px;">
                                    <button type="submit" class="btn btn-primary" style="margin-right:5px; width: 100px;">Filter</button>
                                </div>
                            </form>
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
             <li>
              <a href="#tab_4" data-toggle="tab"> <b> Tranship </b></a>
             </li>
             <li>
              <a href="#tab_5" data-toggle="tab"> <b> Screen </b></a>
             </li>
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
                                            <a href="{{ url('/admin/move/view-move/'.Crypt::encrypt($move->id)) }}" class="btn btn-primary btn-mini" title="@lang('common.View')"><i class="fa fa-eye"></i></a>
                              </td>
                             </tr>
                          @endif  
                        @endforeach
                      </tbody>
                  </table>
                </div>
             </div>
          
             <!-- remove code of transhit -->
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
                              <th>Action</th>                        
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
                              <td class="text-center">
                                            <a href="{{ url('/admin/move/view-delivery/'.Crypt::encrypt($move->delivery->id)) }}" class="btn btn-primary btn-mini" title="@lang('common.View')"><i class="fa fa-eye"></i></a>
                             </td>
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
         aaSorting: []
    });
    table[2] = $('#delivery-table').DataTable({
        columnDefs: [
            {  orderable: false, targets: 7 },
        ],
        aaSorting: []
    });
    table[3] = $('#transload-table').DataTable({
        aaSorting: []
    });
    table[4] = $('#screening-table').DataTable({
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

    $('.datepicker').datepicker({
        autoclose: true
    })

    $(function() {
        $('.select2').select2()
        
    })


   
    $( "#duration" ).change(function() {
        if ($(this).val() == 'last_week') {
            var from = moment().subtract(1, 'weeks').startOf('isoWeek').format('MM/DD/YYYY');
            var to = moment().subtract(1, 'weeks').endOf('isoWeek').format('MM/DD/YYYY');
            $('#datepicker_from').val(from);
            $('#datepicker_to').val(to);
        }else if($(this).val() == 'last_month'){
            var from = moment().subtract(1, 'months').startOf('month').format('MM/DD/YYYY');
            var to = moment().subtract(1, 'months').endOf('month').format('MM/DD/YYYY');
            $('#datepicker_from').val(from);
            $('#datepicker_to').val(to);
        }else if($(this).val() == 'last_2month'){
            var from = moment().subtract(2, 'months').startOf('month').format('MM/DD/YYYY');
            var to = moment().subtract(1, 'months').endOf('month').format('MM/DD/YYYY');
            $('#datepicker_from').val(from);
            $('#datepicker_to').val(to);
        }else{
            $('#datepicker_from').val('');
            $('#datepicker_to').val('');
        }
    });

</script>
@stop