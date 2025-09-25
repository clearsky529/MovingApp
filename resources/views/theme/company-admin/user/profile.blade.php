@extends('theme.company-admin.layouts.main')
{{app()->setLocale(session()->get("locale"))}}
<?php $heading_title = trans('auth.user profile'); ?>
@section('title', $heading_title)
@section('page-style')
    <link rel="stylesheet" href="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@stop
@section('content')
{{app()->setLocale(session()->get("locale"))}}

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @lang('auth.user profile')
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('company-admin/home')}}"><i class="fa fa-dashboard"></i> @lang('auth.home')</a></li>
                <li class="active">@lang('auth.edit profile')</li>
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

        @if(Session::has('flash_message_failure'))
        <div class="col-md-12" style="margin-top: 10px;">
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! session('flash_message_failure') !!}</strong>
            </div>
        </div>
        @endif
        <!-- Main content -->
        <section class="content">

            <div class="row">
                <div class="col-md-3">
                    <div>
                        <div class="box box-primary">
                            <div class="box-body box-profile">
                                <?php
                                    $s3_base_url = config('filesystems.disks.s3.url');
                                    $s3_image_path = $s3_base_url.'userprofile/';
                                      if($user->profile_pic != 'avatar.png' && !empty($user->profile_pic))
                                      {
                                            $profile_img_path = $s3_image_path.$user->profile_pic;
                                    //     $path = public_path('user_image/').$user->profile_pic;

                                    //   if(file_exists($path))
                                    //   {
                                    //     $profile_img_path = asset('user_image/').'/'.$user->profile_pic;
                                    //   }
                                    //   else
                                    //   {
                                    //     $profile_img_path = asset('backend/assets/dist/img/avatar.png');
                                    //   }
                                      }
                                      else
                                      {
                                        $profile_img_path = $s3_image_path.'avatar.png';
                                      }
                                      // $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/'.$user->profile_pic;
                                  ?>
                                <img src="{{$profile_img_path}}" id="output" class="profile-user-img img-responsive img-circle" alt="User Image">
                                <!-- <h3 class="profile-username text-center"></h3> -->
                            <p class="text-muted text-center">{{ $company->name }}</p>
                            <ul class="list-group list-group-unbordered">
                            </ul>
                            <!--  <button type="submit" class="btn btn-primary btn-block"><b>Change Profile</b></button> -->
                            </div>
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                            <h3 class="box-title"  id="title">Subscription
                                <!-- <span class="badge bg-green" id="data1" style="text-align: center;">

                                    {{ $company->subscription ? $company->subscription->title : 'Free trial' }}
                                </span> -->
                            </h3>
                           @if($company->subscription ? $company->subscription->title : '')
                            @if(($company->subscription ? $company->subscription->title : '') != 'Enterprise' || ('Enterprise' || $company->subscription ? $company->subscription->title : '') != 'Free trial' || ($company->subscription ? $company->subscription->title : '') != 'Kika Direct')
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                              </div>
                            @endif
                            @endif

                              <div class="box-header with-border" id="status">
                                  @if($get_plan_details['status'] == 'expired')
                                    <center>Your Plan has been expired.</center>
                                  @endif
                              </div>
                              <!-- /.box-tools -->
                              <!-- <a href=".route("company-admin.extend-plan") -->
                              <span class="badge plan-badge">{{ $company->subscription ? $company->subscription->title : 'Free Trial' }}</span>
                              <a href="{{route('company-admin.extend-plan')}}" type="button" class="btn btn-success change-plan">Change Plan</a>
                            </div>
                            <!-- /.box-header -->

                            @if($company->subscription ? $company->subscription->title : '')
                            @if($company->subscription->title  != 'Enterprise' && $company->subscription->title  != 'Kika Direct')
                            <div class="box-body">

                                @if($company->subscription)
                                    <ul class="nav nav-stacked">
                                        <li><a href="#" id="month-free">Monthly free users<span class="pull-right badge bg-blue" id="monthfree">{{ $company->subscription->free_users }}</span></a></li>
                                        <li><a href="#" id="add-on-user">Add-on user<span class="pull-right badge bg-blue" id="addonuser">{{$company->companySubscription ? $company->companySubscription->addon_user : 0 }}</span></a></li>
                                        <li><a href="#" id="month-price">Monthly price<span class="pull-right badge bg-blue" id="monthprice">{{ $company->subscription->currency ? $company->subscription->currency->currency_code : ''}}
                                            {{
                                        $company->subscription->currency ? $company->subscription->currency->currency_symbol : '' }}{{$company->subscription->monthly_price }}
                                    </span></a></li>
                                        <li><a href="#" id="month-price">Total price<span class="pull-right badge bg-blue" id="monthprice">{{ $company->subscription->currency ? $company->subscription->currency->currency_code : ''}}
                                        {{$company->subscription->currency ? $company->subscription->currency->currency_symbol : ''}}
                                         {{bcdiv(($company->subscription->monthly_price+($company->subscription->addon_price * $company->companySubscription->addon_user)),1,2) }}</span></a></li>
                                        <br>
                                    </ul>
                                    <form action="{{ route('company-admin.profile.add-on') }}" method="post">
                                        @csrf
                                        <input type="hidden" value="{{$company->subscription ? $company->subscription->currency ? $company->subscription->currency->currency_code : '' : ''}}" name="currency">
                                        <div class="add-part" id="add_on_user">
                                            <h3 class="user">Add on user <span class="badge bg-blue" style="text-align: center;">{{ $company->subscription ? $company->subscription->currency ? $company->subscription->currency->currency_code : '' : ''}}
                                                {{$company->subscription ? $company->subscription->currency ? $company->subscription->currency->currency_symbol : '' : ''}}
                                                {{$company->subscription->addon_price }}/User</span></h3>
                                            <div class="number">
                                                <span class="minus">-</span>
                                                <input id="AddOnUser" type="text" min="1" name="add_on_user" value="1"/>
                                                <span class="plus">+</span>
                                            </div>
                                            <div class="btn-parts">
                                                <button id="payment-request-button" type="submit" class="btn btn-success">Buy<span id="AppendAddON"></span></button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    You are currently on the Free Trail plan.
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                              <h3 class="box-title" >Refer An Agent </h3>
                              <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                              </div>
                            </div>
                            <div class="box-body">
                                <div><h5><center>Your Referral Code Is : </center></h5><center><b>
                                    {{$company->referral_code ? $company->referral_code : 'N/A'}}</b></center></div>
                                <br>
                                <div class="btn-parts">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-refer"> Refer An Agent </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@lang('auth.edit profile')</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form class="" method="post" enctype="multipart/form-data" action="{{ route('company-admin.edit-profile') }}">{{ csrf_field() }}
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Company name" value="{{ $user->company->name }}">
                                            @error('company_name')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Kika ID</label>
                                            <input type="text" class="form-control" value="{{ $user->company->kika_id }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Contact Name</label>
                                            <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Enter contact name" value="{{ $company->contact_name }}">
                                            @error('contact_name')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">Website</label>
                                            <input type="url" class="form-control" id="contact_name" name="website" placeholder="Enter website URL" value="{{ $company->website }}">
                                            @error('website')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label">@lang('auth.email')</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="@lang('user.Enter email')" value="{{ $user->email }}">
                                            @error('email')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="file_path" class=" control-label">@lang('auth.image')</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" readonly="readonly" id="file_path" class="form-control" placeholder="{{ trans('common.Browse...')}}">
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="file_browser">
                                                    <i class="fa fa-search"></i>@lang('auth.browse')</button>
                                                </span>
                                            </div>
                                        <input accept="image/*" onchange="loadFile(event)" type="file" class="hidden" id="file" name="image">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="file_path" class=" control-label">@lang('auth.icr_title_image')</label>
                                        <div class="form-group">
                                            <div class="input-group file_input_div">
                                                <div id="dropArea" class="form-control" style="cursor: pointer;">
                                                    <label for="fileInput">Choose a file or drag it here</label>
                                                </div>
                                                <input type="file" name="icr_image" id="fileInput" style="display: none;" accept="image/*">
                                                {{-- <input type="text" readonly="readonly" id="file_path" class="form-control" placeholder="@lang('auth.choose_file')"> --}}
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="button" id="icr_title_preview">@lang('auth.preview')</button>
                                                </span>
                                            </div>
                                            <span class="error error_icr_image" style="display: none">
                                                <strong>The icr image must be an image.</strong>
                                            </span>
                                            @error('icr_image')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 20px">
                                        <div class="form-group">
                                            <label for="name" class="control-label">
                                                <span style="vertical-align: super; margin-right: 5px;">@lang('auth.default_icr_title')</span>
                                                <input type="checkbox" class="default_icr_title" id="checkbox_cross_icon" {{ ($company->icr_title_toggle == 1 ? 'checked' : '') }}>
                                                <input type="hidden" name="icr_toggle" value="{{ ($company->icr_title_toggle ?? 1) }}">
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="control-label">
                                                <span style="vertical-align: super; margin-right: 5px;">@lang('auth.allow_icr_images')</span>
                                                <input type="radio" value="1" id="checkbox_cross_icon" name="is_allow_icr_image" {{ ($company->is_allow_icr_image == 1 ? 'checked' : '') }}>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="name" class="control-label">
                                                <span style="vertical-align: super; margin-right: 5px;">@lang('auth.not_allow_icr_images')</span>
                                                <input type="radio" value="0" id="checkbox_cross_icon" name="is_allow_icr_image" {{ ($company->is_allow_icr_image == 0 ? 'checked' : '') }}>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="name" class="control-label">@lang('auth.title_bar_color')</label>
                                            <div id="color-picker" style="width: fit-content;"></div>
                                            @error('color_code')
                                                <span class="error">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="append_icr_preview" style="display: none">
                                    @php
                                        $title_color_code = ($company->icr_title_toggle == 0 && $company->title_bar_color_code != null ? $company->title_bar_color_code : '#d5d5d5');

                                        $title_image = ($company->icr_title_toggle == 0 && $company->icr_title_image != null ? $company->icr_title_image : '');
                                        $s3_base_url = config('filesystems.disks.s3.url');
                                        $s3_image_path = $s3_base_url.'icrtitle/';
                                        $title_image_path = '';
                                        if($title_image != '') {
                                            $title_image_path = $s3_image_path.$title_image;
                                        }
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div><img class="icr_title_preview_image" src="{{$title_image_path}}" style="display: {{($title_image != '' ? 'block' : 'none')}}"></div>
                                            <div class="title_bar_bg" style="width: 720px;background-color: {{$title_color_code}};padding: 0px 5px;border: 1px solid #000;font-size: 15px;margin-top: 15px;">
                                                <div class="row">
                                                    <div class="col-md-9">Inventory And Condition Report - Jacob Kramer : 251207 - Preview</div>
                                                    <div class="col-md-3" style="text-align: right">Page 1 of 1</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <!-- <button type="submit" class="btn btn-default">Cancel</button> -->
                                <button type="submit" class="btn btn-primary pull-right">@lang('auth.save')</button>
                            </div>
                            <!-- /.box-footer -->
                        </form>
                    </div>
                </div>
                <!--/.col (right) -->
            </div>
            <!-- /.row -->

        </section>
        <!-- /.content -->
        <div class="modal fade" id="modal-refer">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Refer An Agent</h4>
                    </div>
                    <div class="modal-body">
                        <form id="refer-form" method="post" action="JavaScript:Void(0);">
                        @csrf
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" name="refer-email" class="form-control" id="inputEmail3" placeholder="Email">
                                        <span class="error-refer" style="color:red; display:none;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="button" id="refer-friend" class="btn btn-primary">Send Referral Link</button>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('page-script')
<script src="{{ asset('backend/assets/plugins/stripe/js/stripe_v3.js') }}"></script>
<script src="{{ asset('backend/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/blockUI/blockUI.js') }}"></script>
<script>
   $(document).ready(function(){
    AppendPrice();
    $('.minus').click(function () {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.plus').click(function () {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
    var now = new Date();
    var years_ago = new Date(now.getFullYear()-18,now.getMonth(),now.getDate());
    // alert(now.getMonth());

    var plan = '<?php echo $get_plan_details['status'];?>';

    if(plan == 'expired')
    {
        $("#data1").removeClass();
        $("#data1").addClass('badge bg-red');
        $("#monthfree").removeClass();
        $("#monthfree").addClass('pull-right badge bg-red');
        $("#addonuser").removeClass();
        $("#addonuser").addClass('pull-right badge bg-red');
        $("#monthprice").removeClass();
        $("#monthprice").addClass('pull-right badge bg-red');
        $("#title").css("color","red");
        $("#status").css("color","red");
        $("#month-free").css("color","red");
        $("#add-on-user").css("color","red");
        $("#month-price").css("color","red");
        $("#add_on_user").hide();
    }

    startDate = new Date('1960-01-01'),
    // alert(startDate);
    endDate = new Date('2001-01-01');
        $('#dob').datepicker({
            autoclose: true,
            orientation: "bottom",
            format: 'yyyy-mm-dd',
            // startDate: startDate, //set start date
            endDate: now,
        })

        $('#file_browser').click(function(e){
            e.preventDefault();
            $('#file').click();
        });

        $('#file').change(function(){
            $('#file_path').val($(this).val());
        });

        $('#file_path').click(function(){
              $('#file_browser').click();
        });
    });

    $('#AddOnUser').change(function() {
        AppendPrice();
    });

    $('#file_path').click(function(){
        $('#file_browser').click();
    });


    function AppendPrice() {
        var userPrice = '{{ $company->subscription ? $company->subscription->addon_price : 0 }}';
        var currency  = '{{ $company->subscription ? $company->subscription->currency ? $company->subscription->currency->currency_code : '' : ''}}{{$company->subscription ? $company->subscription->currency ? $company->subscription->currency->currency_symbol : '' : ''}}';
        var addonUserValue = $('#AddOnUser').val();
        var finalPrice = addonUserValue*userPrice;
        if (addonUserValue <= 0) {
            $('#payment-request-button').attr('disabled',true);
            $('#AppendAddON').html(currency +' ' + 0.00.toFixed(2) )
        }else{
            $('#payment-request-button').attr('disabled',false);
            $('#AppendAddON').html(currency +' ' + finalPrice.toFixed(2));
        }
    }

    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
    };

    $('#modal-refer').on('hidden.bs.modal', function(){
        $(this).find('form')[0].reset();
        $('.error-refer').hide();
    });

    $('#refer-friend').click(function () {
        $("#refer-friend").prop("disabled", true);
    })

    $('#refer-friend').click(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            }
        });
        $.ajax({
            url: "{{ route('company-admin.refer-friend') }}",
            method: 'post',
            data: {
                email: $('input[name="refer-email"]').val(),
            },
            success: function(result){
                if(result.errors)
                {
                    $.each(result.errors, function(key, value){
                        $('.error-refer').show();
                        $('.error-refer').html(value);
                    });
                }
                else
                {
                    $('.error-refer').hide();
                    $('input[name="refer-email"]').val('')
                    $('#modal-refer').modal('hide');

                    window.location.href = "{{ route('company-admin.profile') }}";
                }
            }
        });
    });

    $(document).ready(function(){
        if($('#checkbox_cross_icon').prop('checked') == true) {
            $('#dropArea').css('background-color', '#eee');
            $('#fileInput').attr('disabled',true);
            $('#icr_title_preview').attr('disabled',true);
            $('.color_picker_div').attr('disabled',true);
            $('.append_icr_preview').css('display','none');
        } else {
            fileUpload();
        }
    });

    // Default ICR Title Checkbox


    // ICR title image - file drag-drop
    function fileUpload()
    {
        let dropArea = document.getElementById('dropArea');
        let fileInput = document.getElementById('fileInput');

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.style.backgroundColor = '#f0f0f0';
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.style.backgroundColor = '#ffffff';
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            if($('#checkbox_cross_icon').prop('checked') == false) {
                dropArea.style.backgroundColor = '#ffffff';

                let files = event.dataTransfer.files;
                fileInput.files = files;
                uploadFiles();
            }
        });

        fileInput.addEventListener('change', () => {
            uploadFiles();
        });

        function uploadFiles() {
            let files = fileInput.files;
            $('#dropArea label').text(files[0]['name']);
            image_preview(files);
        }
    }

    // color picker palette
    var selected_color_code = "{{ ($company->icr_title_toggle == 0 && $company->title_bar_color_code != null ? $company->title_bar_color_code : '#d5d5d5') }}";

    (function(root, factory) {
        /* jshint strict:false */
        /* global define:false, module:false*/
        if (typeof define === 'function' && define.amd) {
            // Register as an anonymous AMD module
            define([], factory)
        } else if (typeof module === 'object' && typeof module.exports === 'object') {
            // Register CommonJS-ish module (should work with Component & Browserify)
            module.exports = factory()
        } else {
            // Register as a browser global
            root.EightBitColorPicker = factory()
        }
    }(this, function() {
        'use strict';

        /**
         * Class name used for identifying color picker elements
         */
        var className = 'eight-bit-color-picker'

        /**
         * Helper function to check if the argument is a DOM element object
         *
         * @param {Any} el - Object to test
         * @returns {Mixed} Truthy or false value
         */
        var isDOMElement = function(el) {
            return el && el.nodeType && el.tagName
        }

        /**
         * Generates a random color from 0-255
         *
         * @returns {Number}
         */
        var randomColor = function() {
            return Math.floor(Math.random() * 256)
        }

        /**
         * Checks if color is an integer in the range of 0-255
         *
         * @param {Number} color
         * @returns {Boolean}
         */
        var isColorInRange = function(color) {
            return (
                typeof color === 'number' &&
                Math.floor(color) === color &&
                color >= 0 &&
                color <= 255
            )
        }

        /**
         * Default color palette used by the widget
         */
        var defaultPalette = [
            '#400000', '#400000', '#400900', '#234000', '#004000', '#004000', '#004000',
            '#000d40', '#000040', '#000040', '#000040', '#000040', '#280040', '#400003',
            '#400000', '#000000', '#540000', '#540000', '#541d00', '#375400', '#005400',
            '#005400', '#005402', '#002154', '#000054', '#000054', '#000054', '#000054',
            '#3c0054', '#540017', '#540000', '#0d0d0d', '#680000', '#680000', '#683100',
            '#4b6800', '#006800', '#006800', '#006816', '#003568', '#001168', '#000068',
            '#000068', '#000068', '#500068', '#68002b', '#680000', '#212121', '#7c0000',
            '#7c0000', '#7c4500', '#5f7c00', '#0b7c00', '#007c00', '#007c2a', '#00497c',
            '#00257c', '#00007c', '#00007c', '#10007c', '#64007c', '#7c003f', '#7c0000',
            '#353535', '#900000', '#900400', '#905900', '#739000', '#1f9000', '#009000',
            '#00903e', '#005d90', '#003990', '#000090', '#000090', '#240090', '#780090',
            '#900053', '#900000', '#494949', '#a40000', '#a41800', '#a46d00', '#87a400',
            '#33a400', '#00a400', '#00a452', '#0071a4', '#004da4', '#0000a4', '#0000a4',
            '#3800a4', '#8c00a4', '#a40067', '#a40013', '#5d5d5d', '#b80000', '#b82c00',
            '#b88100', '#9bb800', '#47b800', '#00b800', '#00b866', '#0085b8', '#0061b8',
            '#000db8', '#0000b8', '#4c00b8', '#a000b8', '#b8007b', '#b80027', '#717171',
            '#cc0000', '#cc4000', '#cc9500', '#afcc00', '#5bcc00', '#06cc00', '#00cc7a',
            '#0099cc', '#0075cc', '#0021cc', '#0c00cc', '#6000cc', '#b400cc', '#cc008f',
            '#cc003b', '#858585', '#e00000', '#e05400', '#e0a900', '#c3e000', '#6fe000',
            '#1ae000', '#00e08e', '#00ade0', '#0089e0', '#0035e0', '#2000e0', '#7400e0',
            '#c800e0', '#e000a3', '#e0004f', '#999999', '#f41414', '#f46814', '#f4bd14',
            '#d7f414', '#83f414', '#2ef414', '#14f4a2', '#14c1f4', '#149df4', '#1449f4',
            '#3414f4', '#8814f4', '#dc14f4', '#f414b7', '#f41463', '#adadad', '#ff2828',
            '#ff7c28', '#ffd128', '#ebff28', '#97ff28', '#42ff28', '#28ffb6', '#28d5ff',
            '#28b1ff', '#285dff', '#4828ff', '#9c28ff', '#f028ff', '#ff28cb', '#ff2877',
            '#c1c1c1', '#ff3c3c', '#ff903c', '#ffe53c', '#ffff3c', '#abff3c', '#56ff3c',
            '#3cffca', '#3ce9ff', '#3cc5ff', '#3c71ff', '#5c3cff', '#b03cff', '#ff3cff',
            '#ff3cdf', '#ff3c8b', '#d5d5d5', '#ff5050', '#ffa450', '#fff950', '#ffff50',
            '#bfff50', '#6aff50', '#50ffde', '#50fdff', '#50d9ff', '#5085ff', '#7050ff',
            '#c450ff', '#ff50ff', '#ff50f3', '#ff509f', '#e9e9e9', '#ff6464', '#ffb864',
            '#ffff64', '#ffff64', '#d3ff64', '#7eff64', '#64fff2', '#64ffff', '#64edff',
            '#6499ff', '#8464ff', '#d864ff', '#ff64ff', '#ff64ff', '#ff64b3', '#fdfdfd',
            '#ff7878', '#ffcc78', '#ffff78', '#ffff78', '#e7ff78', '#92ff78', '#78ffff',
            '#78ffff', '#78ffff', '#78adff', '#9878ff', '#ec78ff', '#ff78ff', '#ff78ff',
            '#ff78c7', '#ffffff', '#ff8c8c', '#ffe08c', '#ffff8c', '#ffff8c', '#fbff8c',
            '#a6ff8c', '#8cffff', '#8cffff', '#8cffff', '#8cc1ff', '#ac8cff', '#ff8cff',
            '#ff8cff', '#ff8cff', '#ff8cdb', '#ffffff'
        ]

        /**
         * Constructor for Color Picker object
         *
         * Takes in an options hash with various properties
         *
         * @constructor
         * @param {Object} opts - Object containing color picker options
         * @param {DOMElement|String} opts.el - Reference to DOMElement or an id
         * @param {String[]} [opts.palette] - List of 256 hex colors to use as color palette
         * @param {String|Number} [opts.color] - Value from 0-255 to use as initial color value
         */
        function EightBitColorPicker(opts) {
            // Initialize instance variables
            this.el = isDOMElement(opts.el) ? opts.el : document.getElementById(opts.el)
            this.palette = opts.palette || defaultPalette
            this.color = parseInt(opts.color || this.el.dataset.color || randomColor(), 10)

            // Validate own values
            this.validate()

            // Render color-picker UI
            render.call(this)
        }

        // Reference protoype in a variable to improve minification
        var pickerProto = EightBitColorPicker.prototype

        /**
         * Renders color-picker UI and modifies the HTML of the element
         */
        var render = function() {
            // Set class on element
            this.el.classList.add(className)

            // Set inner HTML with subnodes based on a template
            buildSubNodes.call(this)

            // Populates and builds color map for picker
            buildColorPickerUI.call(this)

            // Declare exitListener
            var exitListener

            // Bind listener to show color map on click
            this.el.addEventListener('click', (function() {
                this.show()

                // Bind exit listener to hide map when clicked elsewhere
                if (exitListener) {
                    return
                }
                exitListener = (function(e) {
                    if (this.el.contains(e.target)) {
                        return
                    }
                    this.hide()
                    window.removeEventListener('click', exitListener)
                    exitListener = null
                }).bind(this)
                window.addEventListener('click', exitListener)
            }).bind(this))
        }

        /**
         * Sets innerHTML of an EightBitColorPicker's element with a template
         */
        var buildSubNodes = function() {
            this.el.innerHTML =
                '<div class="ebcp-selection" style="background: ' + selected_color_code + ';display: inline-block;margin-right: 10px;">' +
                '&nbsp;' +
                '</div>' +
                '<button type="button" class="color_picker_div">v</button>'+
                '<div class="ebcp-selector" style="z-index: 1;"">' +
                '<div class="ebcp-palette"></div>' +
                '<div class="ebcp-preview-values">' +
                '<div class="ebcp-text-container">' +
                '<input name="color_code" type="text" class="ebcp-text ebcp-hex-color" value="' + selected_color_code + '">' +
                '</div>' +
                '<div class="ebcp-color-preview" style="background: ' + selected_color_code + ';">' +
                '&nbsp;' +
                '</div>' +
                '</div>' +
                '</div>'
        }

        /**
         * Convenience proxy to picker element's addEventListener function
         */
        pickerProto.addEventListener = function(type, listener, useCapture) {
            this.el.addEventListener(type, listener, useCapture)
        }

        /**
         * Convenience proxy to picker element's removeEventListener function
         */
        pickerProto.removeEventListener = function(type, listener, useCapture) {
            this.el.removeEventListener(type, listener, useCapture)
        }

        /**
         * Updates the value of this.color and its representations
         *
         * @param {Number|String} color - The color from 0-255 to use
         * @param {Boolean} [previewOnly] - Only updates preview representation if truthy
         */
        pickerProto.updateColor = function(color, previewOnly) {
            var eightBitColor = parseInt(color, 10),
                colorAsString = eightBitColor.toString(),
                elements = this.loadSelectors()
            if (!color || !colorAsString.length || colorAsString.length > 3) {
                return
            }
            if (!isColorInRange(eightBitColor)) {
                return
            }
            if (eightBitColor === this.color) {
                return
            }

            var twentyFourBitColor = this.palette[eightBitColor]

            // If not preview only, then update this.color & dispatch change event
            if (!previewOnly) {
                var event = new CustomEvent('colorChange', {
                    detail: {
                        oldColor: this.color,
                        newColor: eightBitColor,
                        picker: this
                    }
                })
                this.color = eightBitColor
                elements.selectedColor.style.background = twentyFourBitColor
                $('.title_bar_bg').css('background-color',twentyFourBitColor);
                this.el.dispatchEvent(event)
            }
            // elements.eightBitText.value = eightBitColor
            elements.hexText.value = twentyFourBitColor
            elements.previewColor.style.background = twentyFourBitColor
        }

        /**
         * Restores preview color representations to match the value of this.color
         */
        pickerProto.restoreColor = function() {
            var elements = this.loadSelectors()
            elements.previewColor.style.background = this.getHexColor()
            elements.hexText.value = this.getHexColor()
            // elements.eightBitText.value = this.get8BitColor()
        }

        /**
         * Updates picker to have a new palette & refreshes color displays
         *
         * @returns {Boolean} Whether or not the update operation was successful
         */
        pickerProto.updatePalette = function(palette) {
            if (!EightBitColorPicker.isValidPalette(palette)) {
                return false
            }

            // Temporarily set color to undefined to prevent a noop from updateColor
            var color = this.color
            this.color = undefined

            // Update the palette to the new value, rebuild the display, & trigger a
            // color update
            this.palette = palette
            buildPalette.call(this)
            this.updateColor(color)

            return true
        }

        /**
         * Loads and caches selectors
         */
        pickerProto.loadSelectors = function() {
            if (this.selectors) {
                return this.selectors
            }
            this.selectors = {
                selectionUI: this.el.querySelector('.ebcp-selector'),
                palette: this.el.querySelector('.ebcp-palette'),
                selectedColor: this.el.querySelector('.ebcp-selection'),
                // eightBitText: this.el.querySelector('.ebcp-8bit-color'),
                hexText: this.el.querySelector('.ebcp-hex-color'),
                previewColor: this.el.querySelector('.ebcp-color-preview')
            }
            return this.selectors
        }

        /**
         * Builds DOM elements to display the color palette
         */
        var buildPalette = function() {
            // Cache selectors
            var elements = this.loadSelectors()

            // Variables used for generating color map
            var fragment = document.createDocumentFragment(),
                row = document.createElement('div'),
                rowSize = 0

            // Generation of color map
            this.palette.forEach(function(twentyFourBitColor, eightBitColor) {
                var colorEl = document.createElement('div')
                colorEl.dataset.eightBitColor = eightBitColor
                colorEl.style.background = twentyFourBitColor
                row.appendChild(colorEl)
                rowSize += 1
                if (rowSize % 16 === 0) {
                    row.classList.add('ebcp-palette-row')
                    fragment.appendChild(row)
                    row = document.createElement('div')
                }
            })

            // Clear innerHTML of palette and append new fragment
            elements.palette.innerHTML = ''
            elements.palette.appendChild(fragment)
        }

        /**
         * Builds color map in the DOM and attach event listeners
         */
        var buildColorPickerUI = function() {
            // Maintain reference to this
            var picker = this

            // Cache selectors
            var elements = this.loadSelectors()

            // Build the palette
            buildPalette.call(this)

            // Hover handling for color preview
            elements.palette.addEventListener('mouseover', function(e) {
                var eightBitColor = e.target.dataset.eightBitColor
                picker.updateColor(eightBitColor, true)
            })

            // Restore preview to selected color when cursor leaves the color map
            elements.palette.addEventListener('mouseleave', picker.restoreColor.bind(picker))

            // Click handling for color selection
            elements.palette.addEventListener('click', function(e) {
                picker.updateColor(e.target.dataset.eightBitColor)
            })

            // Update color when text input is edited
            // elements.eightBitText.addEventListener('keyup', function(e) {
            //     if (e.keyCode >= 33 && e.keyCode <= 40) {
            //         // Allow navigation keys
            //         return true
            //     }
            //     picker.updateColor(this.value)
            // })

            // Restore & normalize color when leaving focus of text input
            // elements.eightBitText.addEventListener('blur', picker.restoreColor.bind(picker))
        }

        /**
         * Returns a clone of the default color palette
         *
         * @returns {String[]}
         */
        EightBitColorPicker.getDefaultPalette = function() {
            return defaultPalette.slice(0)
        }

        /**
         * Function which checks if a given palette is valid
         *
         * @param {String[]} palette
         * @returns {Boolean}
         */
        EightBitColorPicker.isValidPalette = function(palette) {
            var colorCheck = RegExp.prototype.test.bind(/^#[a-f0-9]{6}$/)
            return Array.isArray(palette) && palette.length === 256 &&
                palette.map(colorCheck).reduce(function(a, b) {
                    return a && b
                }, true)
        }

        /**
         * Function to automatically detect elements with the color picker's class
         * name and instantiate them. The default color can be customized via a
         * "data-color" attribute.
         *
         * @returns {DOMElement[]}
         */
        EightBitColorPicker.detect = function() {
            var elements = document.getElementsByClassName(className)
            return Array.prototype.map.call(elements, function(el) {
                return new EightBitColorPicker({
                    el: el
                })
            })
        }

        /**
         * Validates that the picker object is instantiated correctly
         *
         * @throws Error
         */
        pickerProto.validate = function() {
            var err
            if (!this.el) {
                err = 'Element for color picker not found'
            }
            if (!isColorInRange(this.color)) {
                err = 'Color outside the range of 0-255'
            }
            if (!this.palette || this.palette.length !== 256) {
                err = 'Invalid color map set'
            }
            if (err) {
                throw new Error(err)
            }
        }

        /**
         * Displays the color picker selection view
         */
        pickerProto.show = function() {
            var selectionUI = this.loadSelectors().selectionUI,
                leftOffset = this.el.offsetLeft,
                topOffset = this.el.offsetTop

            selectionUI.style.left = leftOffset + 40 + 'px'
            selectionUI.style.top = topOffset + 40 + 'px'
            selectionUI.classList.add('ebcp-shown-selector')
        }

        /**
         * Hides the color picker selection view
         */
        pickerProto.hide = function() {
            var selectionUI = this.loadSelectors().selectionUI
            selectionUI.classList.remove('ebcp-shown-selector')
        }

        /**
         * Returns the element in which the picker was rendered
         *
         * @returns {DOMElement}
         */
        pickerProto.getElement = function() {
            return this.el
        }

        /**
         * Returns the current color as an integer between 0 and 255
         *
         * @returns {Number}
         */
        pickerProto.get8BitColor = function() {
            return this.color
        }

        /**
         * Returns the current color in hex format with a leading "#"
         *
         * @returns {String}
         */
        pickerProto.getHexColor = function() {
            return this.palette[this.color]
        }

        /**
         * Returns the current color as an object with keys "r", "g", and "b". Values
         * are integers from 0 to 255.
         *
         * @returns {Object}
         */
        pickerProto.getRGBColor = function() {
            var hex = this.getHexColor()
            return {
                r: parseInt(hex.slice(1, 3), 16),
                g: parseInt(hex.slice(3, 5), 16),
                b: parseInt(hex.slice(5, 7), 16)
            }
        }

        /**
         * Returns the terminal escape code sequence to use the current color as a
         * foreground color.
         *
         * @returns {String}
         */
        pickerProto.getForegroundEscapeSequence = function() {
            return '\\x1b[38;5;' + this.get8BitColor() + 'm'
        }

        /**
         * Returns the terminal escape code sequence to use the current color as a
         * background color.
         *
         * @returns {String}
         */
        pickerProto.getBackgroundEscapeSequence = function() {
            return '\\x1b[48;5;' + this.get8BitColor() + 'm'
        }

        /**
         * Expose constructor function as either AMD or global module
         */
        return EightBitColorPicker
    }));

    var el = document.getElementById('color-picker');
    var ebcp = new EightBitColorPicker({
        el: el
    });
    $('.ebcp-selection').click(false);

$(document).on('click','#icr_title_preview',function(){
    $('.append_icr_preview').css('display','block');
});


function image_preview(files)
{
    icr_title_image_validation(files);
    if (files && files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('.icr_title_preview_image').attr('src', e.target.result).css('display','block');
        }
        reader.readAsDataURL(files[0]); // convert to base64 string
    }
}

