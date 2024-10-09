@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Academic Year Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('academic-years.index') }}">S.Y. Configuration</a></li>
            <li class="breadcrumb-item active">Details</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

<div class="row">
    <!-- Academic Year Details Card -->
    <div class="col-md-6 mb-3">
        <div class="card" style="height: 100%;">
            <div class="card-body">
                <h5 class="card-title">Academic Year: {{ $academicYear->start_year }} - {{ $academicYear->end_year }}</h5>
                <p>Status: 
                    @if($academicYear->is_current)
                        <span class="badge bg-success">Current</span>
                    @else
                        <span class="badge bg-secondary">Non-Current</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Course Filter Section Card -->
    <div class="col-md-6 mb-3">
        <div class="card" style="height: 100%;">
            <div class="card-body">
                <h5 class="card-title">Filter by Course</h5>
                <form method="GET" action="{{ route('academic-years.show', $academicYear->id) }}" class="mb-0">
                    <div class="row">
                        <div class="col-12">
                            <select name="course_id" id="course_id" class="form-select" onchange="this.form.submit()">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Students Per Course Section -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Students Count Per Course</h5>
        <ul class="list-group">
            @foreach($studentsPerCourse as $courseGroup)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $courseGroup->course->course_name }}
                    <span class="badge bg-primary rounded-pill">{{ $courseGroup->total }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<!-- List of Students -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">List of Students</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->profile->id_number ?? 'N/A' }}</td>
                            <td>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->course->course_code ?? 'N/A' }}</td>
                            <td>
                                @if($student->status_id == 1)
                                    <span class="badge bg-success">Active</span>
                                @elseif($student->status_id == 2)
                                    <span class="badge bg-danger">Inactive</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
