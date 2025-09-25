<header class="main-header">

  <?php $users_info = DB::table('users')->where('id',Auth::user()->id)->first(); ?>
    <!-- Logo -->
    <a href="{{ route('support-admin.home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>Ki</b>ka</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Kika</b></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">

            <?php 
                $s3_base_url = config('filesystems.disks.s3.url');
                $s3_image_path = $s3_base_url.'userprofile/';
                  // if(isset($users_info->profile_pic) && !empty($users_info->profile_pic))
                  // {
                  // $path = public_path('user_image/').$users_info->profile_pic;

                  // if(file_exists($path))
                  // {
                  //     $profile_img_path = asset('user_image/').'/'.$users_info->profile_pic;
                  // }
                  // else
                  // {
                  //     $profile_img_path = asset('backend/assets/dist/img/avatar.png');
                  // }
                  // }
                  // else
                  // {
                  //     $profile_img_path = asset('backend/assets/dist/img/avatar.png');
                  // }
                if($users_info->profile_pic != 'avatar.png' && !empty($users_info->profile_pic))
                {
                  // $path = $users_info->profile_pic;

                  // if(file_exists($path))
                  // {
                      $profile_img_path = $s3_image_path.$users_info->profile_pic;
                  // }
                  // else
                  // {
                  //     $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/avatar.png';
                  // }
                }
                else
                {
                    $profile_img_path = $s3_image_path.'avatar.png';
                }
                  // $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/'.$users_info->profile_pic;
              ?>


            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{ $profile_img_path }}" class="user-image object-cover" alt="User Image">
              <span class="hidden-xs">{{ $users_info->username }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="{{ $profile_img_path }}" class="img-circle object-cover" alt="User Image">

                <p>
                  {{ $users_info->username }}
                </p>
              </li>
              <!-- Menu Body -->
  
              <!-- Menu Footer-->
              <li class="user-footer">
                    <a href="{{ route('support-admin.profile') }}" class="btn btn-default btn-flat">@lang('common.profile')</a>
                    <a href="{{ route('support-admin.changepassword') }}" class="btn btn-default btn-flat">@lang('common.change password')</a>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-default btn-flat">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
        </ul>
      </div>
    </nav>
  </header>



