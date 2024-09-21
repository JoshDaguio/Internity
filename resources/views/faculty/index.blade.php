@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Faculty</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Faculty</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('faculty.create') }}" class="btn btn-primary mb-3">Add Faculty</a>
    
    <div class="row">
        <!-- Filters and Progress Bar Row -->
        <div class="col-lg-6">
            <!-- Filters Section -->
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Filter by Course and Status</h5>
                    <form method="GET" action="{{ route('faculty.index') }}">
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

        <!-- Progress Bar for Faculty by Course -->
        <div class="col-lg-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Faculty by Course</h5>
                    <div class="progress-container" style="min-height: 107px; max-height: 107px; overflow-y: auto;">
                        @php
                            $totalFaculty = $faculties->count();
                        @endphp

                        @if ($totalFaculty > 0)
                            @foreach ($courses as $course)
                                @php
                                    // Calculate the number of faculty per course
                                    $courseFacultyCount = $faculties->where('course_id', $course->id)->count();
                                    $percentage = ($courseFacultyCount / $totalFaculty) * 100;
                                @endphp
                                @if ($courseFacultyCount > 0)
                                    <p>{{ $course->course_code }} ({{ $courseFacultyCount }} faculty)</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #B30600;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ round($percentage, 2) }}%
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>No faculty available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty List Table -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Faculty List</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>ID Number</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($faculties as $faculty)
                                    <tr>
                                        <td>
                                            @if($faculty->profile)
                                                {{ $faculty->profile->first_name }} {{ $faculty->profile->last_name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $faculty->email }}</td>
                                        <td>
                                            @if($faculty->profile)
                                                {{ $faculty->profile->id_number }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $faculty->course ? $faculty->course->course_code : 'N/A' }}</td>
                                        <td>
                                            {{ $faculty->status_id == 1 ? 'Active' : 'Inactive' }}
                                        </td>
                                        <td>
                                            @if($faculty->status_id == 1)
                                                <a href="{{ route('faculty.show', $faculty->id) }}" class="btn btn-info"><i class="bi bi-info-circle"></i></a>
                                                <a href="{{ route('faculty.edit', $faculty) }}" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal-{{ $faculty->id }}">
                                                    Deactivate
                                                </button>

                                                <!-- Deactivate Modal -->
                                                <div class="modal fade" id="deactivateModal-{{ $faculty->id }}" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deactivateModalLabel">Deactivate Faculty Account</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to deactivate this faculty account?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form action="{{ route('faculty.destroy', $faculty) }}" method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Deactivate</button>
                                                                </form>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <form action="{{ route('faculty.reactivate', $faculty) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success">Reactivate</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
        </div>
    </div>

@endsection
