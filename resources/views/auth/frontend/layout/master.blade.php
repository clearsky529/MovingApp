<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
	<title>Kika</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="{{asset('/frontend/assets/css/fontawesome.min.css')}}" rel="stylesheet" >
	<link href="{{asset('/frontend/assets/css/bootstrap.min.css')}}" rel="stylesheet">
	<link href="{{asset('/frontend/assets/css/all.min.css')}}" rel="stylesheet">
	<link href="{{asset('/frontend/assets/css/style.css')}}" rel="stylesheet">
</head>
<body>
  @include('auth.frontend.layout.header')
    @yield('content')
  @if($slug->slug == "pricing")
  @include('auth.frontend.layout.footerPricing')
  @else
  @include('auth.frontend.layout.footer')
  @endif
  
