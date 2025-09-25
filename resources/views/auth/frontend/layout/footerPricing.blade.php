<!-- Footer Start -->
<div class="footer">
    <div class="pricing-footer">
	    <div class="container">
		    <div class="row py-5 footer-row">
		   	<div class="col-xxl-3 col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 mb-md-3 mb-sm-3 mb-3">
				<?php
					$slugs_demo = App\Cms::where('status','=',1)->where('field_status','Product Demo')->get()->toArray();
					$id_key = array_search(19, array_column($slugs_demo, 'id'));
					$recordWithId = array_splice($slugs_demo, $id_key, 1);
					array_unshift($slugs_demo, $recordWithId[0]);
				?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Product Demo</h5>
		      	<ul class="ps-0">
					@foreach($slugs_demo as $key =>$slug)
						<li class="mb-0">
							<a href="{{route('auth.frontend.slug',$slug['slug'])}}">{{$slug['title']}}</a>
						</li>
					@endforeach
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
        		<?php $price = App\Cms::where('status','=',1)->where('field_status','Pricing')->first(); ?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Pricing</h5>
		      	<ul class="ps-0">
		      		<!-- <li class="mb-0"><a href="{{route('auth.frontend.slug',$price->slug)}}">Pricing</a></li> -->
		      		<li class="mb-0"><a href="#">Pricing</a></li>
		      	</ul>
		    </div>
		    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 mb-md-3 mb-sm-3 mb-3">
        <?php $child_doc = App\Cms::where('status','=',1)->where('field_status','Docs & Guides')->get(); ?>
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Docs & Guides</h5>
		      	<ul class="ps-0">
              	@foreach($child_doc as $key =>$doc)
		            @if($doc->title == "Kika Tablet Application User Guide")
		              <li><a class="mb-0" href="frontend/assets/pdf/KikaUserGuide-Application.pdf" target="_blank">{{$doc->title}}</a></li>
		            @elseif($doc->title == "Kika Management System User Guide")
		              <li><a class="mb-0" href="frontend/assets/pdf/KikaManagementSystemUserGuide.pdf" target="_blank">{{$doc->title}}</a></li>
		            @else
		              <li><a class="mb-0" href="{{route('auth.frontend.slug',$doc->slug)}}">{{$doc->title}}</a></li>
		            @endif
          		@endforeach
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-4 col-sm-6 col-12 mb-3">
          <h5 class="mb-2 mb-md-2 mb-sm-2">About Us</h5>
          <?php $aboutAs = App\Cms::where('status','=',1)->where('field_status','About Us footer')->first(); ?>
		      	<ul class="ps-0">
		      	    <li class="mb-0"><a href="{{route('auth.frontend.slug',$aboutAs->slug)}}">Letter from Damien</a></li>
		      	</ul>
		    </div>
		    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-4 col-sm-6 col-12 mb-md-3 mb-sm-3">
		      	<h5 class="mb-2 mb-md-2 mb-sm-2">Contact</h5>
		      	<ul class="ps-0">
                  	<li class="mb-0"><a href="mailto:leticia@kikamoving.com" >Email - Enquires</a></li>
                    <li class="mb-0"><a href="mailto:support@kikamoving.com" >Email - Support</a></li>
                  	<li class="mb-0"><a href="https://bit.ly/3HjG2ZR" target="_blank">Facebook Group</a></li>
                  	<li class="mb-0"><a href="https://bit.ly/3IhIZeH" target="_blank">LinkedIn Group</a></li>
                </ul>
		    </div>
	  	</div>
	  	</div>
	</div>
	<div class="container">
		<!--<hr class="bg-danger border-2 border-top border-secondary">-->
		<div class="row">
			<div class="col px-3 py-3 pricing-copyright-footer">
		      	<p class="text-secondary mb-0">Copyright 2022 Kika Moving Pty Ltd. All Rights Reserved</p>
		    </div>
	    </div>
	</div>
</div>
<!-- Footer End -->

<script src="{{asset('frontend/assets/js/jquery.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/popper.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/bootstrap.min.js')}}"></script> 
<script src="{{asset('frontend/assets/js/all.min.js')}}"></script>
<script src="{{asset('frontend/assets/js/custom.js')}}"></script>

</body>
</html>






































