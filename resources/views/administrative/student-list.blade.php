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
    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3">Add Student</a>
    
    <div class="row">
        <!-- Filters and Progress Bar Row -->
        <div class="col-lg-6">
            <!-- Filters Section -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Filter by Course and Status</h5>
                    <form method="GET" action="{{ route('students.list') }}">
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
                            <select name="status_id" id="status_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="1" {{ request('status_id') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="2" {{ request('status_id') == '2' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Progress Bar for Approved Students by Course -->
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Approved Students by Course</h5>
                    <div class="progress-container" style="min-height: 107px; max-height: 107px; overflow-y: auto;">
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
                                    <th>ID Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvedStudents as $student)
                                    <tr>
                                        <td>{{ $student->profile ? $student->profile->id_number : 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                                {{ $student->profile->last_name}}, {{ $student->profile->first_name}}
                                            </a>
                                        </td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->course ? $student->course->course_code : 'N/A' }}</td>
                                        <td>{{ $student->status_id == 1 ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            @if($student->status_id == 1)
                                                @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2) <!-- Super Admin or Admin -->
                                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $student->id }}">
                                                        Deactivate
                                                    </button>

                                                    <!-- Deactivate Modal -->
                                                    <div class="modal fade" id="deactivateModal-{{ $student->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel-{{ $student->id }}" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deactivateModalLabel-{{ $student->id }}">Deactivate Student Account</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Are you sure you want to deactivate this student account?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <form action="{{ route('students.deactivate', $student->id) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-danger">Deactivate</button>
                                                                    </form>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                @if(auth()->user()->role_id == 1 || auth()->user()->role_id == 2) <!-- Super Admin or Admin -->
                                                    <form action="{{ route('students.reactivate', $student->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm">Reactivate</button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No students found.</td>
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
