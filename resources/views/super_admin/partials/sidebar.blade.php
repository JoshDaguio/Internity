@php
@endphp
<!-- ======= Sidebar ======= -->

<ul class="sidebar-nav" id="sidebar-nav">

  <li class="nav-item">
    <a class="nav-link {{ request()->is('admin/dashboard') ? '' : 'collapsed' }}" href="{{ url('super_admin/dashboard') }}">
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
    <a class="nav-link {{ request()->routeIs('admin-accounts.index', 'admin-accounts.create', 'admin-accounts.edit') ? '' : 'collapsed' }}" data-bs-target="#admin-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Admin</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="admin-nav" class="nav-content {{ request()->routeIs('admin-accounts.index', 'admin-accounts.create', 'admin-accounts.edit') ? '' : 'collapse' }}" data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('admin-accounts.index') }}" class="{{ Request::routeIs('admin-accounts.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>View Admins</span>
        </a>
      </li>
      <li>
        <a href="{{ route('admin-accounts.create') }}" class="{{ Request::routeIs('admin-accounts.create') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Add Admin</span>
        </a>
      </li>
    </ul>
  </li><!-- End Admin Nav -->


  <li class="nav-heading">User Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('registrations.pending', 'students.list') ? '' : 'collapsed' }}" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="student-nav" class="nav-content {{ request()->routeIs('registrations.pending', 'students.list') ? '' : 'collapse' }} " data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('registrations.pending') }}" class="{{ Request::routeIs('registrations.pending') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Student Registration</span>
        </a>
      </li>
      <li>
        <a href="{{ route('students.list') }}" class="{{ Request::routeIs('students.list') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Student List</span>
        </a>
      </li>
    </ul>
  </li><!-- End Student Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('company.index', 'company.create', 'company.show', 'company.edit') ? '' : 'collapsed' }}" href="{{ route('company.index') }}">
      <i class="bi bi-person"></i>
      <span>Company</span>  
    </a>
  </li><!-- End Company Page Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('faculty.index', 'faculty.create', 'faculty.show', 'faculty.edit') ? '' : 'collapsed' }}" href="{{ route('faculty.index') }}">
      <i class="bi bi-person"></i>
      <span>Faculty</span>  
    </a>
  </li><!-- End Faculty Page Nav -->


  <li class="nav-heading">Course Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('courses.index', 'courses.create', 'courses.show', 'courses.edit') ? '' : 'collapsed' }}" href="{{ route('courses.index') }}">
      <i class="bi bi-person"></i>
      <span>Courses</span>  
    </a>
  </li><!-- End Courses Nav -->

  <li class="nav-heading">Internship Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('internship_hours.index', 'internship_hours.create', 'internship_hours.edit') ? '' : 'collapsed' }}" data-bs-target="#hours-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-menu-button-wide"></i><span>Internship Hours</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="hours-nav" class="nav-content {{ request()->routeIs('internship_hours.index', 'internship_hours.create','internship_hours.edit') ? '' : 'collapse' }} " data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('internship_hours.index') }}" class="{{ Request::routeIs('internship_hours.index') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>View Internship Hours</span>
        </a>
      </li>
      <li>
        <a href="{{ route('internship_hours.create') }}" class="{{ Request::routeIs('internship_hours.create') ? 'active' : '' }}">
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
