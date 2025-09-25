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
         <?php $price = App\Cms::where('status','=',1)->where('field_status','Pricing')->first();?>
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
