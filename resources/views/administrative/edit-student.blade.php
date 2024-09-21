@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Edit Student Account</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Student</li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
        <h5 class="card-title">Student Edit Account Form</h5>

            <form action="{{ route('students.update', $student) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row g-3">
                    <!-- First Name -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $student->profile->first_name ?? '' }}" placeholder="First Name" required>
                            <label for="first_name">First Name</label>
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $student->profile->last_name ?? '' }}" placeholder="Last Name" required>
                            <label for="last_name">Last Name</label>
                        </div>
                    </div>

                    <!-- ID Number -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="id_number" name="id_number" value="{{ $student->profile->id_number ?? '' }}" placeholder="ID Number" required>
                            <label for="id_number">ID Number</label>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" value="{{ $student->email }}" placeholder="Email" required>
                            <label for="email">Email</label>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password (leave blank to keep current password)</label>
                        </div>
                    </div>

                    <!-- Course Selection -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <select name="course_id" id="course_id" class="form-select" required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $course->id == $student->course_id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="course_id">Course</label>
                        </div>
                    </div>
                </div>

                <!-- Submit and Cancel Buttons -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('students.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection
