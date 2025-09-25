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
	<style>
		.quick-col-part-2
		{
			position: relative;
		}
		.image_desktop
		{
			position: absolute;
			width: 20%;
			z-index: 1 !important;
		}
		@media only screen and (max-width: 992px) {
			.image_desktop{
				height: 175px !important;
				width: 100px !important;
    			bottom: -15px !important;
    			top: unset !important;
				left: -15px;
				transform: none;
			}
			.img-tablet
			{
				width: 100% !important;
			}
		}
		@media only screen and (min-width: 993px) {
			.image_desktop{
				width: 30% !important;
				height: auto !important;
				bottom: -20px;
    			left: -15px;
			}
		}
		@media only screen and (min-width: 1500px) {
			.image_desktop{
				width: 24% !important;
				height: auto !important;
				bottom: -20px;
    			left: 30px;
			}
		}
	</style>
</head>
<body>
<!-- Header Start -->
<nav class="navbar navbar-expand-lg navbar-light bg-white header-part">
	<div class="container-fluid">
		<a href="{{ url('/') }}"><img src="{{asset('/frontend/assets/images/LOGO/logo.png')}}" /></a>
	<!--<a class="navbar-brand" href="#">Kika</a>-->
  <button class="navbar-toggler shadow-none border-0 px-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
	</button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown">
        <?php
			$slugs_demo = App\Cms::where('status','=',1)->where('field_status','Product Demo')->get()->toArray();
			$id_key = array_search(19, array_column($slugs_demo, 'id'));
			$recordWithId = array_splice($slugs_demo, $id_key, 1);
			array_unshift($slugs_demo, $recordWithId[0]);
		?>
  		  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Product Demo</a>
        <ul class="dropdown-menu rounded-0 shadow-lg border-0 p-0" aria-labelledby="navbarDropdown">
          @foreach($slugs_demo as $key =>$slug)
		  	<li>
				<a class="dropdown-item" href="{{route('auth.frontend.slug',$slug['slug'])}}">{{$slug['title']}}</a>
			</li>
		  
          @endforeach
			  </ul>
			</li>
			<li class="nav-item">
				<?php $price = App\Cms::where('status','=',1)->where('field_status','Pricing')->first(); ?>
          		<!-- <a class="nav-link" href="{{route('auth.frontend.slug',$price->slug)}}">Pricing</a> -->
          		<a class="nav-link" href="#">Pricing</a>

            </li>
			<li class="nav-item dropdown">
       <?php $child_doc = App\Cms::where('status','=',1)->where('field_status','Docs & Guides')->get(); ?>
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Docs & Guides
  			</a>
				<ul class="dropdown-menu rounded-0 shadow-lg border-0 p-0" aria-labelledby="navbarDropdown">
          @foreach($child_doc as $key =>$doc)
            @if($doc->title == "Kika Tablet Application User Guide")
              <li><a class="dropdown-item" href="frontend/assets/pdf/KikaUserGuide-Application.pdf" target="_blank">{{$doc->title}}</a></li>
            @elseif($doc->title == "Kika Management System User Guide")
              <li><a class="dropdown-item" href="frontend/assets/pdf/KikaManagementSystemUserGuide.pdf" target="_blank">{{$doc->title}}</a></li>
            @else
              <li><a class="dropdown-item" href="{{route('auth.frontend.slug',$doc->slug)}}">{{$doc->title}}</a></li>
            @endif
          @endforeach
				</ul>
			</li>
			<li class="nav-item">
        <?php $child_doc = App\Cms::where('status','=',1)->where('field_status','About Us')->first(); ?>
  			<a class="nav-link" href="{{route('auth.frontend.slug',$child_doc->slug)}}">About Us</a>
	    </li>
	    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Contact
        </a>
        <ul class="dropdown-menu rounded-0 shadow-lg border-0 p-0" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="mailto:leticia@kikamoving.com">Email - Enquires</a></li>
          <li><a class="dropdown-item" href="mailto:support@kikamoving.com">Email - Support </a></li>
          <li><a class="dropdown-item" href="https://bit.ly/3HjG2ZR" target="_blank">Facebook Group</a></li>
          <li><a class="dropdown-item" href="https://bit.ly/3IhIZeH" target="_blank">LinkedIn Group</a></li>
        </ul>
	    </li>
		</ul>
		  <a class="btn rounded-pill p-2 header-start-trial shadow-none ms-0 ms-lg-4 mt-2 mt-lg-0" href="{{ url('admin/login') }}" type="button">Login </a>
			</form>
		</div>
	</div>
</nav>
<!-- Header End -->

