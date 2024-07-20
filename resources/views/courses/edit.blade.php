@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Edit Course</h1>
    <form action="{{ route('courses.update', $course) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" id="course_code" name="course_code" class="form-control" value="{{ $course->course_code }}" required>
        </div>
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name</label>
            <input type="text" id="course_name" name="course_name" class="form-control" value="{{ $course->course_name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection