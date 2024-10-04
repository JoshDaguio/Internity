@extends('layouts.app')

@section('body')

  <div class="pagetitle">
        <h1>Jobs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Job Listings</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Success and Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <a href="{{ route('jobs.create') }}" class="btn btn-primary mb-3">Add Job</a>

    <div class="row">
        <!-- Job Listings Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Jobs List</h5>
              <div class="table-responsive">
                <!-- Table with stripped rows, formatted for DataTables -->
                <table class="table datatable">
                  <thead>
                    <tr>
                      <th>Job Title</th>
                      <th>Industry</th>
                      <th>Available</th>
                      <th>Work Type</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                      <td>
                        <a href="{{ route('jobs.show',$job) }}" class="btn btn-light btn-sm">
                            {{ $job->title }}
                        </a>
                      </td>
                      <td>{{ $job->industry }}</td>
                      <td>
                          @if($job->positions_available > 0)
                              {{ $job->positions_available }}
                          @else
                              <span class="text-danger">No more available positions</span>
                          @endif
                      </td>
                      <td>{{ $job->work_type }}</td>
                      <td>
                        <!-- <a href="{{ route('jobs.show', $job) }}" class="btn btn-info btn-sm"><i class="bi bi-info-circle"></i></a> -->
                        <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <!-- <form action="{{ route('jobs.destroy', $job) }}" method="POST" style="display:inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form> -->
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
                <!-- End Table with stripped rows -->
              </div>
            </div>
          </div>
        </div>

        <!-- Active Internships Section -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Active Internships</h5>

              <!-- Job list container with fixed height and overflow -->
              <div class="job-list" style="min-height: 186px; max-height: 200px; overflow-y: auto;">
                @if($jobs->isEmpty())
                    <p>No active internships available at the moment.</p>
                @else
                    @foreach ($jobs as $job)
                        @php
                            // Calculate the number of accepted internships for this job
                            $acceptedInternships = \App\Models\AcceptedInternship::where('job_id', $job->id)->count();
                            // Calculate percentage of accepted internships for this job
                            $percentage = $totalAcceptedInternships > 0 ? ($acceptedInternships / $totalAcceptedInternships) * 100 : 0;
                        @endphp
                        <p>{{ $job->title }} ({{ $acceptedInternships }})</p>
                        <div class="progress mb-3">
                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #B30600;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ round($percentage, 2) }}%
                            </div>
                        </div>
                    @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Add DataTables Script -->
<script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script>
  // Initialize the DataTable
  document.addEventListener('DOMContentLoaded', function() {
      const datatable = new simpleDatatables.DataTable(".datatable");
  });
</script>
@endsection
