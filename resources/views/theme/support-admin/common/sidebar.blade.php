<aside class="main-sidebar">
  <?php $users_info = DB::table('users')->where('id',Auth::user()->id)->first(); ?>
  
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">

            <?php 
                $s3_name = config('filesystems.disks.s3.url');
                $s3_path = $s3_name.'userprofile/';
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
                      $profile_img_path = $s3_path.$users_info->profile_pic;
                  }else{
                    $profile_img_path = $s3_path.'avatar.png';
                  }
                  // $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/'.$users_info->profile_pic;
              ?>
              
          <img src="{{$profile_img_path}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ $users_info->username }}</p>
          <i class="fa fa-circle text-success"></i> Online
        </div>
      </div>

      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        
        <li class="{{ Request::segment(2) === 'home' ? 'active open' : null }}">
          <a href="{{ route('support-admin.home') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
        <li class="{{ Request::segment(2) === 'companies' ? 'active open' : null }}">
          <a href="{{ route('support-admin.companies') }}">
            <i class="fa fa-building"></i> <span>Companies</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
        <!-- <li class="{{ Request::segment(2) === 'move' ? 'active open' : null }}">
          <a href="{{ route('support-admin.move') }}">
            <i class="fa fa-briefcase"></i> <span>Moves</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li> -->

        <!-- <li class="{{ Request::segment(2) === 'cms' ? 'active open' : null }}">
          <a href="{{ route('support-admin.cms') }}">
            <i class="fa fa-building"></i> <span>CMS</span>
            <span class="pull-right-container">
            </span>
          </a>
        </li> -->
       
      
      </ul>

    </section>
    <!-- /.sidebar -->
  </aside>