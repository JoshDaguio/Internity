@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Students</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item active">List</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Add Student Button -->
    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3 btn-sm">Add Student</a>

    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <a href="{{ route('students.import') }}" class="btn btn-success mb-3 ms-2 btn-sm">Import Students</a>
    @endif

    <div class="row">
        <!-- Filters and Progress Bar Row -->
        <div class="col-lg-6">
            <!-- Filters Section -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Filter</h5>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <div class="filter-container" style="min-height: 163px; max-height: 163px; overflow-y: auto;">
                    @else
                    <div class="filter-container" style="min-height: 72px; max-height: 72px;">
                    @endif
                        <form method="GET" action="{{ route('students.list') }}">

                            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            <div class="mb-3">
                                <select name="course_id" id="course_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="internship_status" id="internship_status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Internship Status</option>
                                    <option value="ongoing" {{ request('internship_status') == 'ongoing' ? 'selected' : '' }}>Ongoing Internship</option>
                                    <option value="no_internship" {{ request('internship_status') == 'no_internship' ? 'selected' : '' }}>No Internship</option>
                                    <option value="complete" {{ request('internship_status') == 'complete' ? 'selected' : '' }}>Internship Hours Complete</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="requirements_status" id="requirements_status" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Requirements</option>
                                    <option value="complete" {{ request('requirements_status') == 'complete' ? 'selected' : '' }}>Complete</option>
                                    <option value="incomplete" {{ request('requirements_status') == 'incomplete' ? 'selected' : '' }}>To Review</option>
                                    <option value="no_submission" {{ request('requirements_status') == 'no_submission' ? 'selected' : '' }}>No Submission</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="status_id" id="status_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @else
                            <div class="mb-3">
                                <select name="status_id" id="status_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar for Approved Students by Course -->
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Approved Students by Course</h5>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <div class="filter-container" style="min-height: 163px; max-height: 163px; overflow-y: auto;">
                    @else
                    <div class="filter-container" style="min-height: 72px; max-height: 72px;">
                    @endif
                        @php
                            $totalApproved = $approvedStudents->count();
                        @endphp

                        @if ($totalApproved > 0)
                            @foreach ($courses as $course)
                                @php
                                    // Calculate the number of approved students per course
                                    $courseApprovedCount = $approvedStudents->where('course_id', $course->id)->count();
                                    $percentage = ($courseApprovedCount / $totalApproved) * 100;
                                @endphp
                                @if ($courseApprovedCount > 0)
                                    <p>{{ $course->course_code }} ({{ $courseApprovedCount }})</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #B30600;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ round($percentage, 2) }}%
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>No approved students available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Students Table -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Approved Student List</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Course</th>
                                    <th>Progress</th>
                                    <th>Evaluation</th>
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                    <th>Requirements</th>
                                    @endif
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvedStudents as $student)
                                    <tr>
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                                {{ $student->profile->last_name }}, {{ $student->profile->first_name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $student->course ? $student->course->course_code : 'N/A' }}
                                        </td>
                                        <td>
                                            @if ($student->hasInternship)
                                                <a href="{{ route('students.dtr', $student->id) }}" class="btn btn-light btn-sm">
                                                    @if($student->remainingHours > 0)
                                                        <p><strong><i class="bi bi-hourglass-split"></i> Total Hours:</strong> {{ $student->totalWorkedHours }} / {{ $student->remainingHours }} hrs <strong>({{ round($student->completionPercentage, 2) }}%)</strong></p>
                                                    @else
                                                        <p><span class="badge bg-success"><strong><i class="bi bi-check-circle"></i> Internship Hours Completed</strong></span></p>
                                                    @endif                                                
                                                <div class="progress mb-3" style="height: 15px; width: 250px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                        style="width: {{ $student->completionPercentage }}%; background-color: #B30600;" 
                                                        aria-valuenow="{{ $student->completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ round($student->completionPercentage, 2) }}%
                                                    </div>
                                                </div>
                                                </a>
                                            @else
                                                <span class="badge bg-warning">No Internship Yet</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($student->evaluationStatus == 'No Internship')
                                                <span class="badge bg-secondary">{{ $student->evaluationStatus }}</span>
                                            @elseif ($student->evaluationStatus == 'Internship Ongoing')
                                                <span class="badge bg-warning">{{ $student->evaluationStatus }}</span>
                                            @elseif ($student->evaluationStatus == 'Sent' && $evaluation)
                                                <a href="{{ route('evaluations.internCompanyRecipientList', $evaluation->id) }}" class="btn btn-light btn-sm">
                                                    <span class="badge bg-primary">{{ $student->evaluationStatus }}</span>
                                                </a>
                                            @elseif ($student->evaluationStatus == 'Not Sent' && $evaluation)
                                                <a href="{{ route('evaluations.internCompanyRecipientList', $evaluation->id) }}" class="btn btn-light btn-sm">
                                                    <span class="badge bg-danger">{{ $student->evaluationStatus }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('evaluations.create') }}" class="btn btn-light btn-sm">
                                                    <span class="badge bg-secondary">Evaluation Not Available</span>
                                                </a>
                                            @endif
                                        </td>
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                        <td>
                                            @if($student->requirements)
                                                <a href="{{ route('requirements.review', $student->requirements->id) }}" class="btn btn-light btn-sm">
                                                    @if($student->requirements->status_id == 2) <!-- Accepted -->
                                                        <span class="badge bg-success">Complete</span>
                                                    @else
                                                        <span class="badge bg-warning">To Complete</span>
                                                    @endif
                                                </a>
                                            @else
                                                <span class="badge bg-secondary">No Submission</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td>
                                            @if($student->status_id == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
        </div>
    </div>
@endsection
