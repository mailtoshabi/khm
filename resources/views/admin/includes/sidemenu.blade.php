  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          @if(empty($auth_user['logo']))
            <img src="https://via.placeholder.com/100x100/666666/fff?text={{ substr($auth_user['name'],0,1) }}" class="img-circle" alt="{{ $auth_user['name'] }}">

          @else
            <img src="{{ asset($auth_user['logo']) }}" class="img-circle" alt="{{ $auth_user['name'] }}">
          @endif

        </div>
        <div class="pull-left info">
          <p>{{ $auth_user['name'] }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- search form -->
      {{--<form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>--}}
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        {{--<li class="treeview">
          <a href="{{ route('admin.index') }}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>--}}


        @foreach($menus as $menu)
          @include('admin.includes.side-menu-item', ['level' => 0, 'menu' => $menu])
        @endforeach


        {{--@ability('admin','')
        <li class="treeview">
          <a href="{{ route('admin.test') }}">
            <i class="fa fa-institution"></i> <span>Test</span>
          </a>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-cog"></i> <span>Settings</span>
          </a>
        </li>
        @endrole



        @ability('customer,admin','')
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Customers</span>
          </a>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-clone"></i> <span>Reports</span>
          </a>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-cog"></i> <span>Settings</span>
          </a>
        </li>
        @endrole--}}

      </ul>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    </section>
    <!-- /.sidebar -->
  </aside>
