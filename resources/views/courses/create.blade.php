@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Create Program</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Programs</li>
            <li class="breadcrumb-item active">Create</li>
        </ol>
    </nav>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Create Program Form</h5>

        <form action="{{ route('courses.store') }}" method="POST" class="row g-3">
            @csrf

            <!-- Course Code -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingCourseCode" name="course_code" placeholder="Course Code" required>
                    <label for="floatingCourseCode">Program Code</label>
                </div>
            </div>

            <!-- Course Name -->
            <div class="col-md-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="floatingCourseName" name="course_name" placeholder="Course Name" required>
                    <label for="floatingCourseName">Program Name</label>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-sm">Create</button>
                <a href="{{ route('courses.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
