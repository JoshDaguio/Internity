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
      <i class="bi bi-person-circle"></i>
      <span>{{ __('Profile') }}</span>  
    </a>
  </li><!-- End Profile Page Nav -->


  <li class="nav-heading">User Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('registrations.pending', 'students.list', 'students.edit', 'students.show', 'students.create', 'requirements.review', 'students.import', 'student.internship.files.view', 'admin.students.managePriority') ? '' : 'collapsed' }}" data-bs-target="#student-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-people"></i><span>Student</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="student-nav" class="nav-content {{ request()->routeIs('registrations.pending', 'students.list', 'students.edit', 'students.show', 'students.create', 'requirements.review', 'students.import', 'student.internship.files.view', 'admin.students.managePriority') ? '' : 'collapse' }} " data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('registrations.pending') }}" class="{{ Request::routeIs('registrations.pending') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Student Registration</span>
        </a>
      </li>
      <li>
        <a href="{{ route('students.list') }}" class="{{ Request::routeIs('students.list', 'students.edit', 'students.show', 'students.create', 'requirements.review', 'students.import', 'student.internship.files.view', 'admin.students.managePriority') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Student List</span>
        </a>
      </li>
    </ul>
  </li><!-- End Student Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('company.index', 'company.create', 'company.show', 'company.edit', 'company.import') ? '' : 'collapsed' }}" href="{{ route('company.index') }}">
      <i class="bi bi-building"></i>
      <span>Company</span>  
    </a>
  </li><!-- End Company Page Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('faculty.index', 'faculty.create', 'faculty.show', 'faculty.edit') ? '' : 'collapsed' }}" href="{{ route('faculty.index') }}">
      <i class="bi bi-person-badge"></i>
      <span>Faculty</span>  
    </a>
  </li><!-- End Faculty Page Nav -->
  
  <li class="nav-heading">Program Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('courses.index', 'courses.create', 'courses.show', 'courses.edit') ? '' : 'collapsed' }}" href="{{ route('courses.index') }}">
      <i class="bi bi-book"></i>
      <span>Programs</span>  
    </a>
  </li><!-- End Courses Nav -->

<li class="nav-heading">Internship Management</li>

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('internship_hours.index', 'internship_hours.create',  'internship_hours.edit') ? '' : 'collapsed' }}" href="{{ route('internship_hours.index') }}">
      <i class="bi bi-clock-history"></i>
      <span>Internship Hours</span>  
    </a>
  </li><!-- End Internship Hours Nav -->

  <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('admin.jobs.index', 'admin.jobs.create', 'admin.jobs.edit') ? '' : 'collapsed' }}" href="{{ route('admin.jobs.index') }}">
          <i class="bi bi-briefcase"></i>
          <span>Job Listings</span>  
      </a>
  </li><!-- End Job Listings Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('penalties.index', 'penalties.create', 'penalties.edit', 'penalties.show') ? '' : 'collapsed' }}" href="{{ route('penalties.index') }}">
      <i class="bi bi-exclamation-triangle"></i>
      <span>Violations and Penalties</span>  
    </a>
  </li><!-- End Violations and Penalties Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('file_uploads.index', 'file_uploads.create') ? '' : 'collapsed' }}" href="{{ route('file_uploads.index') }}">
      <i class="bi bi-folder"></i>
      <span>File Management</span>  
    </a>
  </li><!-- End File Management Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('skill_tags.index') ? '' : 'collapsed' }}" href="{{ route('skill_tags.index') }}">
      <i class="bi bi-tags"></i>
      <span>Skill Tags</span>  
    </a>
  </li><!-- End Skill Tags Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('pullouts.index', 'pullouts.create') ? '' : 'collapsed' }}" data-bs-target="#request-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-envelope"></i><span>Requests</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="request-nav" class="nav-content {{ request()->routeIs('pullouts.index', 'pullouts.create', 'requests.adminIndex', 'requests.show', 'requests.respond') ? '' : 'collapse' }} " data-bs-parent="#sidebar-nav">
      <li>
        <a href="{{ route('pullouts.index') }}" class="{{ Request::routeIs('pullouts.index', 'pullouts.create') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Leave Request</span>
        </a>
      </li>
      <li>
        <a href="{{ route('requests.adminIndex') }}" class="{{ Request::routeIs('requests.adminIndex', 'requests.show', 'requests.respond') ? 'active' : '' }}">
          <i class="bi bi-circle"></i><span>Student Request</span>
        </a>
      </li>
    </ul>
  </li><!-- End Requests Nav -->


  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('evaluations.index', 'evaluations.create', 'evaluations.results', 'evaluations.showResponseForm','evaluations.internCompanyRecipientList', 'evaluations.manageQuestions') ? '' : 'collapsed' }}" href="{{ route('evaluations.index') }}">
        <i class="bi bi-clipboard-data"></i>
        <span>Evaluations</span>  
    </a>
  </li><!-- End Evaluation Nav -->

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
