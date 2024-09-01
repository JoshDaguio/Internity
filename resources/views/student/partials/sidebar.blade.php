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
      <i class="bi bi-person-circle"></i>
      <span>Profile</span>  
    </a>
  </li><!-- End Profile Page Nav -->

  <li class="nav-heading">Internship Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#">
      <i class="bi bi-clipboard-check"></i>
      <span>Requirements</span>  
    </a>
  </li><!-- End Requirement Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('internship.listings', 'internship.application') ? '' : 'collapsed' }}" data-bs-target="#internship-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-briefcase"></i><span>Internship</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="internship-nav" class="nav-content {{ request()->routeIs('internship.listings', 'internship.application') ? '' : 'collapse' }}" data-bs-parent="#sidebar-nav">
      <li>
        <a href="#" class="{{ Request::routeIs('internship.listings') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Internship Listings</span>
        </a>
      </li>
      <li>
        <a href="#" class="{{ Request::routeIs('internship.application') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Internship Application</span>
        </a>
      </li>
    </ul>
  </li><!-- End Internship Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('dtr.logging', 'dtr.reports') ? '' : 'collapsed' }}" data-bs-target="#dtr-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-clock"></i><span>Daily Time Record</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="dtr-nav" class="nav-content {{ request()->routeIs('dtr.logging', 'dtr.reports') ? '' : 'collapse' }}" data-bs-parent="#sidebar-nav">
      <li>
        <a href="#" class="{{ Request::routeIs('dtr.logging') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Logging</span>
        </a>
      </li>
      <li>
        <a href="#" class="{{ Request::routeIs('dtr.reports') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Reports</span>
        </a>
      </li>
    </ul>
  </li><!-- End Daily Time Record Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('end_of_day_reports.create', 'end_of_day_reports.index', 'end_of_day_reports.compile.monthly', 'end_of_day_reports.show') ? '' : 'collapsed' }}" href="{{ route('end_of_day_reports.index') }}">
      <i class="bi bi-file-earmark-text"></i>
      <span>Reports</span>  
    </a>
  </li><!-- End Reports Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" href="#">
      <i class="bi bi-file-earmark-text"></i>
      <span>Requests</span>  
    </a>
  </li><!-- End Request Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('file_uploads.index') ? '' : 'collapsed' }}" href="{{ route('file_uploads.index') }}">
      <i class="bi bi-folder"></i>
      <span>Files</span>  
    </a>
</li><!-- End File Nav -->

  <li class="nav-heading">Account Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
      <i class="bi bi-box-arrow-right"></i>
      <span>{{ __('Log Out') }}</span>
      <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
        @csrf
      </form>
    </a>
  </li><!-- End Log Out Page Nav -->
</ul>
