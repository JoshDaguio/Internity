@extends('layouts.app')
@section('body')
<div class="container">
    <h1>Create Course</h1>
    <form action="{{ route('courses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" id="course_code" name="course_code" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name</label>
            <input type="text" id="course_name" name="course_name" class="form-control" required>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('courses.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
