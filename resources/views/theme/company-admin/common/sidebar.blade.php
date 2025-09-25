<aside class="main-sidebar">
  <?php
      if(Session::get('company-admin')){
        $users_info = DB::table('users')->join('companies','companies.tbl_users_id','=','users.id')->where('users.id',Session::get('company-admin'))->first();
        // dd($users_info);
      }
      else{
        $users_info = DB::table('users')->join('companies','companies.tbl_users_id','=','users.id')->where('users.id',Auth::user()->id)->first(); 
        // dd($users_info);
      } 
   ?>

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">

            <?php 
                 $s3_base_url = config('filesystems.disks.s3.url');
                 $s3_image_path = $s3_base_url.'userprofile/';
                  if($users_info->profile_pic != 'avatar.png' && !empty($users_info->profile_pic))
                  {
                  // $path = public_path('user_image/').$users_info->profile_pic;

                  // if(file_exists($path))
                  // {
                      $profile_img_path = $s3_image_path.$users_info->profile_pic;
                  // }
                  // else
                  // {
                  //     $profile_img_path = asset('backend/assets/dist/img/avatar.png');
                  // }
                  }
                  else
                  {
                      $profile_img_path = $s3_image_path.'avatar.png';
                  }
                  // $profile_img_path = 'https://kikaimages.s3.ap-southeast-2.amazonaws.com/'.$users_info->profile_pic;
              ?>
              
          <img src="{{$profile_img_path}}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ $users_info->name }}</p>
          <i class="fa fa-circle text-success"></i> Online
        </div>
      </div>

      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        
        <li class="{{ Request::segment(2) === 'home' ? 'active open' : null }}">
          <a href="{{ route('company-admin.home') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>

        <li class="{{ Request::segment(2) === 'move' ? 'active open' : null }}">
          <a href="{{ route('company-admin.move') }}">
            <i class="fa fa-briefcase"></i> <span>Manage Moves</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
        <!--start code by ss_24_aug -->
        @if($users_info->kika_direct == 1)
        <li class="{{ Request::segment(2) === 'agents' ? 'active open' : null }}" hidden="true">
          <a href="{{ route('company-admin.agents') }}">
            <i class="fa fa-user-secret"></i> <span>Manage Agents</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
        @else
        <li class="{{ Request::segment(2) === 'agents' ? 'active open' : null }}">
          <a href="{{ route('company-admin.agents') }}">
            <i class="fa fa-user-secret"></i> <span>Manage Agents</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
        @endif
        <!--end code by ss_24_aug -->
        <li class="{{ Request::segment(2) === 'user' ? 'active open' : null }}">
          <a href="{{ route('company-admin.user') }}">
            <i class="fa fa-users"></i> <span>Manage Devices</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
       
        <li class="{{ Request::segment(2) === 'archive-move' ? 'active open' : null }}">
          <a href="{{ route('company-admin.archive') }}">
            <i class="fa fa-archive"></i> <span>Archived Moves</span>
            <span class="pull-right-container">
              <!-- <small class="label pull-right bg-green">new</small> -->
            </span>
          </a>
        </li>
      </ul>

    </section>
    <!-- /.sidebar -->
  </aside>