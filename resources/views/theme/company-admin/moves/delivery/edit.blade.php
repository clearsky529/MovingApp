<?php
  // echo "<pre>";
  // print_r($delivery_move->move);
  // print_r($kika_ids);
  // exit;
?>

@extends('theme.company-admin.layouts.main')
@section('title', 'Moves - Delivery')
@section('page-style')
@stop
@section('content')
<!-- Content Header (Page header) -->
<style>
/* custom model start */
  .stop-scrolling .sweet-alert h2 {
    font-family: 'Source Sans Pro',sans-serif;
    font-weight: 500;
    font-size: 18px;
    margin: 10px 0 !important;
    line-height: 28px;
  }

  .stop-scrolling .modal {
    background: rgba(0,0,0,0.3);
  }

  .stop-scrolling .sweet-alert .confirm {
    margin-top:10px;
    padding: 8px 11px;
    background-color:#fff !important;
  }

  .stop-scrolling .sweet-alert .confirm:hover {
    background-color:#00a65a !important;
  }

  .stop-scrolling {
    background-color: transparent;
    border: 1px solid #00a65a;
    color: #000;
    font-size: 14px;
  }

  .locked-fields {
    position: relative;
    padding-top: 15px;
    padding-bottom: 15px;
    margin-bottom: 15px;
  }

  .locked-fields>.locked-overlay {
    position: absolute;
    top: 0; left: 0; right: -30px;
    width: calc(100% + 30px); height: 100%;
    z-index: 10;
    background: rgba(255,0,0,0.05); /* Subtle red tint, or adjust as needed */
    border: 2px solid red;           /* Red border for visual cue */
    cursor: not-allowed;
  }

  .temporarily-hidden-fields {
    display: none;
  }

/* custom model end */

</style>
<?php
    $userId = '';
    if(Session::get('company-admin')){
        $userId = Session::get('company-admin');
    }
    elseif(Auth::user() != null){
        $userId = Auth::user()->id;
    }
    $subscription_id = App\UserSubscription::where('user_id',$userId)->latest()->value('subscription_id');
    $get_subscription = App\Subscription::where('id',$subscription_id)->value('title');
    // dd($get_subscription);

?>
  <section class="content-header">
    <h1>
    Edit Delivery
      <!-- <small>Control panel</small> -->
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ route('company-admin.move') }}">Manage Moves</a></li>
      <li class="active">Edit Delivery</li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-lg-12">
         <div class="box box-primary">

            <form class="form-horizontal" method="post" action="{{ route('company-admin.moves.update-delivery',Crypt::encrypt($delivery_move->id)) }}">
               {{ csrf_field() }}
              <div class="box-header with-border">
                <h3 class="box-title">Edit Delivery</h3>
                <button type="submit" class="btn btn-primary btn-sm pull-right">Update</button>
              </div>
              <div class="box-body">
                  <div class="col-lg-8">
                    <h4>Client</h4>
                    <div class="form-group">
                      <label for="name" class="col-sm-3 control-label">Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" placeholder="Name" value="{{ old('name') ? old('name') : $delivery_move->contact->contact_name }}" name="name">
                         @if ($errors->has('name'))
                          <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                      </div>
                    </div>
