@php
@endphp
<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link {{ request()->is('faculty/dashboard') ? '' : 'collapsed' }}" href="{{ url('faculty/dashboard') }}">
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


  <li class="nav-heading">Student Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="student-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Students</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Add Students</span>
        </a>
      </li>
    </ul>
  </li><!-- End Student Nav -->

<li class="nav-heading">Internship Management</li>

<li class="nav-item">
  <a class="nav-link collapsed" data-bs-target="#hours-nav" data-bs-toggle="collapse" href="#">
    <i class="bi bi-menu-button-wide"></i><span>Internship Hours</span><i class="bi bi-chevron-down ms-auto"></i>
  </a>
  <ul id="hours-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
    <li>
      <a href="#" >
        <i class="bi bi-circle"></i><span>View Internship Hours</span>
      </a>
    </li>
    <li>
      <a href="#">
        <i class="bi bi-circle"></i><span>Add Internship Hours</span>
      </a>
    </li>
  </ul>
</li><!-- End Internship Hours Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('penalties.index', 'penalties.create', 'penalties.edit', 'penalties.show') ? '' : 'collapsed' }}" data-bs-target="#violations-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Violation and Penalties</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="violations-nav" class="nav-content {{ request()->routeIs('penalties.index', 'penalties.create', 'penalties.edit', 'penalties.show') ? '' : 'collapse' }} " data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('penalties.index') }}" class="{{ Request::routeIs('penalties.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>View Violation</span>
        </a>
      </li>
      <li>
        <a href="{{ route('penalties.create') }}" class="{{ Request::routeIs('penalties.create') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Add Violations</span>
        </a>
      </li>
    </ul>
  </li><!-- End Violation and Penatlies Nav -->
  
  


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
