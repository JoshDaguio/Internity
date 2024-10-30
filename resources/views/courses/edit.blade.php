@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Edit Course</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Courses</li>
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
        <h5 class="card-title">Edit Course Form</h5>

        <form action="{{ route('courses.update', $course) }}" method="POST" class="row g-3">
            @csrf
            @method('PATCH')

            <!-- Course Code -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingCourseCode" name="course_code" value="{{ $course->course_code }}" placeholder="Course Code" required>
                    <label for="floatingCourseCode">Course Code</label>
                </div>
            </div>

            <!-- Course Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingCourseName" name="course_name" value="{{ $course->course_name }}" placeholder="Course Name" required>
                    <label for="floatingCourseName">Course Name</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
