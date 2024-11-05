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

  <li class="nav-heading">Internship Management</li>
  
  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('students.list', 'students.show', 'students.eod', 'students.dtr') ? '' : 'collapsed' }}" href="{{ route('students.list') }}">
      <i class="bi bi-people"></i>
      <span>Student</span>  
    </a>
  </li><!-- End Student Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('file_uploads.index', 'file_uploads.create', 'file_uploads.edit') ? '' : 'collapsed' }}" href="{{ route('file_uploads.index') }}">
      <i class="bi bi-folder"></i>
      <span>File Management</span>  
    </a>
  </li><!-- End File Management Nav -->

  <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('evaluations.recipientIndex', 'evaluations.showResponseForm', 'evaluations.viewUserResponse') ? '' : 'collapsed' }}" href="{{ route('evaluations.recipientIndex') }}">        
      <i class="bi bi-clipboard-data"></i>
      <span>Evaluations</span>  
    </a>
  </li><!-- End Evaluation Nav -->

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
