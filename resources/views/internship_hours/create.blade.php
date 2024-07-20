@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Create Internship Hours</h1>
    <form action="{{ route('internship_hours.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select id="course_id" name="course_id" class="form-select">
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="hours" class="form-label">Hours</label>
            <input type="number" id="hours" name="hours" class="form-control" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection