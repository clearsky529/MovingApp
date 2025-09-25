<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags -->
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" type="image/png" href="{{asset('image/logo/k-symbol.png')}}"/>
	<title>Kika Home</title>

	<link rel="stylesheet" href="{{asset('css/style.css')}}">
	<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
</head>
<style>
.signin_btn{
	float: right;
    margin-top: -115px;
    color: #000 !important;
    font-size: 17px;
    text-decoration: none;
}
@media (max-width: 1024px) {
    .signin_btn{
		margin-top: 0px !important;
	}
}
</style>
<body>

	<div class="container-fluid main-body">
		<div class="container">
		<a href="{{ url('admin/login') }}" class="signin_btn">Sign In</a>
			<div class="row">
				<div class="centered-div">
					<h1 class="title-heading">Never scan or lose another <br>inventory again.</h1>
					<p class="sub-content-heading">Using pen and paper for inventory and condition reports gets messy. The invents can<br>
						be hard to read and get lost easily. Kika lets your team create, distribute and track<br>
						everything to do with the inventory process in real time. Use a solution that works with<br>
						you and delivers more productivity.
					</p>
					<a href="{{ route('company-admin.register') }}"><button type="button" class="sign-up-btn">TRY FOR FREE</button></a>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid light-bg footer-div">
		<div class="container">
			<div class="row">
				<!-- <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
					<div class="text-center footer-col">
						<p>Call Us</p>
						<a href="tel:+61 423 666 840">+61 423 666 840</a>
					</div>
				</div> -->
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="text-center footer-col">
						<p>Email Address</p>
						<a href="mailto:info@kikamoving.com">info@kikamoving.com</a>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="text-center footer-col">
						<p>Address</p>
						<div>Brisbane, QLD Australia</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery 3 -->
	<script src="js/jquery.min.js"></script>
	<!-- Bootstrap 3.3.7 -->
	<script src="js/bootstrap.min.js"></script>

</body>
</html>