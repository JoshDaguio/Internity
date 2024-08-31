@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Course Details</h1>
    <p><strong>Course Code:</strong> {{ $course->course_code }}</p>
    <p><strong>Course Name:</strong> {{ $course->course_name }}</p>
    <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning me-2">Edit</a>
    <form action="{{ route('courses.destroy', $course) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
    <a href="{{ route('courses.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
