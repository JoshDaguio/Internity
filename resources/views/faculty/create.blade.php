@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Add New Faculty</h1>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Faculty Creation Form -->
    <form action="{{ route('faculty.store') }}" method="POST">
        @csrf
        <!-- First Name -->
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" required>
        </div>

        <!-- ID Number -->
        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" name="id_number" class="form-control" id="id_number" value="{{ old('id_number') }}" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
        </div>

        <!-- Course -->
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select name="course_id" id="course_id" class="form-select" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('faculty.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection