<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
{{--  <link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>--}}
  <title>Kika | @yield('title')</title>

  @stack('before-styles')
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/bootstrap/dist/css/bootstrap.min.css')}}">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/font-awesome/css/font-awesome.min.css')}}">

  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/Ionicons/css/ionicons.min.css')}}">

  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('backend/assets/dist/css/AdminLTE.min.css')}}">

  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{asset('backend/assets/dist/css/skins/_all-skins.min.css')}}">

  <!-- Morris chart -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/morris.js/morris.css')}}">

  <!-- jvectormap -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/jvectormap/jquery-jvectormap.css')}}">

  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('backend/assets/plugins/select2/select2.min.css')}}">

  <!-- Date Picker -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')}}">

  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('backend/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css')}}">

  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="{{asset('backend/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css')}}">

   <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('backend/assets/plugins/select2/select2.min.css')}}">

  <link rel="stylesheet" href="{{asset('css/backend.css')}}">
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
      .error{
        color:red;
        position: absolute;
      }
    </style>
    @if (trim($__env->yieldContent('page-style')))
        @yield('page-style')
    @endif
    @stack('after-styles')

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  @include('theme.company-admin.common.header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('theme.company-admin.common.sidebar')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    @yield('content')
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <!-- <b>Version</b> 2.4.18 -->
    </div>
    <strong>Copyright &copy; 2019-<?php echo  date('Y'); ?> <a href="javascript:void(0);">Kika</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>
@stack('before-scripts')
<!-- jQuery 3 -->
<script src="{{asset('backend/assets/bower_components/jquery/dist/jquery.min.js')}}"></script>

<!-- jQuery UI 1.11.4 -->
<script src="{{asset('backend/assets/bower_components/jquery-ui/jquery-ui.min.js')}}"></script>

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- Bootstrap 3.3.7 -->
<script src="{{asset('backend/assets/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>

<!-- Morris.js charts -->
<script src="{{asset('backend/assets/bower_components/raphael/raphael.min.js')}}"></script>
<script src="{{asset('backend/assets/bower_components/morris.js/morris.min.js')}}"></script>

<!-- Sparkline -->
<script src="{{asset('backend/assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')}}"></script>

<!-- jvectormap -->
<script src="{{asset('backend/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('backend/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>

<!-- jQuery Knob Chart -->
<script src="{{asset('backend/assets/bower_components/jquery-knob/dist/jquery.knob.min.js')}}"></script>

<!-- daterangepicker -->
<script src="{{asset('backend/assets/bower_components/moment/min/moment.min.js')}}"></script>
<script src="{{asset('backend/assets/bower_components/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

<!-- select2 -->
<script src="{{asset('backend/assets/plugins/select2/select2.full.min.js')}}"></script>

<!-- datepicker -->
<script src="{{asset('backend/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')}}"></script>

<!-- Bootstrap WYSIHTML5 -->
<script src="{{asset('backend/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>

<!-- Slimscroll -->
<script src="{{asset('backend/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>

<!-- FastClick -->
<script src="{{asset('backend/assets/bower_components/fastclick/lib/fastclick.js')}}"></script>

<!-- AdminLTE App -->
<script src="{{asset('backend/assets/dist/js/adminlte.min.js')}}"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{--<script src="{{asset('backend/assets/dist/js/pages/dashboard.js')}}"></script>--}}

<!-- AdminLTE for demo purposes -->
<script src="{{asset('backend/assets/dist/js/demo.js')}}"></script>

@stack('after-scripts')
  @if (trim($__env->yieldContent('page-script')))
      @yield('page-script')
  @endif
</body>
</html>