<!-- Main Start -->
<div class="main-content">
<!--Quick easy inventories Section -->
<div class="container-fuid py-5 px-5 px-xs-2 quick-section">
	<div class="row align-items-center">
	    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 quick-col-part">
	      <h1 class="text-dark mb-4">Quick easy inventories</h1>
	      <p class="text-secondary mb-4">Use a solution that works with you, delivers more <br>productivity and saves you money</p>
	      <a class="btn rounded-pill p-2 quick-start-trial fw-bold mb-3 shadow-none" href="https://www.kikamoving.com/company-admin/register/step-1">Start One Month Free Trial</a>
	      <div class="quicks">
    	      <div class="quick-icons">
    	          <span class="icon-text-part"><i class="fa fa-check me-2"></i><span class="me-3 me-xs-5">Free Trial</span></span>
    	          <span class="icon-text-part"><i class="fa fa-check me-2"></i><span class="me-3 me-xs-5">No credit card required</span></span>
    	          <span class="icon-text-part"><i class="fa fa-check me-2"></i><span class="me-3">Cancel anytime</span></span>

    	      </div>
	      </div>
	    </div>
	    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 quick-col-part-2 text-md-center text-sm-center" style="position:relative;">
			<div class="row">
				{{-- <div class="col-xxl-1 col-xl-1 col-lg-1 col-md-12 col-sm-12 quick-col-part-2 text-md-center text-sm-center">
					<img src="{{asset('frontend/assets/images/mobile.png')}}" class="image_mobile" alt="" style="position:absolute;">
				</div> --}}
				<div class="col-xxl-11 col-xl-11 col-lg-11 col-md-12 col-sm-12 quick-col-part-2 text-md-center text-sm-center">
					<img src="{{asset('frontend/assets/images/slider-appmockup.png')}}" class="image_desktop" alt="" style="position:absolute;">
					<img src="{{asset('frontend/assets/images/tab2.png')}}" alt="" class="img-tablet">
				</div>
			</div>
	    </div>
	</div>
</div>
<!--Inventory Toolkit Section -->
<div class="container-fuid py-5 px-5 px-xs-2 main-toolkit">
	<div class="container pt-5 px-5 text-center main-toolkit-container">
		<div class="row">
		    <div class="col toolkit-col-part">
		      <h1 class="text-dark mb-5 mb-xs-4">Kika is the Complete Inventory Toolkit <br>for the Removal Industry</h1>
		      <p class="text-secondary mb-5">Kika is an end to end solution for everything in the inventory workflow with origin, destination and<br>
controlling agent management, paper inventory integration - and much more</p>
		    </div>
		    <div class="image-part">
			  <a href="https://apple.co/3377bRz"><img src="{{asset('/frontend/assets/images/New Project (2).png')}}" alt=""></a>
			  <img src="{{asset('/frontend/assets/images/New Project (3).png')}}" alt="">
			  <a href="https://bit.ly/3GAm3FK"><img src="{{asset('/frontend/assets/images/New Project (4).png')}}" alt=""></a>
			</div>
		</div>
	</div>
</div>

<!--Why Kika Section -->
<div class="container-fuid py-5 px-5 col-md-12 col-md-offset-5 why-kika" style="background-color: #e6f5ff;">
		<div class="col-12 text-center heading-text-part mb-5">
			<h1 style="font-weight: bold;">Why Kika ?</h1>
		</div>
		<div class="col-12 text-center img-cont-part">
			<div class="row">
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-bottom border-end border-secondary ">
			    	<img src="{{asset('/frontend/assets/images/AppStoreIcon.png')}}" alt="" >
            <img src="{{asset('/frontend/assets/images/GogglePlayIcon.png')}}" alt="" style="width: 80px;">
			    	<h4>Branded Apps</h4>
			    	<p>Van Lines, Move Associations, Relocation and Move Companies can get their own branded apps with custom forms powered by Kika.</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-bottom border-end border-secondary">
			      <img src="{{asset('/frontend/assets/images/graph.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Open To All</h4>
			    	<p>No membership of any removal association required. Choose the system that is open to all your agents and contractors</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-bottom border-end border-secondary">
			      <img src="{{asset('/frontend/assets/images/watch.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Save Time</h4>
			    	<p>Never scan or lose another inventory again. Cut up to 70% of taps needed to complete an inventory</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-bottom border-secondary">
			      <img src="{{asset('/frontend/assets/images/moniter.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Platform Agnostic</h4>
			    	<p>Works with your moving system because why change what’s working for you ?</p>
			    </div>
			</div>
			<div class="row">
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-end border-secondary">
			      <img src="{{asset('/frontend/assets/images/files.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Paper Invents</h4>
			    	<p>Inventory created on paper? Use Kika to manage the delivery workflow by entering the system ‘half way’</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-end border-secondary">
			      <img src="{{asset('/frontend/assets/images/frame.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Tablet Set Up</h4>
			    	<p>Road crews can set up jobs on the tablet meaning almost no admin required to implement Kika</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-end border-secondary">
			      <img src="{{asset('/frontend/assets/images/truck.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Kika Direct</h4>
			    	<p>Don’t use agents or need to screen/tranship ? No problem. Kika also serves companies that only uplift/deliver</p>
			    </div>
			    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 border-2 border-secondary">
			      <img src="{{asset('/frontend/assets/images/ship.png')}}" alt="" class="mb-3 mt-3">
			    	<h4>Screen/Tranship</h4>
			    	<p>Screen inventories into categories like quarantine and storage for accurate and faster transhipping documentation</p>
			    </div>
			</div>
		</div>
	</div>


<!--CONNECT WITH KIKA Section -->
<div class="container-fuid py-5 px-5 col-md-12 col-md-offset-5 inventory-section">
	<div class="row">
	    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12  inventory-col">
	      <p class="connect-kika">CONNECT WITH KIKA</p>
	      <h1 class="text-dark mb-4">Inventory pdf’s get emailed to all concerned agents</h1>
	      <p class="text-secondary mb-4 inventory-desc">Get the inventory pdf emailed instantly to you as soon as the job is finalised on the tablet</p>
	    </div>
	    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 text-center inventory-col-2">
	      <img src="{{asset('/frontend/assets/images/chart/Group 1.png')}}" alt="">
	    </div>
	</div>
</div>
<!--Experience the magic Section -->
<div class="container-fuid py-5 px-5 main-part">
	<div class="container py-5 px-5 text-center experience-section">
		<div class="row">
		    <div class="col">
		      <h1 class="text-light mb-4">Experience the magic of Kika for yourself</h1>
		      <p class="text-light mb-4">Sign up now and put our best inventory system guarantee to <br>the test. No obligation or lock in contracts.</p>
			  <a class="btn rounded-pill p-2 experience-start-trial fw-bold shadow-none" href="https://www.kikamoving.com/company-admin/register/step-1">Start One Month Free Trial</a>
		    </div>
		</div>
	</div>
</div>
</div>
<!-- Main End -->

<!-- Footer Start -->
<div class="footer">
	<div class="container">
		<div class="row py-5 footer-row">
		   	<div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-md-3 mb-sm-3 mb-3">
         <?php //$slugs_demo = App\Cms::where('status','=',1)->where('field_status','Product Demo')->get();?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Product Demo</h5>
		      	<ul class="ps-0">
              @foreach($slugs_demo as $key =>$slug)
			  	<li class="mb-0">
					<a href="{{route('auth.frontend.slug',$slug['slug'])}}">{{$slug['title']}}</a>
				</li>
                
              @endforeach
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-6 col-12 mb-3">
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Pricing</h5>
		      	<ul class="ps-0">
		      		<?php $price = App\Cms::where('status','=',1)->where('field_status','Pricing')->first(); ?>
			        <!-- <li class="mb-0"><a href="{{route('auth.frontend.slug',$price->slug)}}">Pricing</a></li> -->
			        <li class="mb-0"><a href="#">Pricing</a></li>

		      	</ul>
		    </div>
		    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-12 mb-md-3 mb-sm-3 mb-3">
        	<?php $child_doc = App\Cms::where('status','=',1)->where('field_status','Docs & Guides')->get(); ?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Docs & Guides</h5>
		      	<ul class="ps-0">
            	@foreach($child_doc as $key =>$doc)
            		@if($doc->title == "Kika Tablet Application User Guide")
              			<li><a class="mb-0" href="frontend/assets/pdf/KikaUserGuide-Application.pdf" target="_blank">{{$doc->title}}</a></li>
            		@elseif($doc->title == "Kika Management System User Guide")
              			<li><a class="mb-0" href="frontend/assets/pdf/KikaManagementSystemUserGuide.pdf">{{$doc->title}}</a></li>
            		@else
              			<li><a class="mb-0" href="{{route('auth.frontend.slug',$doc->slug)}}">{{$doc->title}}</a></li>
            		@endif
          		@endforeach
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-6 col-12 mb-3">
        <?php $aboutAs = App\Cms::where('status','=',1)->where('field_status','About Us')->first(); ?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">About Us</h5>
		      	<ul class="ps-0">
            <li class="mb-0"><a href="{{route('auth.frontend.slug',$aboutAs->slug)}}">Letter from Damien</a></li>
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-6 col-sm-6 col-12 mb-md-3 mb-sm-3">
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Contact</h5>
		      	<ul class="ps-0">
    		      	<li class="mb-0"><a href="mailto:leticia@kikamoving.com" >Email - Enquires</a></li>
                    <li class="mb-0"><a href="mailto:support@kikamoving.com" >Email - Support</a></li>
    		      	<li class="mb-0"><a href="https://bit.ly/3HjG2ZR" target="_blank">Facebook Group</a></li>
    		      	<li class="mb-0"><a href="https://bit.ly/3IhIZeH" target="_blank">LinkedIn Group</a></li>
    		    </ul>
		    </div>
	  	</div>
		<hr class="bg-danger border-2 border-top border-secondary">
		<div class="row">
			<div class="col px-3">
		      	<p class="text-secondary">Copyright 2022 Kika Moving Pty Ltd. All Rights Reserved</p>
		    </div>
	    </div>
	</div>
</div>
<!-- Footer End -->
<script src="{{asset('/frontend/assets/js/jquery.min.js')}}"></script>
<script src="{{asset('/frontend/assets/js/all.min.js')}}"></script>
<script src="{{asset('/frontend/assets/js/fontawesome.min.js')}}"></script>
<script src="{{asset('/frontend/assets/js/bootstrap.min.js')}}"></script>
</body>
</html>