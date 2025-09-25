<?php
  // echo "<pre>";
  // print_r($self_company);
  // exit;
?>

@extends('theme.company-admin.layouts.main')
@section('title', 'Moves - Uplift')
@section('page-style')
@stop
@section('content')

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
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add Move
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.move') }}">Manage Moves</a></li>
        <li class="active">Add Move</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
           <div class="box box-primary">
              <form class="form-horizontal" method="post" action="{{ route('company-admin.moves.store-uplift') }}">
                {{ csrf_field() }}
                <div class="box-header with-border">
                  <h3 class="box-title">Create Uplift</h3>
                  <div class="button-right-join">
                    <button type="submit" class="btn btn-primary btn-sm create-delivery">Submit & Create Delivery</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                  </div>
                </div>
                <div class="box-body">
                  <div class="col-lg-8">
                    <h4>Controlling Agent</h4>
                    <div class="form-group">
                      <label for="controllingKikaID" class="col-sm-3 control-label">Select Agent</label>
                        <div class="col-sm-9">
                          <select class="form-control select2" name="controlling_agent_kika_id" id="controllingKikaID" onchange="ControllingAgent()">
                            <option value="self" selected>{{ $self_company->name }}</option>
                            <option value="none" <?php if(old('controlling_agent_kika_id') == "none") echo "selected"; ?> >Agent Not Registered With Kika</option>
                            @foreach($kika_ids as $kika_id)
                            @if($kika_id->kika_id != $self_company->kika_id)
                              <option value="{{ $kika_id->id }}" <?php if(old('controlling_agent_kika_id') == $kika_id->id) echo "selected"; ?> >{{ $kika_id->company_name }}</option>
                            @endif
                            @endforeach
                          </select>
                        </div>
                        <input type="hidden" name="controlling_agent_kika_id" value="" id="controllingid">
                    </div>

                    <div class="form-group">
                        <label for="controllingAgentID" class="col-sm-3 control-label">Kika ID</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="controllingAgentID" placeholder="Controlling Agent Kika ID." value="{{ old('controlling_agent_id') }}" name="controlling_agent_id" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="controllingAgent" class="col-sm-3 control-label">Agent</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="controllingAgent" placeholder="Controlling Agent" value="{{ old('controlling_agent') }}" name="controlling_agent" readonly>
                           @if ($errors->has('controlling_agent'))
                            <span class="text-danger">{{ $errors->first('controlling_agent') }}</span>
                          @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="controllingAgentEmail" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                          <input type="email" class="form-control" id="controllingAgentEmail" placeholder="Controlling Agent Email" value="{{ old('controlling_agent_email') }}" name="controlling_agent_email" readonly>
                           @if ($errors->has('controlling_agent_email'))
                            <span class="text-danger">{{ $errors->first('controlling_agent_email') }}</span>
                          @endif
                        </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="box-body">
                  <div class="col-lg-8">
                    <h4>Client</h4>
                    <div class="form-group">
                      <label for="name" class="col-sm-3 control-label">Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" placeholder="Name" value="{{ old('name') }}" name="name">
                         @if ($errors->has('name'))
                          <span class="text-danger">{{ $errors->first('name') }}</span>
                        @endif
                      </div>
                    </div>
