@php
@endphp
<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link {{ request()->is('admin/dashboard') ? '' : 'collapsed' }}" href="{{ url('admin/dashboard') }}">
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


  <li class="nav-heading">User Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#faculty-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Faculty</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="faculty-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Faculties</span>
        </a>
        <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Add Faculty</span>
        </a>
      </li>
    </ul>
  </li><!-- End Faculty Nav -->

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#company-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Company</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="company-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Companies</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Add Company</span>
        </a>
      </li>
    </ul>
  </li><!-- End Company Nav -->

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

  <li class="nav-heading">Course Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#course-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Courses</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="course-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Courses</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Add Course</span>
        </a>
      </li>
    </ul>
  </li><!-- End Course Nav -->

<li class="nav-heading">Violation Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#violations-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Violation and Penalties</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="violations-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Violation</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>Add Violations</span>
        </a>
      </li>
    </ul>
  </li><!-- End Violation and Penatlies Nav -->
  
  <li class="nav-heading">Violation Management</li>

  <li class="nav-item">
    <a class="nav-link collapsed" data-bs-target="#violations-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Violation and Penalties</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="violations-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
      <li>
        <a href="components-alerts.html">
          <i class="bi bi-circle"></i><span>View Violation</span>
        </a>
      </li>
      <li>
        <a href="components-alerts.html">
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
