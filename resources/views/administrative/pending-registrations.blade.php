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
    
    <!-- Course Filter Section -->
    <form method="GET" action="{{ route('registrations.pending') }}" class="mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Filter by Course</h5>
                <div class="row">
                    <div class="d-flex">
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
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Pending Student Registrations</h5>

            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRegistrations as $registration)
                        <tr>
                            <td>{{ $registration->profile->id_number }}</td>
                            <td>{{ $registration->profile->last_name }}</td>
                            <td>{{ $registration->profile->first_name }}</td>
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
                            <td colspan="6">No pending registrations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