<!--                    <div class="form-group">
                      <label for="contactno" class="col-sm-3 control-label">Contact No.</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="contactno" placeholder="Contact No." value="{{ old('contact_number') ? old('contact_number') : $delivery_move->contact->contact_number }}" name="contact_number">
                          @if ($errors->has('contact_number'))
                            <span class="text-danger">{{ $errors->first('contact_number') }}</span>
                          @endif
                      </div>
                    </div>-->
                    <div class="form-group">
                      <label for="contactname" class="col-sm-3 control-label">Move No.</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="move_number" placeholder="Move No." value="{{ old('move_number') ? old('move_number') : $move->move_number }}" name="move_number" readonly>
                         @if ($errors->has('move_number'))
                          <span class="text-danger">{{ $errors->first('move_number') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="email" class="col-sm-3 control-label">Email</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" value="{{ old('email') ? old('email') : $delivery_move->contact->email }}" id="email" placeholder="Email" name="email">
                          @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                          @endif
                      </div>
                    </div>
                  </div>
                </div>
               <div class="box-body">
                  <div class="col-lg-8">
                     <h4>Delivery</h4>
                    <div class="form-group">
                      <label for="volume" class="col-sm-3 control-label">Volume</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="volume" placeholder="Volume" value="{{ old('volume') ? old('volume') : $delivery_move->volume }}" name="volume">
                          @if ($errors->has('volume'))
                            <span class="text-danger">{{ $errors->first('volume') }}</span>
                          @endif
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="destination-address" class="col-sm-3 control-label">Destination Address</label>
                      <div class="col-sm-9">
                        <input type="text" name="destination_address" value="{{ old('destination_address') ? old('destination_address') : $delivery_move->delivery_address }}" class="form-control" id="destination-address" placeholder="Destination Address">
                        @if ($errors->has('destination_address'))
                          <span class="text-danger">{{ $errors->first('destination_address') }}</span>
                        @endif
                      </div>
                    </div>
                    <div class="form-group">
                        <label for="kikaID" class="col-sm-3 control-label text-nowrap">Select Destination Agent</label>
                        <div class="col-sm-9">
                          <select class="form-control select2" name="kika_id" id="kikaID" onchange="getKikaId()">
                            <option value="0" selected>Agent Not Registered With Kika</option>
                            @foreach($kika_ids as $kika_id)
                              <!-- <option value="{{ $kika_id->id }}" <?php if(old('kika_id') == $kika_id->id) echo "selected"; elseif( $delivery_move->move->destination_agent == $kika_id->id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>} -->
                              <option value="{{ $kika_id->id }}" <?php if(old('kika_id') == $kika_id->id) echo "selected"; elseif( $delivery_move->delivery_agent_kika_id == $kika_id->kika_id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>}
                            @endforeach
                          </select>
                        </div>
                        <input type="hidden" name="hidden_kika_id" value="" id="hiddenkikaid">
                    </div>
                    <div class="form-group">
                        <label for="destinationAgentID" class="col-sm-3 control-label">Destination Agent Kika ID</label>
                        <div class="col-sm-9">
                          <input type="text" name="destination_agent_id" value="{{$delivery_move->delivery_agent_kika_id }}" class="form-control" id="destinationAgentID" placeholder="Destination Agent kik ID." readonly>
                        </div>
                    </div>
                    @if($kikadirect_self_company != "")
                    <div class="form-group">
                        <label for="destinationAgent" class="col-sm-3 control-label">Destination Agent</label>
                        <div class="col-sm-9">
                          <input type="text" name="destination_agent" value="{{ old('destination_agent') ? old('destination_agent') : $delivery_move->delivery_agent }}" class="form-control" id="destinationAgent" placeholder="Destination Agent" readonly>
                          @if ($errors->has('destination_agent'))
                            <span class="text-danger">{{ $errors->first('destination_agent') }}</span>
                          @endif
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="destinationAgent" class="col-sm-3 control-label">Destination Agent</label>
                        <div class="col-sm-9">
                          <input type="text" name="destination_agent" value="{{ old('destination_agent') ? old('destination_agent') : $delivery_move->delivery_agent }}" class="form-control" id="destinationAgent" placeholder="Destination Agent">
                          @if ($errors->has('destination_agent'))
                            <span class="text-danger">{{ $errors->first('destination_agent') }}</span>
                          @endif
                        </div>
                    </div>
                    @endif
                    @if($kikadirect_self_company != "")
                    <div class="form-group">
                        <label for="destinationAgentEmail" class="col-sm-3 control-label">Destination Agent Email</label>
                        <div class="col-sm-9">
                          <input type="email" class="form-control" id="destinationAgentEmail" placeholder="Destination Agent Email" value="{{ old('destination_agent_email') ? old('destination_agent_email') : $delivery_move->delivery_agent_email }}" name="destination_agent_email" readonly="">
                           @if ($errors->has('destination_agent_email'))
                            <span class="text-danger">{{ $errors->first('destination_agent_email') }}</span>
                          @endif
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="destinationAgentEmail" class="col-sm-3 control-label">Destination Agent Email</label>
                        <div class="col-sm-9">
                          <input type="email" class="form-control" id="destinationAgentEmail" placeholder="Destination Agent Email" value="{{ old('destination_agent_email') ? old('destination_agent_email') : $delivery_move->delivery_agent_email }}" name="destination_agent_email" >
                           @if ($errors->has('destination_agent_email'))
                            <span class="text-danger">{{ $errors->first('destination_agent_email') }}</span>
                          @endif
                        </div>
                    </div>
                    @endif

                    @if($kikadirect_self_company != "")
                      <div class="form-group" hidden="true">
                        <div class="radio">
                          <div class="col-sm-offset-3 col-sm-9">
                              <label><input type="checkbox" <?php if ($move->required_storage == 1) { echo "checked"; } ?> name="storage"> Transhipping required.</label>
                              <label class="ml-5"><input type="checkbox" <?php if ($move->required_screening == 1) { echo "checked"; } ?> name="screening"> Screening required.</label>
                          </div>
                        </div>
                      </div>
                    @else
                      <div class="form-group">
                        <div class="radio">
                          <div class="col-sm-offset-3 col-sm-9">
                              <label><input type="checkbox" <?php if ($move->required_storage == 1) { echo "checked"; } ?> name="storage"> Transhipping required.</label>
                              <label class="ml-5"><input type="checkbox" <?php if ($move->required_screening == 1) { echo "checked"; } ?> name="screening"> Screening required.</label>
                          </div>
                        </div>
                      </div>
                    @endif

                    <div class="locked-fields temporarily-hidden-fields" style="position: relative;">

                      <div class="form-group">
                        <label for="contractorKikaID" class="col-sm-3 control-label">Select Contractor Agent</label>
                          <div class="col-sm-9">
                            <select class="form-control select2" name="contractor_agent_kika_id" id="contractorKikaID">
                              <option value="0" selected>Agent Not Registered With Kika</option>
                              @foreach($kika_ids as $kika_id)
                              @if($kika_id->company_type == 3 || $kika_id->company_type == 2)
                                <option value="{{ $kika_id->id }}" <?php if(old('contractor_agent_kika_id') == $kika_id->id) echo "selected"; elseif($delivery_move->sub_contactor && $delivery_move->sub_contactor_kika_id == $kika_id->kika_id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>
                              @endif
                              @endforeach
                            </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="contractorID" class="col-sm-3 control-label">Contractor Kika ID</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="contractorID" placeholder="Contractor Kika ID" value="{{ old('contractor_id') }}" name="contractor_id" readonly="">
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="contractor" class="col-sm-3 control-label">Contractor</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="contractor" placeholder="Contractor" value="{{ old('contractor') ? old('contractor') : $delivery_move->sub_contactor }}" name="contractor">
                            @if ($errors->has('contractor'))
                              <span class="text-danger">{{ $errors->first('contractor') }}</span>
                            @endif
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="contractor_email" class="col-sm-3 control-label">Contractor Email</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="contractor_email" placeholder="Contractor Email" value="{{ old('contractor_email') ? old('contractor_email') : $delivery_move->sub_contactor_email }}" name="contractor_email">
                            @if ($errors->has('contractor_email'))
                              <span class="text-danger">{{ $errors->first('contractor_email') }}</span>
                            @endif
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="date" class="col-sm-3 control-label">Date</label>
                        <div class="col-sm-9">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="date" value="<?php $date = new DateTime($delivery_move->date); echo $date->format('d-m-Y');?>" class="form-control pull-right datepicker" id="datepicker1" autocomplete="off" readonly>
                          </div>
                          @if ($errors->has('date'))
                            <span class="text-danger">{{ $errors->first('date') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="status" class="col-sm-3 control-label">Status</label>
                        <div class="col-sm-9">
                          <select name="status" id="status" class="form-control">
                            <option @if($delivery_move->status == 0) selected @endif value="0">Pending</option>
                            <option @if($delivery_move->status == 1) selected @endif value="1">In-progress</option>
                            <option @if($delivery_move->status == 2) selected @endif value="2">Complete</option>
                          </select>
                            @if ($errors->has('status'))
                            <span class="text-danger">{{ $errors->first('status') }}</span>
                          @endif
                        </div>
                      </div>

                      <div class="locked-overlay"></div>
                    </div>

                  </div>
               </div>
            </form>
         </div>
      </div>
    </div>
  </section>

@endsection
@section('page-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
  $('.select2').select2()

 //Date picker
  $('.datepicker').datepicker({
    autoclose: true,
    format: 'd-mm-yyyy'
  })

  function getKikaId(){
    var kika_id = $('#kikaID').val();
    $('#hiddenkikaid').val(kika_id);
  }

  $( document ).ready(function() {
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    if(controllingsubscription == "Kika Direct"){
        $("#kikaID").prop("disabled", true);
        $("#contractorKikaID").prop("disabled", true);
        $("#contractorID").prop("disabled", true);
        $("#contractor").prop("disabled", true);
        $("#contractor_email").prop("disabled", true);
    }
    else{
      $("#kikaID").prop("disabled", false);
      $("#contractorKikaID").prop("disabled", false);
      $("#contractorID").prop("disabled", false);
      $("#contractor").prop("disabled", false);
      $("#contractor_email").prop("disabled", false);
    }
    var contractor_kika_id  = $('#contractorKikaID').val();
    var kika_id = $('#kikaID').val();

    if(kika_id == 0){
      var self_kika_id = '<?php echo $self_company->kika_id ?>' ;
      $('#destinationAgentID').val(self_kika_id);

    }
    if (kika_id != 0) {
      var controllingsubscription = '<?php echo $get_subscription; ?>';
        getAgent(kika_id);
    }

    if (contractor_kika_id != 0) {
      var controllingsubscription = '<?php echo $get_subscription; ?>';
      if(controllingsubscription !== "Kika Direct"){
        getContractorAgent(contractor_kika_id);
      }
    }

    $(".locked-fields>.locked-overlay").click(function(e) {
      e.stopPropagation();
      e.preventDefault();
      alert('Field not accessible at this time');
    });

  });

  $("#kikaID").change(function(){
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    var agent_id = $(this).val();
    if (agent_id != 0) {
      if(controllingsubscription !== "Kika Direct"){
        getAgent(agent_id);
      }
    }else{
      $('#destinationAgentID').val('');
      $('#destinationAgent').val('');
      $('#destinationAgentEmail').val('');
      $("#destinationAgent").attr('readonly', false);
      $("#destinationAgentEmail").attr('readonly', false);
    }
  });

  $("#contractorKikaID").change(function(){
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    var contractor_kika_id = $(this).val();
    if (contractor_kika_id != 0) {
      if(controllingsubscription !== "Kika Direct"){
        getContractorAgent(contractor_kika_id);
      }
    }else{
      $('#contractorID').val('');
      $('#contractor').val('');
      $('#contractor_email').val('');
      $("#contractor").attr('readonly', false);
      $("#contractor_email").attr('readonly', false);
    }
  });

  function getAgent(agent_id) {
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    var url = '{{ route("company-admin.moves.get-agent") }}';
    $.ajax({
        type: "POST",
        url: url,
        data: {'agent_id': agent_id,'controllingsubscription' : controllingsubscription, '_token': "{{ csrf_token() }}"},
        success: function(data){
          if(data[0] == 1){
            sweetAlert(data[1].company_name + ' is unable to be allocated jobs due to their subscription plan.');
            // setTimeout(function(){
            //     location.reload(2);
            // }, 2000);
            var destinationAgentId = '<?php echo $delivery_move->move->destination_agent; ?>';
            $("#kikaID").val(destinationAgentId).trigger('change');
          }
          else if(data && data[0] !== 1){
            $('#destinationAgentID').val(data.kika_id);
            $('#destinationAgent').val(data.company_name);
            $('#destinationAgentEmail').val(data.email);
            $("#destinationAgent").attr('readonly', true);
            $("#destinationAgentEmail").attr('readonly', true);
          }else{
            $('#destinationAgentID').val('');
            $('#destinationAgent').val('');
            $('#destinationAgentEmail').val('');
            $("#destinationAgent").attr('readonly', false);
            $("#destinationAgentEmail").attr('readonly', false);
            alert("No agent found for given kika ID!")
          }
        }
    });
  }

  function getContractorAgent(contractor_kika_id) {
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    var url = '{{ route("company-admin.moves.get-agent") }}';
    $.ajax({
        type: "POST",
        url: url,
        data: {'agent_id': contractor_kika_id,'controllingsubscription' : controllingsubscription, '_token': "{{ csrf_token() }}"},
        success: function(data){
          if(data[0] == 1){
            sweetAlert(data[1].company_name + ' is unable to be allocated jobs due to their subscription plan.');
            // setTimeout(function(){
            //     location.reload(2);
            // }, 2000);
            // var destinationAgentId = '<?php echo $delivery_move->move->destination_agent; ?>';
            var ContractorKikaID = '<?php echo $delivery_move->sub_contactor_kika_id; ?>';
            var kikas = [];
            kikas = JSON.parse('<?php echo $kika_ids;?>');
            var ContractorValue = kikas.filter(function( ele ){
              return ele.kika_id == ContractorKikaID;
            });
            $("#contractorKikaID").val(ContractorValue[0].id).trigger('change');
            // $("#contractorKikaID").val(destinationAgentId).trigger('change');
          }
          else if(data && data[0] !== 1){
            $('#contractorID').val(data.kika_id);
            $('#contractor').val(data.company_name);
            $("#contractor").attr('readonly', true);
            $('#contractor_email').val(data.email);
            $("#contractor_email").attr('readonly', true);
          }else{
            $('#contractorID').val('');
            $('#contractor').val('');
            alert("No agent found for given kika ID!")
          }
        }
    });
  }
</script>
@stop
