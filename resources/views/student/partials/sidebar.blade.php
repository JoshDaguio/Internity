@php
@endphp
<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link {{ request()->is('student/dashboard') ? '' : 'collapsed' }}" href="{{ url('student/dashboard') }}">
      <i class="bi bi-grid"></i>
      <span>Dashboard</span>
    </a>
  </li><!-- End Dashboard Nav -->

  <li class="nav-heading">Profile Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('profile.profile') ? '' : 'collapsed' }}" href="{{ route('profile.profile') }}">
      <i class="bi bi-person"></i>
      <span>{{ __('Profile') }}</span>  
    </a>
  </li><!-- End Profile Page Nav -->


  <li class="nav-heading">Internship Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#">
      <i class="bi bi-person"></i>
      <span>Requirements</span>  
    </a>
  </li><!-- End Requirement Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#internship-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Internship</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="internship-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
      <li>
        <a href="#">
          <i class="bi bi-circle"></i><span>Internship Listings</span>
        </a>
        <li>
        <a href="#">
          <i class="bi bi-circle"></i><span>Internship Application</span>
        </a>
      </li>
    </ul>
  </li><!-- End Internship Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#dtr-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Daily Time Record</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="dtr-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Logging</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Reports</span>
        </a>
      </li>
    </ul>
  </li><!-- End Daily Time Record Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#eod-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Reports</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="eod-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>End of Day Report</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Monthly Reports</span>
        </a>
      </li>
    </ul>
  </li><!-- End Report Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" href="#">
      <i class="bi bi-person"></i>
      <span>Requests</span>  
    </a>
  </li><!-- End Request Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" href="#">
      <i class="bi bi-person"></i>
      <span>Files</span>  
    </a>
  </li><!-- End File Nav -->

  <li class="nav-heading">Account Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>{{ __('Log Out') }}</span>
      <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
      </form>
    </a>
  </li><!-- End Log Out Page Nav -->
</ul>
