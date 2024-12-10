@php
  $requirements = Auth::user()->requirements;
  $step1Completed = $requirements ? $requirements->step1Completed() : false;
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
    <a class="nav-link {{ request()->routeIs('internship.files','internship.files.eod','internship.files.dtr') ? '' : 'collapsed' }}" href="{{ route('internship.files') }}">
      <i class="bi bi-file-earmark-zip"></i>
      <span>Internship Files</span>  
    </a>
  </li><!-- End Requirement Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('requirements.index') ? '' : 'collapsed' }}" href="{{ route('requirements.index') }}">
      <i class="bi bi-clipboard-check"></i>
      <span>Requirements</span>  
    </a>
  </li><!-- End Requirement Nav -->


  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('internship.listings', 'internship.applications') ? '' : 'collapsed' }}" data-bs-target="#internship-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-briefcase"></i><span>Internship</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="internship-nav" class="nav-content {{ request()->routeIs('internship.listings', 'internship.applications') ? 'show' : 'collapse' }}" data-bs-parent="#sidebar-nav">
        <li>
            <a href="{{ route('internship.listings') }}" class="{{ request()->routeIs('internship.listings') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Listings</span>
            </a>
        </li>
        <li>
            <a href="{{ route('internship.applications') }}" class="{{ request()->routeIs('internship.applications') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Applications</span>
            </a>
        </li>
    </ul>
</li><!-- End Internship Nav -->

@if($step1Completed)
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('dtr.index', 'dtr.reports') ? '' : 'collapsed' }}" href="{{ route('dtr.index') }}">
      <i class="bi bi-clock"></i>
      <span>Daily Time Record</span>  
    </a>
  </li><!-- End Dail Time Record Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('end_of_day_reports.create', 'end_of_day_reports.index','end_of_day_reports.compile.weekly', 'end_of_day_reports.compile.monthly', 'end_of_day_reports.show') ? '' : 'collapsed' }}" href="{{ route('end_of_day_reports.index') }}">
      <i class="bi bi-file-earmark-text"></i>
      <span>End of Day</span>  
    </a>
  </li><!-- End End Of Day Nav -->

  @endif

  <!-- <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('requests.studentIndex','requests.create','requests.studentShow') ? '' : 'collapsed' }}" href="{{ route('requests.studentIndex') }}">
      <i class="bi bi-envelope"></i>
      <span>Requests</span>  
    </a>
  </li> -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('requests.studentIndex','requests.create','requests.studentShow', 'log-requests.index', 'log-requests.submit', 'ot-requests.index', 'ot-requests.create') ? '' : 'collapsed' }}" data-bs-target="#request-nav" data-bs-toggle="collapse" href="#">
      <i class="bi bi-envelope"></i><span>Requests</span><i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <ul id="request-nav" class="nav-content {{ request()->routeIs('requests.studentIndex','requests.create','requests.studentShow', 'log-requests.index', 'log-requests.create', 'ot-requests.index', 'ot-requests.create') ? 'show' : 'collapse' }}" data-bs-parent="#sidebar-nav">
        <li>
            <a href="{{ route('requests.studentIndex') }}" class="{{ request()->routeIs('requests.studentIndex') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Absence Request</span>
            </a>
        </li>
        <li>
            <a href="{{ route('log-requests.index') }}" class="{{ request()->routeIs('log-requests.index', 'log-requests.create') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Log Request</span>
            </a>
        </li>
        <li>
            <a href="{{ route('ot-requests.index') }}" class="{{ request()->routeIs('ot-requests.index', 'ot-requests.create') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Overtime Request</span>
            </a>
        </li>
    </ul>
</li><!-- End Request Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('file_uploads.index') ? '' : 'collapsed' }}" href="{{ route('file_uploads.index') }}">
      <i class="bi bi-folder"></i>
      <span>Files</span>  
    </a>
  </li><!-- End File Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('evaluations.recipientIndex', 'evaluations.showResponseForm', 'evaluations.viewUserResponse') ? '' : 'collapsed' }}" href="{{ route('evaluations.recipientIndex') }}">        
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
