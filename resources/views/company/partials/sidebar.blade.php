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


    <li class="nav-heading">Job Management</li>

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('jobs.index', 'jobs.create', 'jobs.edit', 'jobs.show') ? '' : 'collapsed' }}" href="{{ route('jobs.index') }}">
        <i class="bi bi-briefcase"></i>
        <span>Job Listings</span>  
      </a>
    </li><!-- End Job Listing Nav -->

    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('company.internApplications', 'company.jobApplications', 'company.interns') ? '' : 'collapsed' }}" data-bs-target="#interns-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-people"></i><span>Internships</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="interns-nav" class="nav-content {{ request()->routeIs('company.internApplications', 'company.jobApplications', 'company.interns') ? '' : 'collapse' }}" data-bs-parent="#sidebar-nav">
        <!-- Applications Link -->
        <li>
          <a href="{{ route('company.internApplications') }}" class="{{ Request::routeIs('company.internApplications', 'company.jobApplications') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Applications</span>
          </a>
        </li>
        <!-- Interns Placeholder -->
        <li>
            <a href="{{ route('company.interns') }}" class="{{ Request::routeIs('company.interns') ? 'active' : '' }}">
            <i class="bi bi-circle"></i><span>Interns</span> 
          </a>
        </li>
      </ul>
    </li><!-- End Intern Nav -->

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('pullouts.companyIndex') ? '' : 'collapsed' }}" href="{{ route('pullouts.companyIndex') }}">
            <i class="bi bi-envelope"></i>
            <span>Requests</span>  
        </a>
    </li><!-- End Request Nav -->


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
