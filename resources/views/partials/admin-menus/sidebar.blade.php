<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
      <div class="sidebar-brand">
        <a href="index.html"> <img alt="image" src="assets/img/logo.png" class="header-logo" /> <span
            class="logo-name">Otika</span>
        </a>
      </div>
      <ul class="sidebar-menu">
        <li class="menu-header">Main</li>
        <li class="dropdown">
          <a href="{{ route('admin.dashboard') }}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
        </li>
        <li class="dropdown">
            <a href="{{ route('admin.category') }}" class="nav-link"><i data-feather="monitor"></i><span>Categories</span></a>
        </li>
        <li class="dropdown">
            <a href="{{ route('admin.brand') }}" class="nav-link"><i data-feather="monitor"></i><span>Brand</span></a>
        </li>
        <li class="dropdown">
          <a href="{{ route('admin.product') }}" class="nav-link"><i data-feather="monitor"></i><span>Product</span></a>
        </li>
        <li class="dropdown">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                data-feather="briefcase"></i><span>Users</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ route('admin.users') }}">Customer</a></li>
              <li><a class="nav-link" href="{{ route('admin.guest') }}">Guest</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                data-feather="briefcase"></i><span>Orders</span></a>
            <ul class="dropdown-menu">
                <li><a class="nav-link" href="{{ route('admin.orderDetails') }}">Customer Orders</a></li>
              <li><a class="nav-link" href="{{ route('admin.guestOrderDetails') }}">Guest Orders</a></li>
            </ul>
          </li>
          <li class="dropdown">
              <a href="#" class="menu-toggle nav-link has-dropdown"><i
                data-feather="briefcase"></i><span>Receipts</span></a>
            <ul class="dropdown-menu">
              <li><a href="{{ route('admin.receipt') }}" class="nav-link">Receipts</a></li>
            </ul>
          </li>
          <li class="dropdown">
              <a href="{{ route('admin.report') }}" class="nav-link"><i data-feather="briefcase"></i><span>Reports</span></a>
          </li>
          <li class="dropdown">
          <a href="{{ route('viewExportPage') }}" class="nav-link"><i data-feather="monitor"></i><span>Export Product</span></a>
        </li>
          <li class="dropdown">
            <a href="{{ route('logout') }}" style="color:red" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link"><i data-feather="log-out"></i><span>{{ __('Logout') }}</span></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
          </li>
      </ul>
    </aside>
  </div>
