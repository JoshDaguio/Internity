@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Student Registration</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item active">Registration</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
    <!-- Course Filter Section -->
        <form method="GET" action="{{ route('registrations.pending') }}" class="mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter by Course</h5>
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
                </div>
            </div>
        </form>
    @endif

    <div class="row">
        <!-- Pending Students Table -->
        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <div class="col-lg-8 col-12">
        @else
        <div class="col-lg-12 col-12">
        @endif
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Student Registrations</h5>

                    <!-- Responsive Table Wrapper -->
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>ID Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->profile->id_number }}</td>
                                        <td>{{ $registration->profile->last_name }}, {{ $registration->profile->first_name }}</td>
                                        <td>{{ $registration->email }}</td>
                                        <td>{{ $registration->course->course_code }}</td>
                                        <td>
                                            <form action="{{ route('registrations.approve', $registration->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No pending registrations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- End Responsive Table Wrapper -->
                </div>
            </div>
        </div>

        <!-- Progress Bar for Pending Registrations by Course -->
        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Students by Course</h5>
                    <div class="progress-container" style="min-height: 98px; max-height: 250px; overflow-y: auto;">
                        @php
                            $totalPending = $allPendingRegistrations->count();
                        @endphp

                        @if ($totalPending > 0)
                            @foreach ($courses as $course)
                                @php
                                    // Calculate the number of pending students per course
                                    $coursePendingCount = $allPendingRegistrations->where('course_id', $course->id)->count();
                                    $percentage = ($coursePendingCount / $totalPending) * 100;
                                @endphp
                                @if ($coursePendingCount > 0)
                                    <p>{{ $course->course_code }} ({{ $coursePendingCount }} students)</p>
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #B30600;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ round($percentage, 2) }}%
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>No pending registrations available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
@endsection
