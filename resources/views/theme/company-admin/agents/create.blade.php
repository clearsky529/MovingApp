@extends('theme.company-admin.layouts.main')
@section('title', 'Agent')
@section('page-style')
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

/* custom model end */

</style>
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add Agent
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.agents') }}">Manage agent</a></li>
        <li class="active">Add Agent</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Agent</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" id="myform" action="{{ route('company-admin.agents.store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="kika_id">Kika ID</label>
                    <input type="text" name="kika_id" class="form-control" id="kikaID" placeholder="Enter kika ID">
                    @if ($errors->has('kika_id'))
                        <span class="text-danger">{{ $errors->first('kika_id') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="company_name">Company name*</label>
                    <input type="text" name="company_name" value="{{ !old('kika_id') ? old('company_name') : '' }}" class="form-control" id="company_name" placeholder="Enter company name">
                    @if ($errors->has('company_name'))
                        <span class="text-danger">{{ $errors->first('company_name') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="email">Email*</label>
                    <input type="email" name="email" value="{{ !old('kika_id') ? old('email') : '' }}" class="form-control" id="email" placeholder="Enter email">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="phone">Contact number</label>
                    <input type="text" value="{{ !old('kika_id') ? old('phone') : '' }}" step="any" name="phone" class="form-control" id="phone" placeholder="Enter contact number">
                    @if ($errors->has('phone'))
                        <span class="text-danger">{{ $errors->first('phone') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Status*</label>
                    <select name="status" class="form-control">
                      <option <?php if(!old('kika_id') && old('status') == "1") echo "selected"; ?> selected value="1">Active</option>
                      <option <?php if(!old('kika_id') && old('status') == "0") echo "selected"; ?> value="0">On Hold</option>
                    </select>
                    @if ($errors->has('status'))
                        <span class="text-danger">{{ $errors->first('status') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Company type*</label>
                    <select name="company_type" class="form-control select2">
                      <option disabled selected>Select company type</option>
                      @foreach($companyTypes as $companyType)
                        <option <?php if(!old('kika_id') && old('company_type') == $companyType->id) echo "selected"; ?> value="{{ $companyType->id }}">{{ $companyType->company_type}}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('company_type'))
                        <span class="text-danger">{{ $errors->first('company_type') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="website">Website*</label>
                    <input type="text" name="website" value="{{ !old('kika_id') ? old('website') : '' }}" class="form-control" id="website" placeholder="Enter website">
                    @if ($errors->has('website'))
                        <span class="text-danger">{{ $errors->first('website') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Country*</label>
                    <select name="country" id="country" class="form-control select2 dynamic" data-dependent="state">
                    <option disabled selected>All Country</option>
                    @if(isset($countries))
                        @foreach($countries as $key => $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    @endif
                    </select>
                    @if ($errors->has('country'))
                        <span class="text-danger">{{ $errors->first('country') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>State/Province*</label>
                    <select name="state" id="state" class="form-control select2 dynamic" data-dependent="city">
                    <option disabled selected>All state/province</option>
                    </select>
                    @if ($errors->has('state/province'))
                        <span class="text-danger">{{ $errors->first('state/province') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>City</label>
                    <select name="city" id="city" class="form-control select2">
                    <option disabled selected>All City</option>
                    </select>
                    @if ($errors->has('city'))
                        <span class="text-danger">{{ $errors->first('city') }}</span>
                    @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="box-footer">
                  <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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

  var global_state ;
  var global_city ;
  var global_city_dropdown ;

  $('.select2').select2()

  $('#datepicker').datepicker({
    autoclose: true,
    format: 'dd/mm/yyyy',
    locale: 'en'
  })

  $('#myform').bind('submit', function () {
    $(this).find(':input').prop('disabled', false);
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
          url:"{{ route('company-admin.agents.locationFetch') }}",
          method:"POST",
          data:{select:select, value:value, _token:_token, dependent:dependent},
          success:function(result)
          {
            $('#'+dependent).html(result);
            if (global_state) {
              $("select[name='state']").val(global_state);
            }
            if (global_city) {
              $('#city').html(global_city_dropdown);
              $("select[name='city']").val(global_city);
            }

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

  $("#kikaID").focusin(function(){
    $("#submit").prop('disabled', true);
  });

  $("#kikaID").focusout(function(){
    $("#submit").prop('disabled', false);
    var kika_id = $(this).val();
    var _token = $('input[name="_token"]').val();
    if (kika_id.length > 0) { 
      $.ajax({
        url:"{{ route('company-admin.agents.fetchAgent') }}",
        method:"POST",
        data:{kika_id:kika_id, _token:_token },
        success:function(result)
        {
          if (result.company) {
            $('#state').html(result.state);
            $("input[name='company_name']").val(result.company.name);
            $("input[name='company_name']").attr('readonly', true);
            $("input[name='email']").val(result.company.email);
            $("input[name='email']").attr('readonly', true);
            $("input[name='phone']").val(result.company.contact_number);
            $("input[name='phone']").attr('readonly', true);
            $("input[name='website']").val(result.company.website);
            $("input[name='website']").attr('readonly', true);
            $("select[name='company_type']").val(result.company.type).trigger('change');
            $("select[name='company_type']").prop("disabled", true);
            $("select[name='country']").val(result.company.country).trigger('change');
            $("select[name='country']").prop("disabled", true);
            global_state = result.company.state;
            global_city = result.company.city;
            global_city_dropdown = result.city;
            $("select[name='state']").val(result.company.state).trigger('change');
            $("select[name='state']").prop("disabled", true);
            $("select[name='city']").val(result.company.city).trigger('change');
            $("select[name='city']").prop("disabled", true);
          }
          else if(result !== null)
          {
            $('#myform')[0].reset();
            sweetAlert(result + ' is unable to be allocated jobs due to their subscription plan.');
          }
          else{
            global_state = null;
            global_city = null;
            global_city_dropdown = null;
            $("select[name='company_type']").prop("disabled", false);
            $("select[name='country']").prop("disabled", false);
            $("select[name='state']").prop("disabled", false);
            $("select[name='city']").prop("disabled", false);
            $("input[name='website']").attr("readonly", false);
            $("input[name='company_name']").attr('readonly', false);
            $("input[name='email']").attr('readonly', false);
            $("input[name='phone']").attr('readonly', false);
            $('#myform')[0].reset();
            alert('No agent found for given kika ID.');
          }
        }

      })
    }else{
      global_state = null;
      global_city = null;
      global_city_dropdown = null;
      $("input[name='company_name']").attr('readonly', false);
      $("input[name='email']").attr('readonly', false);
      $("input[name='website']").attr('readonly', false);
      $("input[name='phone']").attr('readonly', false);
      $("select[name='company_type']").prop("disabled", false);
      $("select[name='country']").prop("disabled", false);
      $("select[name='state']").prop("disabled", false);
      $("select[name='city']").prop("disabled", false);
      $('#myform')[0].reset();
    }
  });
</script>
@stop