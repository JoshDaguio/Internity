@extends('layouts.app')

@section('body')
    <h1>Create Student Account</h1>

    <form method="POST" action="{{ route('students.store') }}">
        @csrf

        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" name="id_number" class="form-control" id="id_number" value="{{ old('id_number') }}" required>
        </div>

        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select name="course_id" class="form-select" id="course_id" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('students.list') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
@endsection
