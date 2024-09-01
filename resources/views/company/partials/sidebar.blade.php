  @php
  @endphp
  <!-- ======= Sidebar ======= -->

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link {{ request()->is('company/dashboard') ? '' : 'collapsed' }}" href="{{ url('company/dashboard') }}">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-heading">Profile Management</li>

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('profile.profile') ? '' : 'collapsed' }}" href="{{ route('profile.profile') }}">
        <i class="bi bi-person"></i>
        <span>Profile</span>  
      </a>
    </li><!-- End Profile Page Nav -->


    <li class="nav-heading">Intern Management</li>

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('jobs.index', 'jobs.create', 'jobs.edit', 'jobs.show') ? '' : 'collapsed' }}" href="{{ route('jobs.index') }}">
        <i class="bi bi-briefcase"></i>
        <span>Job Listings</span>  
      </a>
    </li><!-- End Job Listing Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#interns-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Interns</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="interns-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        <li>
          <a href="components-alerts.html">
            <i class="bi bi-circle"></i><span>Intern Applications</span>
          </a>
        </li>
        <li>
          <a href="components-alerts.html">
            <i class="bi bi-circle"></i><span>Interns</span>
          </a>
        </li>
      </ul>
    </li><!-- End Intern Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="#">
        <i class="bi bi-envelope"></i>
        <span>Requests</span>  
      </a>
    </li><!-- End Request Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="#">
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