<!--                    <div class="form-group">
                      <label for="contactno" class="col-sm-3 control-label">Contact No.</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="contactno" placeholder="Contact No." value="{{ old('contact_number') }}" name="contact_number">
                          @if ($errors->has('contact_number'))
                            <span class="text-danger">{{ $errors->first('contact_number') }}</span>
                          @endif
                      </div>
                    </div>-->
                    <div class="form-group">
                      <label for="email" class="col-sm-3 control-label">Email</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" value="{{ old('email') }}" id="email" placeholder="Optional" name="email">
                          @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                          @endif
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="moveno" class="col-sm-3 control-label">Move No.</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="moveno" placeholder="Move No." value="{{ old('move_number') }}" name="move_number">
                          @if ($errors->has('move_number'))
                            <span class="text-danger">{{ $errors->first('move_number') }}</span>
                          @endif
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="box-body">
                  <div class="col-lg-8">
                    <h4>Uplift</h4>
                     @if($kikadirect_self_company != "")
                    <div class="form-group">
                      <label for="kikaID" class="col-sm-3 control-label">Select Origin Agent</label>
                        <div class="col-sm-9">
                          <select class="form-control select2" name="kika_id" id="kikaID" onchange="getKikaId()">
                            <option value="0" selected>Agent Not Registered With Kika</option>
                            @foreach($kika_ids as $kika_id)
                              @if($kika_id->company_type != 1)
                                <option value="{{ $kika_id->id }}" <?php if($self_company->kika_id == $kika_id->kika_id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        <input type="hidden" name="hidden_kika_id" value="" id="hiddenkikaid">
                    </div>
                    @else
                    <div class="form-group">
                      <label for="kikaID" class="col-sm-3 control-label">Select Origin Agent</label>
                        <div class="col-sm-9">
                          <select class="form-control select2" onchange="getKikaId()" name="kika_id" id="kikaID" >
                            <option value="0" selected>Agent Not Registered With Kika</option>
                            @foreach($kika_ids as $kika_id)
                              @if($kika_id->company_type != 1)
                                <option value="{{ $kika_id->id }}" <?php if($self_company->kika_id == $kika_id->kika_id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        <input type="hidden" name="hidden_kika_id" value="" id="hiddenkikaid">
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="originAgentID" class="col-sm-3 control-label">Origin Agent Kika ID</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="originAgentID" placeholder="Origin Agent Kika ID" value="{{ old('origin_agent_id') }}" name="origin_agent" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="originAgent" class="col-sm-3 control-label">Origin Agent</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="originAgent" placeholder="Origin Agent" value="{{ old('origin_agent') }}" name="origin_agent">
                           @if ($errors->has('origin_agent'))
                            <span class="text-danger">{{ $errors->first('origin_agent') }}</span>
                          @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="originAgentEmail" class="col-sm-3 control-label">Origin Agent Email</label>
                        <div class="col-sm-9">
                          <input type="email" class="form-control" id="originAgentEmail" placeholder="Origin Agent Email" value="{{ old('origin_agent_email') }}" name="origin_agent_email">
                           @if ($errors->has('origin_agent_email'))
                            <span class="text-danger">{{ $errors->first('origin_agent_email') }}</span>
                          @endif
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="volume" class="col-sm-3 control-label">Volume</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="volume" placeholder="Volume" value="{{ old('volume') }}" name="volume">
                          @if ($errors->has('volume'))
                            <span class="text-danger">{{ $errors->first('volume') }}</span>
                          @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="upliftaddress" class="col-sm-3 control-label">Uplift Address</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="upliftaddress" placeholder="Uplift Address" value="{{ old('uplift_address') }}" name="uplift_address">
                           @if ($errors->has('uplift_address'))
                            <span class="text-danger">{{ $errors->first('uplift_address') }}</span>
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
                          <input type="text" class="form-control pull-right datepicker" id="datepicker" value="{{ old('date') }}" name="date" readonly>
                        </div>
                        @if ($errors->has('date'))
                          <span class="text-danger">{{ $errors->first('date') }}</span>
                        @endif
                      </div>
                    </div>

                    <div class="locked-fields temporarily-hidden-fields" style="position: relative;">

                      @if($kikadirect_self_company != "")
                      <div class="form-group" hidden="true">
                        <div class="radio">
                          <div class="col-sm-offset-3 col-sm-9">
                            <label><input <?php if(old('icr_created')) echo "checked"; ?> name="icr_created" type="checkbox" id="icrCheckbox"> Inventory report completed.</label>
                          </div>
                        </div>
                      </div>
                      @else
                      <div class="form-group">
                        <div class="radio">
                          <div class="col-sm-offset-3 col-sm-9">
                            <label><input <?php if(old('icr_created')) echo "checked"; ?> name="icr_created" type="checkbox" id="icrCheckbox"> Inventory report completed.</label>
                          </div>
                        </div>
                      </div>
                      @endif

                      <div class="form-group" id="appendInput">

                      </div>
                      
                      <div class="form-group">
                        <label for="contractorKikaID" class="col-sm-3 control-label">Select Contractor</label>
                          <div class="col-sm-9">
                            <select class="form-control select2" name="contractor_agent_kika_id" id="contractorKikaID">
                              <option value="0" selected>Agent Not Registered With Kika</option>
                              @foreach($kika_ids as $kika_id)
                              @if($kika_id->company_type == 3 || $kika_id->company_type == 2)
                                <option value="{{ $kika_id->id }}" <?php if(old('contractor_agent_kika_id') == $kika_id->id) echo "selected"; ?>>{{ $kika_id->company_name }}</option>
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
                            <input type="text" class="form-control" id="contractor" placeholder="Contractor" value="{{ old('contractor') }}" name="contractor">
                             @if ($errors->has('contractor'))
                              <span class="text-danger">{{ $errors->first('contractor') }}</span>
                            @endif
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="contractor_email" class="col-sm-3 control-label">Contractor Email</label>
                          <div class="col-sm-9">
                            <input type="text" class="form-control" id="contractor_email" placeholder="Contractor Email" value="{{ old('contractor_email') }}" name="contractor_email">
                             @if ($errors->has('contractor_email'))
                              <span class="text-danger">{{ $errors->first('contractor_email') }}</span>
                            @endif
                          </div>
                      </div>
                      <div class="form-group">
                        <label for="vehicleregistration" class="col-sm-3 control-label">Vehicle Registration</label>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="vehicleregistration" placeholder="Optional" value="{{ old('vehicle_registration') }}" name="vehicle_registration">
                          @if ($errors->has('vehicle_registration'))
                            <span class="text-danger">{{ $errors->first('vehicle_registration') }}</span>
                          @endif
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="containermoduleno" class="col-sm-3 control-label">Container/Module No</label>
                        <div class="col-sm-9">
                          <input type="text" name="container_number" class="form-control" id="containermoduleno" value="{{ old('container_number') }}" placeholder="Optional">
                          @if ($errors->has('container_number'))
                            <span class="text-danger">{{ $errors->first('container_number') }}</span>
                          @endif
                        </div>
                      </div>
                      {{-- <div class="form-group">
                        <label for="note" class="col-sm-3 control-label">Note</label>
                        <div class="col-sm-9">
                          <textarea name="note" class="form-control" rows="10" placeholder="Note"></textarea>
                        </div>
                      </div> --}}

                      <div class="locked-overlay"></div>
                    </div>

                  </div>
                </div>
              </form>
           </div>
        </div>
      </div>
    </section>
    <!-- /.content -->

@endsection
@section('page-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script>
  $('.select2').select2()

  $('.datepicker').datepicker({
    autoclose: true,
    format: 'd-mm-yyyy'
  })

  function ControllingAgent(){
    var controlling_kika_id = $('#controllingKikaID').val();
    var data = $('#controllingKikaID').val(controlling_kika_id);
  }

  function getKikaId(){
    var kika_id = $('#kikaID').val();
    var get_hidden_kika_id = $('#hiddenkikaid').val(kika_id);
  }

  $( document ).ready(function() {
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    if(controllingsubscription == "Kika Direct") {
        $("#controllingKikaID").prop("disabled", true);
        $("#kikaID").prop("disabled", true);
        $("#contractorKikaID").prop("disabled", true);
        $("#contractorID").prop("disabled", true);
        $("#contractor").prop("disabled", true);
        $("#contractor_email").prop("disabled", true);
    }
    else {
      $("#controllingKikaID").prop("disabled", false);
      $("#kikaID").prop("disabled", false);
      $("#contractorKikaID").prop("disabled", false);
      $("#contractorID").prop("disabled", false);
      $("#contractor").prop("disabled", false);
      $("#contractor_email").prop("disabled", false);
    }
    toggleInput();
    var kika_id = $('#kikaID').val();
    var controlling_kika_id = $('#controllingKikaID').val();
    var contractor_kika_id  = $('#contractorKikaID').val();

    var self_company = '<?php echo $self_company->name ?>' ;
    var self_company_email = '<?php echo $self_company->email ?>' ;
    var self_kika_id = '<?php echo $self_company->kika_id ?>' ;

    if (kika_id != "0") {
      getAgent(kika_id);
    }

    if (controlling_kika_id != "self" && controlling_kika_id != "none") {
      var controllingsubscription = '<?php echo $get_subscription; ?>';
      if(controllingsubscription !== "Kika Direct"){
        getControllingAgent(controlling_kika_id);
      }
    } else if(controlling_kika_id == "self"){
      $('#controllingAgentID').val(self_kika_id);
      $('#controllingAgent').val(self_company);
      $('#controllingAgentEmail').val(self_company_email);
      $('#controllingAgent').attr('readonly', true);
      $('#controllingAgentEmail').attr('readonly', true);

      $('#originAgentID').val(self_kika_id);
      $('#originAgent').val(self_company);
      $('#originAgentEmail').val(self_company_email);
      $("#originAgent").attr('readonly', true);
      $("#originAgentEmail").attr('readonly', true);
    } else if(controlling_kika_id == "none"){
      $('#controllingAgentID').val('');
      $('#controllingAgent').val('<?php echo old('controlling_agent'); ?>');
      $('#controllingAgentEmail').val('<?php echo old('controlling_agent_email'); ?>');
      $('#controllingAgent').attr('readonly', false);
      $('#controllingAgentEmail').attr('readonly', false);

      $('#originAgentID').val(self_kika_id);
      $('#originAgent').val(self_company);
      $('#originAgentEmail').val(self_company_email);
      $("#originAgent").attr('readonly', true);
      $("#originAgentEmail").attr('readonly', true);
    }

    if (contractor_kika_id != 0) {
      getContractorAgent(contractor_kika_id);
    }

    $(".locked-fields>.locked-overlay").click(function(e) {
      e.stopPropagation();
      e.preventDefault();
      alert('Field not accessible at this time');
    });

  });

  $("#kikaID").change(function(){
    var agent_id = $(this).val();
    if (agent_id != 0) {
      getAgent(agent_id);
    }else{
      $('#originAgentID').val('');
      $('#originAgent').val('');
      $('#originAgentEmail').val('');
      $("#originAgent").attr('readonly', false);
      $("#originAgentEmail").attr('readonly', false);
    }
  });

  $("#contractorKikaID").change(function(){
    var contractor_kika_id = $(this).val();
    if (contractor_kika_id != 0) {
      getContractorAgent(contractor_kika_id);
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
            var self_id = '<?php echo $self_company->id; ?>';
            $("#kikaID").val(self_id).trigger('change');
         }
         else if(data && data[0] !== 1){
            $('#originAgentID').val(data.kika_id);
            $('#originAgent').val(data.company_name);
            $('#originAgentEmail').val(data.email);
            $("#originAgent").attr('readonly', true);
            $("#originAgentEmail").attr('readonly', true);
          }
          else{
            $('#originAgentID').val('');
            $('#originAgent').val('');
            $('#originAgentEmail').val('');
            $("#originAgent").attr('readonly', false);
            $("#originAgentEmail").attr('readonly', false);
            alert("No agent found for given kika ID!")
          }
        }
    });
  }

  $("#controllingKikaID").change(function(){
    var controlling_agent_id = $(this).val();
    var self_company = '<?php echo $self_company->name ?>' ;
    var self_company_email = '<?php echo $self_company->email ?>' ;
    var self_kika_id = '<?php echo $self_company->kika_id ?>' ;
    if (controlling_agent_id != "self" && controlling_agent_id != "none") {
      var controllingsubscription = '<?php echo $get_subscription; ?>';

      if(controllingsubscription !== "Kika Direct"){
        getControllingAgent(controlling_agent_id);
      }
    }else if(controlling_agent_id == "self"){
      $('#controllingAgentID').val(self_kika_id);
      $('#controllingAgent').val(self_company);
      $('#controllingAgentEmail').val(self_company_email);
      $('#controllingAgent').attr('readonly', true);
      $('#controllingAgentEmail').attr('readonly', true);
      $('#controllingid').val(self_kika_id);
    }else if(controlling_agent_id == "none"){
      $('#controllingAgentID').val('');
      $('#controllingAgent').val('');
      $('#controllingAgentEmail').val('');
      $('#controllingAgent').attr('readonly', false);
      $('#controllingAgentEmail').attr('readonly', false);


      $('#controllingid').val('none');
    }
  });

  function getControllingAgent(controlling_agent_id) {
    var controllingsubscription = '<?php echo $get_subscription; ?>';
    var url = '{{ route("company-admin.moves.get-agent") }}';
    $.ajax({
        type: "POST",
        url: url,
        data: {'agent_id': controlling_agent_id,'controllingsubscription' : controllingsubscription ,'_token': "{{ csrf_token() }}"},
        success: function(data){
          console.log('Data Response : ', data);
          if(data[0] == 1){
            sweetAlert(data[1].company_name + ' is unable to be allocated jobs due to their subscription plan.');
            // setTimeout(function(){
            //     location.reload(2);
            // }, 2000);
            /**
             * Dev HL
             * Start Date = 25-Sept-2021
             * Set value on hidden field
             */
            $("#controllingKikaID").val('self').trigger('change');
            $('#controllingid').val('');
            /**
             * Dev HL
             * End Date = 25-Sept-2021
             * Set value on hidden field
             */
          }
          else if(data && data[0] !== 1){
            $('#controllingAgentID').val(data.kika_id);
            $('#controllingAgent').val(data.company_name);
            $('#controllingAgentEmail').val(data.email);
            $('#controllingAgent').attr('readonly', true);
            $('#controllingAgentEmail').attr('readonly', true);
            /**
             * Dev HL
             * Start Date = 25-Sept-2021
             * Set value on hidden field
             */
            var agentId = $("#controllingKikaID").val();
            $('#controllingid').val(agentId);
            /**
             * Dev HL
             * End Date = 25-Sept-2021
             * Set value on hidden field
             */
          }
          else{
            $('#controllingAgentID').val('');
            $('#controllingAgent').val('');
            $('#controllingAgentEmail').val('');
            $('#controllingAgent').attr('readonly', false);
            $('#controllingAgentEmail').attr('readonly', false);
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
        data: {'agent_id': contractor_kika_id, 'controllingsubscription' : controllingsubscription, '_token': "{{ csrf_token() }}"},
        success: function(data){
          if(data[0] == 1){
            sweetAlert(data[1].company_name + ' is unable to be allocated jobs due to their subscription plan.');
            // setTimeout(function(){
            //     location.reload(2);
            // }, 2000);
            var self_id = '<?php echo $self_company->id; ?>';
            $("#contractorKikaID").val(self_id).trigger('change');
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
            $('#contractor_email').val('');
            alert("No agent found for given kika ID!")
          }
        }
    });
  }

  $("#icrCheckbox").change(function() {
    toggleInput();
  });

  function toggleInput() {
    if($('#icrCheckbox').prop('checked')) {
      $('#appendInput').html('<label for="containermoduleno" class="col-sm-3 control-label">Total item count</label><div class="col-sm-9"><input type="number" name="item_count" class="form-control" id="containermoduleno" value="{{ old("item_count") }}" placeholder="Total Item Count">@if ($errors->has("item_count"))<span class="text-danger">{{ $errors->first("item_count") }}</span>@endif</div>')
    }else{
      $('#appendInput').html('')
    }
  }

  $('.create-delivery').click(function() {
    $(this).closest("form").append('<input type="hidden" name="create_delivery" value="true">');
  });

</script>
@stop