$("#fileInput").change(function(){
    image_preview(this.files);
});

function icr_title_image_validation(file)
{
    $('.error_icr_image').css('display','none');
    if (file && file[0]) {
        var fileType = file[0]["type"];
        var validImageTypes = ["image/jpg","image/png","image/jpeg", "image/gif", "image/svg"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $('.error_icr_image').css('display','block');
            $("#fileInput").val('');
        }

    }
}

$('#checkbox_cross_icon').change(function(){
    $('input[name="icr_toggle"]').val(1);
    if($(this).prop('checked') == false) {
        $('input[name="icr_toggle"]').val(0);
        fileUpload();
        $('#fileInput').prop('disabled',false);
        $('#dropArea').css('background-color', '#fff');
        $('.color_picker_div').prop('disabled',false);
        $('#icr_title_preview').attr('disabled',false);
        // $('.icr_title_preview_image').css('display','block');
    } else {
        fileUpload();
        $('#dropArea label').text('Choose a file or drag it here');
        $('#dropArea').css('background-color', '#eee');
        $('#dropArea').off('drop', true);
        $('#fileInput').prop('disabled',true);
        $('#icr_title_preview').attr('disabled',true);
        $('.color_picker_div').attr('disabled',true);
        $('.append_icr_preview').css('display','none');
    }
});

</script>

@stop
